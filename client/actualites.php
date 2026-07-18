<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';

// Récupère les articles, qu'ils soient écrits par un rédacteur
// ou importés automatiquement depuis une source RSS.
$stmt = $pdo->query("
    SELECT
        a.id_article,
        a.titre,
        a.contenu,
        a.lien_source,
        a.date_publication,
        u.nom AS auteur_nom,
        u.prenom AS auteur_prenom,
        s.nom_source
    FROM article a
    LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
    LEFT JOIN sources_articles s ON a.id_source = s.id_source
    WHERE a.statut = 'publie'
    ORDER BY a.date_publication DESC
    LIMIT 50
");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

function extraitTexte($texte, $longueur = 220) {
    $texte = trim(strip_tags($texte));
    if (mb_strlen($texte) <= $longueur) {
        return htmlspecialchars($texte);
    }
    return htmlspecialchars(mb_substr($texte, 0, $longueur)) . '…';
}

function formatDate($date) {
    $mois = [
        1=>'janvier',2=>'février',3=>'mars',4=>'avril',5=>'mai',6=>'juin',
        7=>'juillet',8=>'août',9=>'septembre',10=>'octobre',11=>'novembre',12=>'décembre'
    ];
    $ts = strtotime($date);
    return date('j', $ts) . ' ' . $mois[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#2c7a7b">
    <title>Actualités - Urgences Antsiranana</title>
    <style>
        :root {
            --primary: #2c7a7b;
            --primary-dark: #1f5b5c;
            --border-color: #dcdfe3;
            --text-main: #2d3436;
            --text-muted: #6b7280;
            --radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            color: var(--text-main);
        }

        .conteneur {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        header.page-header {
            text-align: center;
            padding: 30px 20px 10px;
        }

        header.page-header h1 {
            margin: 0 0 6px;
            font-size: 1.7rem;
            color: var(--primary-dark);
        }

        header.page-header p {
            color: var(--text-muted);
            margin: 0;
        }

        .liste-articles {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 20px;
        }

        .carte-article {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
        }

        .carte-article h2 {
            margin: 0 0 8px;
            font-size: 1.15rem;
            line-height: 1.35;
        }

        .carte-article h2 a {
            color: var(--text-main);
            text-decoration: none;
        }

        .carte-article h2 a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .meta-article {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .badge-source {
            background: #eef6f6;
            color: var(--primary-dark);
            padding: 2px 9px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-redaction {
            background: #f3f0fb;
            color: #5b3fae;
            padding: 2px 9px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .extrait-article {
            font-size: 0.92rem;
            color: #4a4f54;
            line-height: 1.5;
            margin: 0 0 12px;
        }

        .lien-article {
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
        }
        .lien-article:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 40px 20px;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .conteneur { padding: 14px; }
            header.page-header { padding: 20px 10px 5px; }
            header.page-header h1 { font-size: 1.4rem; }
            .carte-article { padding: 16px; }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php' ?>

    <header class="page-header">
        <h1>Actualités</h1>
        <p>Dernières nouvelles locales et informations utiles</p>
    </header>

    <div class="conteneur">
        <div class="liste-articles">
            <?php if (empty($articles)): ?>
                <p class="empty-state">Aucun article disponible pour le moment.</p>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <article class="carte-article">
                        <div class="meta-article">
                            <span><?php echo formatDate($article['date_publication']); ?></span>
                            <?php if ($article['nom_source']): ?>
                                <span class="badge-source"><?php echo htmlspecialchars($article['nom_source']); ?></span>
                            <?php elseif ($article['auteur_nom']): ?>
                                <span class="badge-redaction">Rédaction — <?php echo htmlspecialchars($article['auteur_prenom'] . ' ' . $article['auteur_nom']); ?></span>
                            <?php endif; ?>
                        </div>

                        <h2>
                            <?php if ($article['lien_source']): ?>
                                <a href="<?php echo htmlspecialchars($article['lien_source']); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($article['titre']); ?>
                                </a>
                            <?php else: ?>
                                <a href="article.php?id=<?php echo (int)$article['id_article']; ?>">
                                    <?php echo htmlspecialchars($article['titre']); ?>
                                </a>
                            <?php endif; ?>
                        </h2>

                        <p class="extrait-article"><?php echo extraitTexte($article['contenu']); ?></p>

                        <?php if ($article['lien_source']): ?>
                            <a class="lien-article" href="<?php echo htmlspecialchars($article['lien_source']); ?>" target="_blank" rel="noopener noreferrer">
                                Lire l'article complet sur le site source →
                            </a>
                        <?php else: ?>
                            <a class="lien-article" href="article.php?id=<?php echo (int)$article['id_article']; ?>">
                                Lire l'article complet →
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>