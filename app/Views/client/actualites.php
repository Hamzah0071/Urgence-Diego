<?php
use App\Models\Article;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Actualités - Urgences Antsiranana</title>
    <link rel="stylesheet" href="public/asset/css/client/home.css">
    <link rel="stylesheet" href="public/asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Compléments propres à cette page : mêmes variables que home.css. */
        .page-header {
            text-align: center;
            padding: 40px 20px 10px;
        }

        .page-header h1 {
            font-family: 'Fraunces', serif;
            margin: 0 0 8px;
            font-size: 2rem;
            color: var(--navy-dk);
        }

        .page-header p {
            color: #64748b;
            margin: 0;
            font-size: 1.05rem;
        }

        .news-source-badge {
            background: var(--sage);
            color: var(--navy-dk);
            padding: 3px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.72rem;
        }

        .empty-state-actus {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state-actus i {
            font-size: 3rem;
            color: #e2e8f0;
            margin-bottom: 16px;
            display: block;
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../includes/header.php'; ?>

    <header class="page-header">
        <h1>Actualités de Diego-Suarez</h1>
        <p>Restez informé des derniers événements en temps réel.</p>
    </header>

    <main>
        <?php if (empty($articles)): ?>
            <div class="empty-state-actus">
                <i class="fas fa-newspaper"></i>
                <p>Aucune actualité n'est disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="news-grid">
                <?php foreach ($articles as $article):
                    $image = Article::extraireImage($article['contenu']);
                    $extrait = Article::nettoyerContenu($article['contenu']);
                    $extraitCourt = mb_substr($extrait, 0, 180) . (mb_strlen($extrait) > 180 ? '…' : '');
                    $lienInterne = "index.php?action=article-detail&id=" . (int)$article['id_article'];
                ?>
                    <a href="<?= $lienInterne ?>" class="news-card">
                        <?php if ($image): ?>
                            <div class="news-img">
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($article['titre']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="news-content">
                            <span class="news-date">
                                <span class="news-source-badge"><?= htmlspecialchars($article['nom_source'] ?? 'Rédaction') ?></span>
                                <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($article['date_publication'])) ?>
                            </span>
                            <h3 class="news-title"><?= htmlspecialchars($article['titre']) ?></h3>
                            <p class="news-excerpt"><?= htmlspecialchars($extraitCourt) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>