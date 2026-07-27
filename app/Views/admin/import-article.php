<?php
if (!function_exists('e')) {
    function e(?string $v = null): string {
        return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    }
}
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
    }

    .resume-item .label {
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .resume-item.nouveaux .nombre { color: #1a7a2e; }
    .resume-item.ignores .nombre { color: var(--text-muted); }
    .resume-item.sources .nombre { color: var(--primary); }

    .rapport-liste {
        list-style: none;
        margin: 0 0 25px;
        padding: 0;
    }

    .rapport-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-left: 4px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 14px 18px;
        margin-bottom: 10px;
    }

    .rapport-item .puce {
        font-size: 1.1rem;
        line-height: 1;
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

    .section-titre {
        margin: 40px 0 14px;
        font-size: 1.15rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-sous-titre {
        margin: -8px 0 16px;
        color: var(--text-muted);
        font-size: 0.88rem;
    }

    .alerte-validation {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        margin-bottom: 16px;
        background: var(--success-bg);
        color: var(--success-text);
        border: 1px solid var(--success-border);
    }

    .article-attente {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 16px 20px;
        margin-bottom: 12px;
    }

    .article-attente h3 {
        margin: 0 0 6px;
        font-size: 1rem;
        color: var(--text-main);
    }

    .article-attente .apercu {
        color: var(--text-muted);
        font-size: 0.87rem;
        line-height: 1.5;
        margin-bottom: 8px;
        max-height: 3em;
        overflow: hidden;
    }

    .article-attente .meta {
        font-size: 0.78rem;
        color: var(--text-muted);
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .article-attente .meta a {
        color: var(--primary);
        text-decoration: none;
    }

    .article-attente .actions {
        display: flex;
        gap: 10px;
    }

    .article-attente .actions a {
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        padding: 7px 14px;
        border-radius: 6px;
    }

    .action-valider {
        color: var(--success-text);
        background: var(--success-bg);
    }

    .action-refuser {
        color: var(--erreur-text);
        background: var(--erreur-bg);
    }
</style>
</head>
<body>
<div class="import-container">

    <?php if ($vue === 'import'): ?>
        <div class="import-header">
            <span class="icone">🔄</span>
            <h1>Import des articles</h1>
            <p>Résultat de l'actualisation des flux RSS et réseaux sociaux</p>
        </div>

        <div class="resume-globale">
            <div class="resume-item nouveaux">
                <span class="nombre"><?php echo (int)$totalNouveaux; ?></span>
                <span class="label">Nouveaux articles</span>
            </div>
            <div class="resume-item ignores">
                <span class="nombre"><?php echo (int)$totalIgnores; ?></span>
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
    <?php else: /* $vue === 'validation' : on vient de valider/refuser un article */ ?>
        <div class="import-header">
            <span class="icone">📝</span>
            <h1>Validation des articles</h1>
            <p>Les flux RSS n'ont pas été retéléchargés pour cette action</p>
        </div>
    <?php endif; ?>

    <?php if ($messageValidation): ?>
        <div class="alerte-validation"><?= e($messageValidation) ?></div>
    <?php endif; ?>

    <h2 class="section-titre">📝 Articles rédacteur en attente de validation</h2>
    <p class="section-sous-titre">Ces articles ont été soumis par des rédacteurs et ne sont pas encore visibles sur le site public.</p>

    <?php if (empty($articlesAValider)): ?>
        <p class="aucune-source">Aucun article en attente de validation.</p>
    <?php else: ?>
        <?php foreach ($articlesAValider as $art): ?>
            <div class="article-attente">
                <h3><?= e($art['titre']) ?></h3>
                <div class="apercu"><?= e(mb_strimwidth(strip_tags($art['contenu']), 0, 200, '…')) ?></div>
                <div class="meta">
                    <span>Par <?= e(trim(($art['auteur_prenom'] ?? '') . ' ' . ($art['auteur_nom'] ?? '')) ?: 'Auteur inconnu') ?></span>
                    <span>Soumis le <?= date('d/m/Y à H:i', strtotime($art['date_publication'])) ?></span>
                    <?php if (!empty($art['lien_source'])): ?>
                        <a href="<?= e($art['lien_source']) ?>" target="_blank" rel="noopener">Voir la publication Facebook ↗</a>
                    <?php endif; ?>
                </div>
                <div class="actions">
                    <a href="index.php?action=admin-import-articles&valider=<?= (int)$art['id_article'] ?>" class="action-valider">Valider</a>
                    <a href="index.php?action=admin-import-articles&refuser=<?= (int)$art['id_article'] ?>"
                       class="action-refuser"
                       onclick="return confirm('Refuser cet article ? Il restera invisible du public.');">Refuser</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="retour" style="margin-top: 30px;">
        <a href="index.php?action=admin-articles">← Retour à la gestion des sources</a>
    </div>

</div>
</body>
</html>
