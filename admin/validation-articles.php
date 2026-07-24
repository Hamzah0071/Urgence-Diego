<?php
/**
 * ============================================================
 *  VALIDATION DES ARTICLES — Urgences Antsiranana (espace Admin)
 * ============================================================
 *  Réservé au rôle "Administrateur" (id_role = 3, table `role`).
 *  Liste les articles soumis par les rédacteurs (statut = 'brouillon')
 *  et permet de les Valider (-> 'publie', visible sur le site public)
 *  ou de les Refuser (-> 'archive', reste invisible du public).
 *
 *  ⚠️ À placer dans le dossier admin/ (à côté de tes autres pages
 *  d'administration). Ajuste "../includes/..." si l'arborescence
 *  est différente chez toi.
 * ============================================================
 */

require_once '../includes/session.php';
// session.php redirige déjà vers login.php si non connecté, connecte $pdo,
// et pose $_SESSION['id_role'].

/* ------------------------------------------------------------
   CONTRÔLE D'ACCÈS : uniquement le rôle Administrateur (id_role = 3)
------------------------------------------------------------- */
if ((int)($_SESSION['id_role'] ?? 0) !== 3) {
    header('Location: ../login.php');
    exit;
}

$succes = '';
$onglet = $_GET['onglet'] ?? 'a_valider';

/* ------------------------------------------------------------
   ACTION : valider un article (brouillon -> publie)
------------------------------------------------------------- */
if (isset($_GET['valider'])) {
    $id_article = (int)$_GET['valider'];
    $stmt = $pdo->prepare("UPDATE article SET statut = 'publie' WHERE id_article = :id AND statut = 'brouillon'");
    $stmt->execute([':id' => $id_article]);
    header('Location: validation-articles.php?onglet=a_valider&succes=valide');
    exit;
}

/* ------------------------------------------------------------
   ACTION : refuser un article (brouillon -> archive)
------------------------------------------------------------- */
if (isset($_GET['refuser'])) {
    $id_article = (int)$_GET['refuser'];
    $stmt = $pdo->prepare("UPDATE article SET statut = 'archive' WHERE id_article = :id AND statut = 'brouillon'");
    $stmt->execute([':id' => $id_article]);
    header('Location: validation-articles.php?onglet=a_valider&succes=refuse');
    exit;
}

if (isset($_GET['succes'])) {
    $messages = [
        'valide'  => "Article validé : il est maintenant visible sur le site public.",
        'refuse'  => "Article refusé : il reste invisible du public.",
    ];
    $succes = $messages[$_GET['succes']] ?? '';
}

