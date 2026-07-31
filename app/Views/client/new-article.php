<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Espace Rédacteur — Urgences Antsiranana</title>
<link rel="stylesheet" href="public/asset/css/client/home.css">
<link rel="stylesheet" href="public/asset/icon/fontAwesome/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
<style>
  /* Compléments spécifiques à cette page : mêmes variables que home.css
     (--navy, --navy-dk, --ink, --sage, --line, --radius), pas de nouvelle palette. */

  main.redacteur {
    max-width: 900px;
  }

  main.redacteur h1 {
    font-family: 'Fraunces', serif;
    font-size: 1.7rem;
    margin: 0 0 4px;
    color: var(--navy-dk);
  }

  p.souscription {
    color: #64748b;
    margin: 0 0 24px;
    font-size: 0.95rem;
  }

  nav.onglets {
    display: flex;
    gap: 8px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--line);
  }

  nav.onglets a {
    text-decoration: none;
    color: #64748b;
    font-weight: 600;
    font-size: 0.92rem;
    padding: 10px 18px;
    border-radius: 8px 8px 0 0;
    position: relative;
    top: 1px;
  }

  nav.onglets a.actif {
    color: var(--navy-dk);
    background: #ffffff;
    border: 1px solid var(--line);
    border-bottom: 1px solid #ffffff;
  }

  .carte {
    background: #ffffff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.03);
  }

  .alerte {
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.9rem;
    margin-bottom: 18px;
  }

  .alerte.erreur {
    background: #fdecea;
    color: #9a2c22;
    border: 1px solid #f5c6c0;
  }

  .alerte.succes {
    background: var(--sage);
    color: #10b981;
    border: 1px solid var(--line);
  }

  .info-aide {
    background: var(--sage);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 0.87rem;
    color: var(--navy-dk);
    margin-bottom: 20px;
    line-height: 1.5;
  }

  .info-aide b {
    color: var(--navy);
  }

  label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 16px 0 6px;
    color: var(--navy-dk);
  }

  label:first-of-type {
    margin-top: 0;
  }

  input[type="text"], input[type="url"], textarea {
    width: 100%;
    padding: 11px 13px;
    border: 1px solid var(--line);
    border-radius: 8px;
    font-size: 0.93rem;
    font-family: inherit;
    color: var(--ink);
  }

  input:focus, textarea:focus {
    outline: none;
    border-color: var(--navy);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
  }

  textarea {
    resize: vertical;
    min-height: 110px;
  }

  .aide-champ {
    font-size: 0.78rem;
    color: #94a3b8;
    margin-top: 4px;
  }

  .mt-20 {
    margin-top: 20px;
  }

  .liste-articles {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .article-item {
    background: #ffffff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .article-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
  }

  .article-item .ligne-haut {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
  }

  .article-item h3 {
    font-family: 'Fraunces', serif;
    margin: 0 0 6px;
    font-size: 1.05rem;
    color: var(--navy-dk);
  }

  .badge {
    font-family: 'Space Mono', monospace;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
  }

  .badge.publie { background: var(--sage); color: #10b981; }
  .badge.brouillon { background: #fff4e0; color: #b4700b; }
  .badge.archive { background: #eef0f3; color: #65707d; }

  .article-item .contenu-apercu {
    color: #475569;
    font-size: 0.88rem;
    margin: 8px 0 10px;
    line-height: 1.5;
    max-height: 3.2em;
    overflow: hidden;
  }

  .article-item .meta {
    font-size: 0.78rem;
    color: #94a3b8;
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }

  .article-item .meta a {
    color: var(--navy);
    text-decoration: none;
  }

  .article-item .actions {
    display: flex;
    gap: 10px;
  }

  .article-item .actions a {
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
  }

  .action-modifier { color: var(--navy-dk); background: var(--sage); }
  .action-supprimer { color: #dc2626; background: #fdecea; }

  .etat-vide {
    text-align: center;
    padding: 50px 20px;
    color: #94a3b8;
  }

  .etat-vide .btn-primary {
    margin-top: 14px;
  }

  @media (max-width: 600px) {
    .article-item .ligne-haut { flex-direction: column; }
    .article-item .actions { flex-direction: column; }
  }
</style>
</head>
<body>

<?php require __DIR__ . '/../includes/header.php'; ?>

<main class="redacteur">
  <h1>Publications</h1>
  <p class="souscription">Publie un article ou consulte l'historique de tes publications.</p>

  <nav class="onglets">
    <a href="index.php?action=new-article&onglet=publier" class="<?= $onglet === 'publier' ? 'actif' : '' ?>">Publier un article</a>
    <a href="index.php?action=new-article&onglet=historique" class="<?= $onglet === 'historique' ? 'actif' : '' ?>">Mon historique</a>
  </nav>

  <?php if ($erreur): ?>
    <div class="alerte erreur"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>
  <?php if ($succes): ?>
    <div class="alerte succes"><?= htmlspecialchars($succes) ?></div>
  <?php endif; ?>

  <?php if ($onglet === 'publier'): ?>

    <div class="info-aide">
      <b>Comment ça marche :</b> publie d'abord ta photo, vidéo ou audio directement sur la page Facebook.
      Reviens ensuite ici, retape le titre et la description de ta publication, puis colle le lien de
      cette publication Facebook ci-dessous. Ton article passera en <b>attente de validation</b> : il ne sera
      visible sur le site public qu'après validation par un administrateur.
    </div>

    <div class="carte">
      <form method="post" action="index.php?action=new-article">
        <input type="hidden" name="action" value="<?= $articleAModifier ? 'modifier' : 'publier' ?>">
        <?php if ($articleAModifier): ?>
          <input type="hidden" name="id_article" value="<?= (int)$articleAModifier['id_article'] ?>">
        <?php endif; ?>

        <label for="titre">Titre de l'article</label>
        <input type="text" id="titre" name="titre" maxlength="255" required
               placeholder="Ex : Fandaharana manokana 24 jolay 2026"
               value="<?= htmlspecialchars($articleAModifier['titre'] ?? '') ?>">

        <label for="contenu">Description</label>
        <textarea id="contenu" name="contenu" required
                  placeholder="Résume ici le contenu de ta publication..."><?= htmlspecialchars($articleAModifier['contenu'] ?? '') ?></textarea>

        <label for="lien_source">Lien de ta publication Facebook</label>
        <input type="url" id="lien_source" name="lien_source" required
               placeholder="https://www.facebook.com/..."
               value="<?= htmlspecialchars($articleAModifier['lien_source'] ?? '') ?>">
        <div class="aide-champ">Colle ici l'adresse (URL) de la publication contenant la photo, vidéo ou audio.</div>

        <button type="submit" class="btn-primary mt-20"><?= $articleAModifier ? "Enregistrer les modifications" : "Publier l'article" ?></button>
        <?php if ($articleAModifier): ?>
          <a href="index.php?action=new-article&onglet=historique" class="btn-call mt-20">Annuler</a>
        <?php endif; ?>
      </form>
    </div>

  <?php else: /* Historique */ ?>

    <?php if (empty($mesArticles)): ?>
      <div class="carte etat-vide">
        Tu n'as encore publié aucun article.
        <br>
        <a href="index.php?action=new-article&onglet=publier" class="btn-primary">Publier mon premier article</a>
      </div>
    <?php else: ?>
      <div class="liste-articles">
        <?php foreach ($mesArticles as $art): ?>
          <div class="article-item">
            <div class="ligne-haut">
              <h3><?= htmlspecialchars($art['titre']) ?></h3>
              <?php
                $libelles_statut = [
                    'brouillon' => 'En attente de validation',
                    'publie'    => 'Validé et publié',
                    'archive'   => 'Archivé',
                ];
                $libelle_statut = $libelles_statut[$art['statut']] ?? $art['statut'];
              ?>
              <span class="badge <?= htmlspecialchars($art['statut']) ?>"><?= htmlspecialchars($libelle_statut) ?></span>
            </div>
            <div class="contenu-apercu"><?= htmlspecialchars(mb_strimwidth(strip_tags($art['contenu']), 0, 220, '…')) ?></div>
            <div class="meta">
              <span>Publié le <?= date('d/m/Y à H:i', strtotime($art['date_publication'])) ?></span>
              <?php if (!empty($art['lien_source'])): ?>
                <a href="<?= htmlspecialchars($art['lien_source']) ?>" target="_blank" rel="noopener">Voir la publication Facebook ↗</a>
              <?php endif; ?>
            </div>
            <div class="actions">
              <a href="index.php?action=new-article&onglet=publier&modifier=<?= (int)$art['id_article'] ?>" class="action-modifier">Modifier</a>
              <a href="index.php?action=new-article&supprimer=<?= (int)$art['id_article'] ?>"
                 class="action-supprimer"
                 onclick="return confirm('Supprimer définitivement cet article ?');">Supprimer</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>