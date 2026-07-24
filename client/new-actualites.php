<?php
/**
 * ============================================================
 *  ESPACE RÉDACTEUR — Urgences Antsiranana
 * ============================================================
 *  Ce fichier gère 2 écrans pour le rôle "Redacteur" (id_role = 2) :
 *    1) Publier un article : le rédacteur tape le titre + la description,
 *       puis colle le lien de SA publication Facebook (photo/vidéo/audio
 *       déjà publiés là-bas). L'article est enregistré avec id_source = NULL
 *       (donc "publication manuelle", pour le distinguer des articles
 *       importés automatiquement via les flux RSS de sources_articles).
 *    2) Historique : liste des articles que CE rédacteur a lui-même publiés,
 *       avec possibilité de les modifier ou de les supprimer.
 *
 *  Ce fichier doit être placé dans le dossier client/, sous le nom
 *  new-actualites.php (c'est le lien déjà présent dans header.php).
 * ============================================================
 */

require_once '../includes/session.php';
// session.php redirige déjà vers login.php si non connecté, connecte $pdo,
// et pose $_SESSION['id_role'].

/* ------------------------------------------------------------
   CONTRÔLE D'ACCÈS : uniquement le rôle Redacteur (id_role = 2)
------------------------------------------------------------- */
if ((int)($_SESSION['id_role'] ?? 0) !== 2) {
    header('Location: ../login.php');
    exit;
}

$id_utilisateur = (int)$_SESSION['id_utilisateur'];

$erreur  = '';
$succes  = '';
$onglet  = $_GET['onglet'] ?? 'publier';
$article_a_modifier = null;

/* ------------------------------------------------------------
   ACTION : publier un nouvel article
------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'publier') {
    $titre       = trim($_POST['titre'] ?? '');
    $contenu     = trim($_POST['contenu'] ?? '');
    $lien_source = trim($_POST['lien_source'] ?? '');

    if ($titre === '' || $contenu === '' || $lien_source === '') {
        $erreur = "Tous les champs sont obligatoires (titre, description et lien Facebook).";
    } elseif (!filter_var($lien_source, FILTER_VALIDATE_URL)) {
        $erreur = "Le lien collé n'est pas une URL valide.";
    } elseif (stripos($lien_source, 'facebook.com') === false) {
        $erreur = "Le lien doit être celui de ta publication Facebook (facebook.com ou m.facebook.com).";
    } else {
        try {
            // Statut "brouillon" : l'article n'est visible sur le site public
            // qu'une fois validé par un administrateur (passage à "publie").
            $stmt = $pdo->prepare(
                "INSERT INTO article (titre, contenu, lien_source, id_auteur, id_source, statut)
                 VALUES (:titre, :contenu, :lien_source, :id_auteur, NULL, 'brouillon')"
            );
            $stmt->execute([
                ':titre'       => $titre,
                ':contenu'     => $contenu,
                ':lien_source' => $lien_source,
                ':id_auteur'   => $id_utilisateur,
            ]);
            header('Location: new-actualites.php?onglet=historique&succes=publie');
            exit;
        } catch (PDOException $e) {
            $erreur = ($e->getCode() == 23000)
                ? "Ce lien Facebook a déjà été utilisé pour un autre article."
                : "Erreur lors de l'enregistrement de l'article.";
        }
    }
}

/* ------------------------------------------------------------
   ACTION : modifier un article existant (le sien uniquement)
------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
    $id_article  = (int)($_POST['id_article'] ?? 0);
    $titre       = trim($_POST['titre'] ?? '');
    $contenu     = trim($_POST['contenu'] ?? '');
    $lien_source = trim($_POST['lien_source'] ?? '');

    if ($titre === '' || $contenu === '' || $lien_source === '') {
        $erreur = "Tous les champs sont obligatoires.";
        $onglet = 'publier';
        $article_a_modifier = ['id_article' => $id_article, 'titre' => $titre, 'contenu' => $contenu, 'lien_source' => $lien_source];
    } elseif (!filter_var($lien_source, FILTER_VALIDATE_URL)) {
        $erreur = "Le lien collé n'est pas une URL valide.";
        $onglet = 'publier';
        $article_a_modifier = ['id_article' => $id_article, 'titre' => $titre, 'contenu' => $contenu, 'lien_source' => $lien_source];
    } else {
        // Si l'article avait déjà été validé et publié, une modification
        // du rédacteur le repasse en brouillon : l'admin doit revalider.
        $stmt = $pdo->prepare(
            "UPDATE article
             SET titre = :titre, contenu = :contenu, lien_source = :lien_source,
                 statut = IF(statut = 'publie', 'brouillon', statut)
             WHERE id_article = :id AND id_auteur = :id_auteur"
        );
        $stmt->execute([
            ':titre'       => $titre,
            ':contenu'     => $contenu,
            ':lien_source' => $lien_source,
            ':id'          => $id_article,
            ':id_auteur'   => $id_utilisateur,
        ]);
        header('Location: new-actualites.php?onglet=historique&succes=modifie');
        exit;
    }
}

/* ------------------------------------------------------------
   ACTION : passer un article existant en mode édition
------------------------------------------------------------- */
if (isset($_GET['modifier'])) {
    $id_article = (int)$_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id_article = :id AND id_auteur = :id_auteur");
    $stmt->execute([':id' => $id_article, ':id_auteur' => $id_utilisateur]);
    $article_a_modifier = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($article_a_modifier) {
        $onglet = 'publier';
    }
}