/* ------------------------------------------------------------
   Récupération des articles selon l'onglet actif
------------------------------------------------------------- */
if ($onglet === 'traites') {
    $stmt = $pdo->query("
        SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
        FROM article a
        LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
        WHERE a.statut IN ('publie', 'archive')
        ORDER BY a.derniere_modification DESC
        LIMIT 100
    ");
} else {
    $stmt = $pdo->query("
        SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
        FROM article a
        LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
        WHERE a.statut = 'brouillon'
        ORDER BY a.date_publication ASC
    ");
}
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

function e(string $v = null): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Validation des articles — Urgences Antsiranana</title>
<link rel="stylesheet" href="../asset/icon/fontAwesome/all.min.css">
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
  body{margin:0; font-family:'Inter',system-ui,sans-serif; background:var(--fond); color:#1A2530;}
  main{max-width:960px; margin:0 auto; padding:28px 20px 60px;}
  h1{font-family:'Poppins',sans-serif; font-size:1.5rem; margin:0 0 4px; color:var(--bleu-nuit);}
  p.souscription{color:var(--gris-texte); margin:0 0 24px; font-size:0.95rem;}
  nav.onglets{display:flex; gap:8px; margin-bottom:22px; border-bottom:1px solid var(--gris-bord);}
  nav.onglets a{
    text-decoration:none; color:var(--gris-texte); font-weight:600; font-size:0.92rem;
    padding:10px 18px; border-radius:8px 8px 0 0; position:relative; top:1px;
  }
  nav.onglets a.actif{
    color:var(--bleu); background:var(--blanc); border:1px solid var(--gris-bord); border-bottom:1px solid var(--blanc);
  }
  .alerte{padding:12px 16px; border-radius:8px; font-size:0.9rem; margin-bottom:18px;}
  .alerte.succes{background:#E9F7EF; color:var(--vert-ok); border:1px solid #B7E4C7;}
  .liste-articles{display:flex; flex-direction:column; gap:14px;}
  .article-item{background:var(--blanc); border:1px solid var(--gris-bord); border-radius:var(--radius); padding:18px 20px;}
  .article-item .ligne-haut{display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap;}
  .article-item h3{margin:0 0 6px; font-size:1.02rem; color:var(--bleu-nuit);}
  .badge{
    font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.4px;
    padding:3px 10px; border-radius:20px; white-space:nowrap;
  }
  .badge.brouillon{background:#FFF4E0; color:#B4700B;}
  .badge.publie{background:#E9F7EF; color:var(--vert-ok);}
  .badge.archive{background:#EEF0F3; color:#65707D;}
  .article-item .contenu-apercu{
    color:var(--gris-texte); font-size:0.88rem; margin:8px 0 10px; line-height:1.5;
    max-height:3.2em; overflow:hidden;
  }
  .article-item .meta{
    font-size:0.78rem; color:#8A94A3; display:flex; gap:16px; flex-wrap:wrap; margin-bottom:12px;
  }
  .article-item .meta a{color:var(--bleu); text-decoration:none;}
  .article-item .actions{display:flex; gap:10px;}
  .article-item .actions a{
    font-size:0.82rem; font-weight:600; text-decoration:none; padding:7px 14px; border-radius:6px;
  }
  .action-valider{color:var(--vert-ok); background:#E9F7EF;}
  .action-refuser{color:var(--rouge-urgence); background:#FDECEA;}
  .etat-vide{text-align:center; padding:50px 20px; color:#8A94A3;}
</style>
</head>
<body>
<main>
  <h1>Validation des articles</h1>
  <p class="souscription">Valide ou refuse les articles soumis par les rédacteurs avant qu'ils n'apparaissent sur le site public.</p>

  <nav class="onglets">
    <a href="validation-articles.php?onglet=a_valider" class="<?= $onglet === 'a_valider' ? 'actif' : '' ?>">À valider</a>
    <a href="validation-articles.php?onglet=traites" class="<?= $onglet === 'traites' ? 'actif' : '' ?>">Déjà traités</a>
  </nav>

  <?php if ($succes): ?>
    <div class="alerte succes"><?= e($succes) ?></div>
  <?php endif; ?>

  <?php if (empty($articles)): ?>
    <div class="etat-vide">
      <?= $onglet === 'traites' ? "Aucun article traité pour le moment." : "Aucun article en attente de validation." ?>
    </div>
  <?php else: ?>
    <div class="liste-articles">
      <?php foreach ($articles as $art): ?>
        <div class="article-item">
          <div class="ligne-haut">
            <h3><?= e($art['titre']) ?></h3>
            <span class="badge <?= e($art['statut']) ?>"><?= e($art['statut']) ?></span>
          </div>
          <div class="contenu-apercu"><?= e(mb_strimwidth(strip_tags($art['contenu']), 0, 220, '…')) ?></div>
          <div class="meta">
            <span>Par <?= e(trim(($art['auteur_prenom'] ?? '') . ' ' . ($art['auteur_nom'] ?? '')) ?: 'Auteur inconnu') ?></span>
            <span>Soumis le <?= date('d/m/Y à H:i', strtotime($art['date_publication'])) ?></span>
            <?php if (!empty($art['lien_source'])): ?>
              <a href="<?= e($art['lien_source']) ?>" target="_blank" rel="noopener">Voir la publication Facebook ↗</a>
            <?php endif; ?>
          </div>
          <?php if ($art['statut'] === 'brouillon'): ?>
            <div class="actions">
              <a href="validation-articles.php?valider=<?= (int)$art['id_article'] ?>" class="action-valider">Valider</a>
              <a href="validation-articles.php?refuser=<?= (int)$art['id_article'] ?>"
                 class="action-refuser"
                 onclick="return confirm('Refuser cet article ? Il restera invisible du public.');">Refuser</a>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
</body>
</html>
