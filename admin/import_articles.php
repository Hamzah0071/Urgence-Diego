<?php
/**
 * import_articles.php
 * -------------------------------------------------------------
 * Parcourt toutes les sources actives de `sources_articles`,
 * télécharge chaque flux (RSS 2.0 ou Atom), et insère les
 * nouveaux articles dans la table `article`.
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
    INSERT IGNORE INTO article (titre, contenu, lien_source, id_source, id_auteur, date_publication)
    VALUES (:titre, :contenu, :lien, :id_source, NULL, :date_publication)
");

$total_nouveaux = 0;
$total_ignores = 0;
$rapport = []; // chaque ligne : ['type' => 'succes'|'erreur'|'avertissement', 'source' => ..., 'message' => ..., 'nb_nouveaux' => ...]

foreach ($sources as $source) {
    $xmlBrut = telechargerFlux($source['url_flux']);

    if ($xmlBrut === false || $xmlBrut === '') {
        $rapport[] = ['type' => 'erreur', 'source' => $source['nom_source'], 'message' => "Impossible de télécharger le flux."];
        continue;
    }

    $articles = parserFlux($xmlBrut);

    if (empty($articles)) {
        $rapport[] = ['type' => 'avertissement', 'source' => $source['nom_source'], 'message' => "Flux téléchargé mais aucun article détecté (format non reconnu ?)."];
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

    $rapport[] = ['type' => 'succes', 'source' => $source['nom_source'], 'message' => "$nouveaux_pour_cette_source nouvel(aux) article(s) ajouté(s).", 'nb_nouveaux' => $nouveaux_pour_cette_source];
}

/* -------------------------------------------------------------
 * Affichage du résultat
 * ----------------------------------------------------------- */
if ($est_cli) {
    echo "=== Import des articles ===\n";
    foreach ($rapport as $ligne) {
        $prefixe = ['succes' => '✅', 'erreur' => '❌', 'avertissement' => '⚠️'][$ligne['type']];
        echo "$prefixe {$ligne['source']} : {$ligne['message']}\n";
    }
    echo "Total : $total_nouveaux nouveaux, $total_ignores déjà existants.\n";
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Import des articles</title>
<style>
    :root {
        --primary: #2c7a7b;
        --primary-dark: #1f5b5c;
        --success-bg: #d4edda;
        --success-text: #155724;
        --success-border: #c3e6cb;
        --erreur-bg: #fdecea;
        --erreur-text: #a12b2b;
        --erreur-border: #f5c6cb;
        --warn-bg: #fff8e1;
        --warn-text: #8a6100;
        --warn-border: #ffe4a1;
        --border-color: #dcdfe3;
        --text-main: #2d3436;
        --text-muted: #6b7280;
        --radius: 10px;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    * { box-sizing: border-box; }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f4f6f7;
        color: var(--text-main);
        margin: 0;
        padding: 30px 16px;
    }

    .import-container {
        max-width: 720px;
        margin: 0 auto;
    }

    .import-header {
        text-align: center;
        margin-bottom: 25px;
    }

    .import-header .icone {
        font-size: 2.4rem;
        display: block;
        margin-bottom: 8px;
    }

    .import-header h1 {
        margin: 0 0 6px;
        font-size: 1.6rem;
    }

    .import-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .resume-globale {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px 25px;
        display: flex;
        justify-content: space-around;
        text-align: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .resume-item .nombre {
        font-size: 1.8rem;
        font-weight: 700;
        display: block;
        line-height: 1.2;
    }

    .resume-item .label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .resume-item.nouveaux .nombre { color: var(--primary); }
    .resume-item.ignores .nombre { color: var(--text-muted); }

    .rapport-liste {
        list-style: none;
        margin: 0 0 25px;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .rapport-item {
        background: #fff;
        border: 1px solid var(--border-color);
        border-left: 4px solid var(--border-color);
        border-radius: var(--radius);
        padding: 14px 18px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: var(--shadow);
    }

    .rapport-item .puce {
        font-size: 1.2rem;
        line-height: 1.4;
        flex-shrink: 0;
    }

    .rapport-item .contenu-item {
        flex: 1;
    }

    .rapport-item .nom-source {
        font-weight: 700;
        display: block;
        margin-bottom: 2px;
    }

    .rapport-item .message {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .rapport-item.succes { border-left-color: #1a7a2e; background: var(--success-bg); }
    .rapport-item.succes .nom-source { color: var(--success-text); }

    .rapport-item.erreur { border-left-color: var(--erreur-text); background: var(--erreur-bg); }
    .rapport-item.erreur .nom-source { color: var(--erreur-text); }

    .rapport-item.avertissement { border-left-color: var(--warn-text); background: var(--warn-bg); }
    .rapport-item.avertissement .nom-source { color: var(--warn-text); }

    .aucune-source {
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
        padding: 30px;
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
    }

    .retour {
        text-align: center;
    }

    .retour a {
        display: inline-block;
        text-decoration: none;
        background: var(--primary);
        color: #fff;
        padding: 10px 22px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background-color 0.15s ease;
    }

    .retour a:hover {
        background: var(--primary-dark);
    }
</style>
</head>
<body>
<div class="import-container">

    <div class="import-header">
        <span class="icone">🔄</span>
        <h1>Import des articles</h1>
        <p>Résultat de l'actualisation des flux RSS et réseaux sociaux</p>
    </div>

    <div class="resume-globale">
        <div class="resume-item nouveaux">
            <span class="nombre"><?php echo (int)$total_nouveaux; ?></span>
            <span class="label">Nouveaux articles</span>
        </div>
        <div class="resume-item ignores">
            <span class="nombre"><?php echo (int)$total_ignores; ?></span>
            <span class="label">Déjà existants</span>
        </div>
        <div class="resume-item sources">
            <span class="nombre"><?php echo count($sources); ?></span>
            <span class="label">Sources traitées</span>
        </div>
    </div>

    <?php if (empty($rapport)): ?>
        <p class="aucune-source">Aucune source active n'est configurée pour le moment.</p>
    <?php else: ?>
        <ul class="rapport-liste">
            <?php foreach ($rapport as $ligne):
                $puce = ['succes' => '✅', 'erreur' => '❌', 'avertissement' => '⚠️'][$ligne['type']];
            ?>
                <li class="rapport-item <?php echo $ligne['type']; ?>">
                    <span class="puce"><?php echo $puce; ?></span>
                    <span class="contenu-item">
                        <span class="nom-source"><?php echo htmlspecialchars($ligne['source']); ?></span>
                        <span class="message"><?php echo htmlspecialchars($ligne['message']); ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="retour">
        <a href="admin_articles.php">← Retour à la gestion des sources</a>
    </div>

</div>
</body>
</html>