<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Espace Rédacteur — Urgences Antsiranana</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --bleu-nuit:#12314F;
    --bleu:#1B4F72;
    --bleu-clair:#EAF1F7;
    --rouge-urgence:#E0483F;
    --vert-ok:#1E8E5A;
    --gris-texte:#4A5568;
    --gris-bord:#DFE5EC;
    --fond:#F5F7FA;
    --blanc:#FFFFFF;
    --radius:10px;
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    font-family:'Inter',system-ui,sans-serif;
    background:var(--fond);
    color:#1A2530;
  }
  main{
    max-width:900px;
    margin:0 auto;
    padding:28px 20px 60px;
  }
  h1{
    font-family:'Poppins',sans-serif;
    font-size:1.5rem;
    margin:0 0 4px;
    color:var(--bleu-nuit);
  }
  p.souscription{
    color:var(--gris-texte);
    margin:0 0 24px;
    font-size:0.95rem;
  }
  nav.onglets{
    display:flex;
    gap:8px;
    margin-bottom:22px;
    border-bottom:1px solid var(--gris-bord);
  }
  nav.onglets a{
    text-decoration:none;
    color:var(--gris-texte);
    font-weight:600;
    font-size:0.92rem;
    padding:10px 18px;
    border-radius:8px 8px 0 0;
    position:relative;
    top:1px;
  }
  nav.onglets a.actif{
    color:var(--bleu);
    background:var(--blanc);
    border:1px solid var(--gris-bord);
    border-bottom:1px solid var(--blanc);
  }
  .carte{
    background:var(--blanc);
    border:1px solid var(--gris-bord);
    border-radius:var(--radius);
    padding:24px;
  }
  .alerte{
    padding:12px 16px;
    border-radius:8px;
    font-size:0.9rem;
    margin-bottom:18px;
  }
  .alerte.erreur{
    background:#FDECEA;
    color:#9A2C22;
    border:1px solid #F5C6C0;
  }
  .alerte.succes{
    background:#E9F7EF;
    color:var(--vert-ok);
    border:1px solid #B7E4C7;
  }
  .info-aide{
    background:var(--bleu-clair);
    border:1px solid #CFE1F0;
    border-radius:8px;
    padding:14px 16px;
    font-size:0.87rem;
    color:var(--bleu-nuit);
    margin-bottom:20px;
    line-height:1.5;
  }
  .info-aide b{color:var(--bleu);}
  label{
    display:block;
    font-size:0.85rem;
    font-weight:600;
    margin:16px 0 6px;
    color:var(--bleu-nuit);
  }
  label:first-of-type{margin-top:0;}
  input[type="text"], input[type="url"], textarea{
    width:100%;
    padding:11px 13px;
    border:1px solid var(--gris-bord);
    border-radius:8px;
    font-size:0.93rem;
    font-family:inherit;
    color:#1A2530;
  }
  input:focus, textarea:focus{
    outline:none;
    border-color:var(--bleu);
    box-shadow:0 0 0 3px rgba(27,79,114,0.12);
  }
  textarea{resize:vertical; min-height:110px;}
  .aide-champ{
    font-size:0.78rem;
    color:#8A94A3;
    margin-top:4px;
  }
  .bouton{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:var(--bleu);
    color:#fff;
    border:none;
    padding:11px 22px;
    border-radius:8px;
    font-weight:600;
    font-size:0.92rem;
    cursor:pointer;
    margin-top:20px;
    text-decoration:none;
  }
  .bouton:hover{background:var(--bleu-nuit);}
  .bouton.secondaire{
    background:transparent;
    color:var(--gris-texte);
    border:1px solid var(--gris-bord);
  }
  .bouton.secondaire:hover{background:#F0F3F7;}
  .liste-articles{
    display:flex;
    flex-direction:column;
    gap:14px;
  }
  .article-item{
    background:var(--blanc);
    border:1px solid var(--gris-bord);
    border-radius:var(--radius);
    padding:18px 20px;
  }
  .article-item .ligne-haut{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
    flex-wrap:wrap;
  }
  .article-item h3{
    margin:0 0 6px;
    font-size:1.02rem;
    color:var(--bleu-nuit);
  }
  .badge{
    font-size:0.72rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.4px;
    padding:3px 10px;
    border-radius:20px;
    white-space:nowrap;
  }
  .badge.publie{background:#E9F7EF; color:var(--vert-ok);}
  .badge.brouillon{background:#FFF4E0; color:#B4700B;}
  .badge.archive{background:#EEF0F3; color:#65707D;}
  .article-item .contenu-apercu{
    color:var(--gris-texte);
    font-size:0.88rem;
    margin:8px 0 10px;
    line-height:1.5;
    max-height:3.2em;
    overflow:hidden;
  }
  .article-item .meta{
    font-size:0.78rem;
    color:#8A94A3;
    display:flex;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:10px;
  }
  .article-item .meta a{color:var(--bleu); text-decoration:none;}
  .article-item .actions{
    display:flex;
    gap:10px;
  }
  .article-item .actions a{
    font-size:0.8rem;
    font-weight:600;
    text-decoration:none;
    padding:6px 12px;
    border-radius:6px;
  }
  .action-modifier{color:var(--bleu); background:var(--bleu-clair);}
  .action-supprimer{color:var(--rouge-urgence); background:#FDECEA;}
  .etat-vide{
    text-align:center;
    padding:50px 20px;
    color:#8A94A3;
  }
  .etat-vide .bouton{margin-top:14px;}
</style>
</head>
<body>

<?php require __DIR__ . '/../includes/header.php'; ?>

<main>
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

        <button type="submit" class="bouton"><?= $articleAModifier ? "Enregistrer les modifications" : "Publier l'article" ?></button>
        <?php if ($articleAModifier): ?>
          <a href="index.php?action=new-article&onglet=historique" class="bouton secondaire">Annuler</a>
        <?php endif; ?>
      </form>
    </div>

  <?php else: /* Historique */ ?>

    <?php if (empty($mesArticles)): ?>
      <div class="carte etat-vide">
        Tu n'as encore publié aucun article.
        <br>
        <a href="index.php?action=new-article&onglet=publier" class="bouton">Publier mon premier article</a>
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
