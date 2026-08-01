<?php
use App\Models\Article;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($article['titre']) ?> - Urgences Antsiranana</title>
    <link rel="stylesheet" href="public/asset/css/client/home.css">
    <link rel="stylesheet" href="public/asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Compléments propres à cette page : mêmes variables que home.css. */
        body {
            line-height: 1.7;
        }

        .article-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--navy);
            font-weight: 600;
            margin-bottom: 30px;
        }

        .back-link:hover {
            color: var(--navy-dk);
        }

        .article-header h1 {
            font-family: 'Fraunces', serif;
            font-size: 2.2rem;
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--navy-dk);
        }

        .article-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }

        .article-image {
            width: 100%;
            border-radius: var(--radius);
            margin-bottom: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .article-content {
            font-size: 1.1rem;
            color: var(--ink);
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }

        .source-box {
            margin-top: 60px;
            padding: 30px;
            background: var(--sage);
            border-radius: var(--radius);
            text-align: center;
        }

        .source-box p {
            margin: 0;
            color: #475569;
        }

        /* ---------- Autres actualités (related) ---------- */
        .related-section {
            max-width: 1100px;
            margin: 20px auto 0;
            padding: 40px 20px 60px;
            border-top: 1px solid var(--line);
        }

        .related-section h2 {
            font-family: 'Fraunces', serif;
            font-size: 1.5rem;
            color: var(--navy-dk);
            margin: 30px 0 24px;
        }

        .news-source-badge {
            background: var(--sage);
            color: var(--navy-dk);
            padding: 3px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.72rem;
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../includes/header.php'; ?>

    <article class="article-container">
        <a href="index.php?action=actualites" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour aux actualités
        </a>

        <header class="article-header">
            <h1><?= htmlspecialchars($article['titre']) ?></h1>
            <div class="article-meta">
                <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($article['date_publication'])) ?></span>
                <span><i class="fas fa-tag"></i> <?= htmlspecialchars($article['nom_source'] ?? 'Rédaction locale') ?></span>
                <?php if (!empty($article['auteur_nom'])): ?>
                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($article['auteur_prenom'] . ' ' . $article['auteur_nom']) ?></span>
                <?php endif; ?>
            </div>
        </header>

        <?php $image = Article::extraireImage($article['contenu']); ?>
        <?php if ($image): ?>
            <img src="<?= htmlspecialchars($image) ?>" class="article-image" alt="<?= htmlspecialchars($article['titre']) ?>">
        <?php endif; ?>

        <div class="article-content">
            <?= $article['contenu'] ?>
        </div>

        <?php if (!empty($article['lien_source'])): ?>
            <div class="source-box">
                <p>Cet article provient d'une source externe (<?= htmlspecialchars($article['nom_source'] ?? '') ?>).</p>
                <a href="<?= htmlspecialchars($article['lien_source']) ?>" target="_blank" rel="noopener" class="btn-primary" style="margin-top: 15px;">
                    Consulter la source originale <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        <?php endif; ?>
    </article>

    <!-- ================= AUTRES ACTUALITÉS ================= -->
    <?php if (!empty($autresArticles)): ?>
        <section class="related-section">
            <h2>Autres actualités</h2>
            <div class="news-grid">
                <?php foreach ($autresArticles as $autre):
                    $imgAutre = Article::extraireImage($autre['contenu']);
                    $extraitAutre = Article::nettoyerContenu($autre['contenu']);
                    $extraitCourt = mb_substr($extraitAutre, 0, 110) . (mb_strlen($extraitAutre) > 110 ? '…' : '');
                    $lienAutre = "index.php?action=article-detail&id=" . (int)$autre['id_article'];
                ?>
                    <a href="<?= $lienAutre ?>" class="news-card">
                        <?php if ($imgAutre): ?>
                            <div class="news-img">
                                <img src="<?= htmlspecialchars($imgAutre) ?>" alt="<?= htmlspecialchars($autre['titre']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="news-content">
                            <span class="news-date">
                                <span class="news-source-badge"><?= htmlspecialchars($autre['nom_source'] ?? 'Rédaction') ?></span>
                                <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($autre['date_publication'])) ?>
                            </span>
                            <h3 class="news-title"><?= htmlspecialchars($autre['titre']) ?></h3>
                            <p class="news-excerpt"><?= htmlspecialchars($extraitCourt) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>