/* ------------------------------------------------------------
   ACTION : supprimer un article (le sien uniquement)
------------------------------------------------------------- */
if (isset($_GET['supprimer'])) {
    $id_article = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM article WHERE id_article = :id AND id_auteur = :id_auteur");
    $stmt->execute([':id' => $id_article, ':id_auteur' => $id_utilisateur]);
    header('Location: new-actualites.php?onglet=historique&succes=supprime');
    exit;
}

/* ------------------------------------------------------------
   Message de succès (après redirection)
------------------------------------------------------------- */
if (isset($_GET['succes'])) {
    $messages = [
        'publie'   => "Article envoyé pour validation. Il sera visible sur le site dès qu'un administrateur l'aura validé.",
        'modifie'  => "Article modifié. Il repasse en attente de validation par un administrateur.",
        'supprime' => "Article supprimé.",
    ];
    $succes = $messages[$_GET['succes']] ?? '';
}

/* ------------------------------------------------------------
   Récupération de l'historique des publications du rédacteur
------------------------------------------------------------- */
$mes_articles = [];
if ($onglet === 'historique') {
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id_auteur = :id_auteur ORDER BY date_publication DESC");
    $stmt->execute([':id_auteur' => $id_utilisateur]);
    $mes_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function e(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
?>
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

<?php include '../includes/header.php'; ?>

<main>
  <h1>Publications</h1>
  <p class="souscription">Publie un article ou consulte l'historique de tes publications.</p>

  <nav class="onglets">
    <a href="new-actualites.php?onglet=publier" class="<?= $onglet === 'publier' ? 'actif' : '' ?>">Publier un article</a>
    <a href="new-actualites.php?onglet=historique" class="<?= $onglet === 'historique' ? 'actif' : '' ?>">Mon historique</a>
  </nav>

  <?php if ($erreur): ?>
    <div class="alerte erreur"><?= e($erreur) ?></div>
  <?php endif; ?>
  <?php if ($succes): ?>
    <div class="alerte succes"><?= e($succes) ?></div>
  <?php endif; ?>

  <?php if ($onglet === 'publier'): ?>

    <div class="info-aide">
      <b>Comment ça marche :</b> publie d'abord ta photo, vidéo ou audio directement sur la page Facebook.
      Reviens ensuite ici, retape le titre et la description de ta publication, puis colle le lien de
      cette publication Facebook ci-dessous. Ton article passera en <b>attente de validation</b> : il ne sera
      visible sur le site public qu'après validation par un administrateur.
    </div>

    <div class="carte">
      <form method="post" action="new-actualites.php">
        <input type="hidden" name="action" value="<?= $article_a_modifier ? 'modifier' : 'publier' ?>">
        <?php if ($article_a_modifier): ?>
          <input type="hidden" name="id_article" value="<?= (int)$article_a_modifier['id_article'] ?>">
        <?php endif; ?>

        <label for="titre">Titre de l'article</label>
        <input type="text" id="titre" name="titre" maxlength="255" required
               placeholder="Ex : Fandaharana manokana 24 jolay 2026"
               value="<?= e($article_a_modifier['titre'] ?? '') ?>">

        <label for="contenu">Description</label>
        <textarea id="contenu" name="contenu" required
                  placeholder="Résume ici le contenu de ta publication..."><?= e($article_a_modifier['contenu'] ?? '') ?></textarea>

        <label for="lien_source">Lien de ta publication Facebook</label>
        <input type="url" id="lien_source" name="lien_source" required
               placeholder="https://www.facebook.com/..."
               value="<?= e($article_a_modifier['lien_source'] ?? '') ?>">
        <div class="aide-champ">Colle ici l'adresse (URL) de la publication contenant la photo, vidéo ou audio.</div>

        <button type="submit" class="bouton"><?= $article_a_modifier ? "Enregistrer les modifications" : "Publier l'article" ?></button>
        <?php if ($article_a_modifier): ?>
          <a href="new-actualites.php?onglet=historique" class="bouton secondaire">Annuler</a>
        <?php endif; ?>
      </form>
    </div>

  <?php else: /* Historique */ ?>

    <?php if (empty($mes_articles)): ?>
      <div class="carte etat-vide">
        Tu n'as encore publié aucun article.
        <br>
        <a href="new-actualites.php?onglet=publier" class="bouton">Publier mon premier article</a>
      </div>
    <?php else: ?>
      <div class="liste-articles">
        <?php foreach ($mes_articles as $art): ?>
          <div class="article-item">
            <div class="ligne-haut">
              <h3><?= e($art['titre']) ?></h3>
              <?php
                $libelles_statut = [
                    'brouillon' => 'En attente de validation',
                    'publie'    => 'Validé et publié',
                    'archive'   => 'Archivé',
                ];
                $libelle_statut = $libelles_statut[$art['statut']] ?? $art['statut'];
              ?>
              <span class="badge <?= e($art['statut']) ?>"><?= e($libelle_statut) ?></span>
            </div>
            <div class="contenu-apercu"><?= e(mb_strimwidth(strip_tags($art['contenu']), 0, 220, '…')) ?></div>
            <div class="meta">
              <span>Publié le <?= date('d/m/Y à H:i', strtotime($art['date_publication'])) ?></span>
              <?php if (!empty($art['lien_source'])): ?>
                <a href="<?= e($art['lien_source']) ?>" target="_blank" rel="noopener">Voir la publication Facebook ↗</a>
              <?php endif; ?>
            </div>
            <div class="actions">
              <a href="new-actualites.php?onglet=publier&modifier=<?= (int)$art['id_article'] ?>" class="action-modifier">Modifier</a>
              <a href="new-actualites.php?supprimer=<?= (int)$art['id_article'] ?>"
                 class="action-supprimer"
                 onclick="return confirm('Supprimer définitivement cet article ?');">Supprimer</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>