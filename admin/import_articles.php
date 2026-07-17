<?php
/**
 * import_articles.php
 * -------------------------------------------------------------
 * Parcourt toutes les sources actives de `sources_articles`,
 * télécharge chaque flux (RSS 2.0 ou Atom), et insère les
 * nouveaux articles dans la table `articles`.
 *
 * Peut être :
 *  - déclenché manuellement depuis l'admin (bouton "Actualiser")
 *  - appelé en ligne de commande : php import_articles.php
 *  - appelé par une tâche planifiée (cron) pour une mise à jour
 *    automatique régulière
 * -------------------------------------------------------------
 */

// Si lancé depuis le navigateur, on exige une session admin.
// Si lancé en CLI (cron), $_SERVER['REQUEST_METHOD'] n'existe pas.
$est_cli = (php_sapi_name() === 'cli');

if (!$est_cli) {
    require_once '../includes/admin_session.php';
} else {
    require_once __DIR__ . '/../includes/db_connect.php';
}

/* -------------------------------------------------------------
 * Récupère le contenu d'une URL avec cURL (plus fiable que
 * file_get_contents pour les flux RSS externes)
 * ----------------------------------------------------------- */
function telechargerFlux($url) {
    if (!function_exists('curl_init')) {
        $contexte = stream_context_create([
            'http' => [
                'timeout' => 15,
                'header' => "User-Agent: Mozilla/5.0 (compatible; UrgencesAntsirananaBot/1.0)\r\n"
            ]
        ]);
        return @file_get_contents($url, false, $contexte);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; UrgencesAntsirananaBot/1.0)',
    ]);
    $contenu = curl_exec($ch);
    $erreur = curl_error($ch);
    curl_close($ch);

    if ($contenu === false) {
        error_log("import_articles: échec téléchargement $url - $erreur");
        return false;
    }
    return $contenu;
}

/* -------------------------------------------------------------
 * Parse un flux RSS 2.0 ou Atom et retourne un tableau d'articles
 * normalisés : titre, lien, resume, date
 * ----------------------------------------------------------- */
function parserFlux($xmlBrut) {
    $articles = [];

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlBrut);
    libxml_clear_errors();

    if ($xml === false) {
        return $articles;
    }

    // --- Format RSS 2.0 : <rss><channel><item>...
    if (isset($xml->channel->item)) {
        foreach ($xml->channel->item as $item) {
            $titre = trim((string)$item->title);
            $lien = trim((string)$item->link);
            $resume = trim((string)($item->description ?? ''));
            $datePub = trim((string)($item->pubDate ?? ''));

            $timestamp = $datePub ? strtotime($datePub) : time();

            $articles[] = [
                'titre' => $titre,
                'lien' => $lien,
                'resume' => $resume,
                'date' => $timestamp ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s'),
            ];
        }
        return $articles;
    }

    // --- Format Atom : <feed><entry>...
    if (isset($xml->entry)) {
        foreach ($xml->entry as $entry) {
            $titre = trim((string)$entry->title);

            // Le lien Atom est un attribut href, pas un noeud texte
            $lien = '';
            if (isset($entry->link)) {
                foreach ($entry->link as $l) {
                    $attrs = $l->attributes();
                    if ((string)($attrs['rel'] ?? 'alternate') === 'alternate' || $lien === '') {
                        $lien = (string)$attrs['href'];
                    }
                }
            }

            $resume = trim((string)($entry->summary ?? $entry->content ?? ''));
            $dateBrute = trim((string)($entry->updated ?? $entry->published ?? ''));
            $timestamp = $dateBrute ? strtotime($dateBrute) : time();

            $articles[] = [
                'titre' => $titre,
                'lien' => $lien,
                'resume' => $resume,
                'date' => $timestamp ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s'),
            ];
        }
        return $articles;
    }

    return $articles;
}

/* -------------------------------------------------------------
 * Import principal
 * ----------------------------------------------------------- */
$stmt_sources = $pdo->query("SELECT * FROM sources_articles WHERE actif = 1");
$sources = $stmt_sources->fetchAll(PDO::FETCH_ASSOC);

$stmt_insertion = $pdo->prepare("
    INSERT IGNORE INTO articles (titre, contenu, lien_source, id_source, id_auteur, date_publication)
    VALUES (:titre, :contenu, :lien, :id_source, NULL, :date_publication)
");

$total_nouveaux = 0;
$total_ignores = 0;
$rapport = [];

foreach ($sources as $source) {
    $xmlBrut = telechargerFlux($source['url_flux']);

    if ($xmlBrut === false || $xmlBrut === '') {
        $rapport[] = "❌ {$source['nom_source']} : impossible de télécharger le flux.";
        continue;
    }

    $articles = parserFlux($xmlBrut);

    if (empty($articles)) {
        $rapport[] = "⚠️ {$source['nom_source']} : flux téléchargé mais aucun article détecté (format non reconnu ?).";
        continue;
    }

    $nouveaux_pour_cette_source = 0;

    foreach ($articles as $article) {
        if ($article['titre'] === '' || $article['lien'] === '') {
            continue;
        }

        $stmt_insertion->execute([
            'titre' => $article['titre'],
            'contenu' => $article['resume'] !== '' ? $article['resume'] : $article['titre'],
            'lien' => $article['lien'],
            'id_source' => $source['id_source'],
            'date_publication' => $article['date'],
        ]);

        if ($stmt_insertion->rowCount() > 0) {
            $nouveaux_pour_cette_source++;
            $total_nouveaux++;
        } else {
            $total_ignores++;
        }
    }

    $rapport[] = "✅ {$source['nom_source']} : $nouveaux_pour_cette_source nouvel(aux) article(s) ajouté(s).";
}

/* -------------------------------------------------------------
 * Affichage du résultat
 * ----------------------------------------------------------- */
if ($est_cli) {
    echo "=== Import des articles ===\n";
    foreach ($rapport as $ligne) {
        echo $ligne . "\n";
    }
    echo "Total : $total_nouveaux nouveaux, $total_ignores déjà existants.\n";
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo "<h2>Résultat de l'importation</h2><ul>";
    foreach ($rapport as $ligne) {
        echo "<li>" . htmlspecialchars($ligne) . "</li>";
    }
    echo "</ul>";
    echo "<p><strong>Total : $total_nouveaux nouveaux article(s), $total_ignores déjà existant(s).</strong></p>";
    echo '<p><a href="admin_articles.php">← Retour à la gestion des sources</a></p>';
}
