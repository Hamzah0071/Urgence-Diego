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
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        .vignette-article {
            width: 200px;
            min-width: 200px;
            background-color: #eef2f5;
            position: relative;
        }

        .vignette-article img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .contenu-carte {
            padding: 20px;
            flex: 1;
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

            .carte-article {
                flex-direction: column;
            }

            .vignette-article {
                width: 100%;
                min-width: 100%;
                height: 180px;
            }

            .contenu-carte {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../includes/header.php'; ?>

    <header class="page-header">
        <h1>Actualités</h1>
        <p>Dernières nouvelles locales et informations utiles</p>
    </header>

    <div class="conteneur">
        <div class="liste-articles">
            <?php if (empty($articles)): ?>
                <p class="empty-state">Aucun article disponible pour le moment.</p>
            <?php else: ?>
                <?php foreach ($articles as $article):
                    $image = null; // Article::extraireImage n'existe pas
                    $lienArticle = $article['lien_source'] ? htmlspecialchars($article['lien_source']) : "index.php?action=article&id=" . (int)$article['id_article'];
                    $targetAttr = $article['lien_source'] ? 'target="_blank" rel="noopener noreferrer"' : '';
                ?>
                    <article class="carte-article">

                        <?php if ($image): ?>
                            <div class="vignette-article">
                                <a href="<?php echo $lienArticle; ?>" <?php echo $targetAttr; ?>>
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($article['titre']); ?>" loading="lazy">
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="contenu-carte">
                            <div class="meta-article">
                                <span><?php echo date('d/m/Y', strtotime($article['date_publication'])); ?></span>
                                <?php if ($article['nom_source']): ?>
                                    <span class="badge-source"><?php echo htmlspecialchars($article['nom_source']); ?></span>
                                <?php elseif ($article['auteur_nom']): ?>
                                    <span class="badge-redaction">Rédaction — <?php echo htmlspecialchars($article['auteur_prenom'] . ' ' . $article['auteur_nom']); ?></span>
                                <?php endif; ?>
                            </div>

                            <h2>
                                <a href="<?php echo $lienArticle; ?>" <?php echo $targetAttr; ?>>
                                    <?php echo htmlspecialchars($article['titre']); ?>
                                </a>
                            </h2>

                            <p class="extrait-article"><?php echo htmlspecialchars(mb_substr(strip_tags($article['contenu']), 0, 220) . '...'); ?></p>

                            <a class="lien-article" href="<?php echo $lienArticle; ?>" <?php echo $targetAttr; ?>>
                                <?php echo $article['lien_source'] ? "Lire l'article complet sur le site source →" : "Lire l'article complet →"; ?>
                            </a>
                        </div>

                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
