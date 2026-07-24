<?php
require_once '../includes/session.php';
// session.php redirige déjà vers login.php si non connecté,
// et fournit $pdo (via db_connect.php) ainsi que $_SESSION['id_utilisateur'].

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupération des infos de l'utilisateur connecté : nom, prénom, email,
// quartier (nom) et rôle (nom), via des jointures.
$stmt = $pdo->prepare("
    SELECT u.nom, u.prenom, u.email, u.date_naissance, u.date_creation,
           q.nom_quartier,
           r.nom_role
    FROM utilisateur u
    LEFT JOIN quartier q ON u.id_quartier = q.id_quartier
    LEFT JOIN role r ON u.id_role = r.id_role
    WHERE u.id_utilisateur = :id
");
$stmt->execute([':id' => $id_utilisateur]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profil) {
    die("Profil introuvable.");
}

function e(string $v = null): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Initiales pour l'avatar (ex : "Jean Dupont" -> "JD")
$initiales = mb_strtoupper(mb_substr($profil['prenom'] ?? '', 0, 1) . mb_substr($profil['nom'] ?? '', 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon profil — Urgences Antsiranana</title>
<link rel="stylesheet" href="../asset/icon/fontAwesome/all.min.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<style>
    :root {
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --blue-deep: #1e40af;
        --blue-deep-dark: #1e3a8a;
        --red-emergency: #dc2626;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --color-border: #e2e8f0;
    }
    body {
        margin: 0;
        background: var(--bg-light);
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
    }
    main.profil-page {
        max-width: 720px;
        margin: 0 auto;
        padding: 32px 24px 60px;
    }
    .profil-entete {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 28px;
    }
    .avatar {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--blue-deep) 0%, #3b82f6 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .profil-entete h1 {
        margin: 0 0 4px;
        font-size: 1.4rem;
        color: var(--text-dark);
    }
    .badge-role {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 3px 12px;
        border-radius: 20px;
        background: rgba(30, 64, 175, 0.1);
        color: var(--blue-deep);
    }
    .carte-profil {
        background: var(--bg-white);
        border: 1px solid var(--color-border);
        border-radius: 12px;
        overflow: hidden;
    }
    .ligne-info {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 22px;
        border-bottom: 1px solid var(--color-border);
    }
    .ligne-info:last-child {
        border-bottom: none;
    }
    .ligne-info i {
        width: 20px;
        color: var(--blue-deep);
        font-size: 0.95rem;
        text-align: center;
    }
    .ligne-info .libelle {
        font-size: 0.78rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 2px;
    }
    .ligne-info .valeur {
        font-size: 0.98rem;
        color: var(--text-dark);
        font-weight: 500;
    }
    .valeur.vide {
        color: var(--text-muted);
        font-weight: 400;
        font-style: italic;
    }
</style>

<main class="profil-page">
    <div class="profil-entete">
        <div class="avatar"><?= e($initiales) ?></div>
        <div>
            <h1><?= e($profil['prenom']) ?> <?= e($profil['nom']) ?></h1>
            <span class="badge-role"><?= e($profil['nom_role'] ?? 'Rôle non défini') ?></span>
        </div>
    </div>

    <div class="carte-profil">
        <div class="ligne-info">
            <i class="fa-solid fa-user"></i>
            <div>
                <div class="libelle">Prénom</div>
                <div class="valeur"><?= e($profil['prenom']) ?></div>
            </div>
        </div>
        <div class="ligne-info">
            <i class="fa-solid fa-id-card"></i>
            <div>
                <div class="libelle">Nom</div>
                <div class="valeur"><?= e($profil['nom']) ?></div>
            </div>
        </div>
        <div class="ligne-info">
            <i class="fa-solid fa-envelope"></i>
            <div>
                <div class="libelle">Email</div>
                <div class="valeur"><?= e($profil['email']) ?></div>
            </div>
        </div>
        <div class="ligne-info">
            <i class="fa-solid fa-location-dot"></i>
            <div>
                <div class="libelle">Quartier</div>
                <div class="valeur <?= empty($profil['nom_quartier']) ? 'vide' : '' ?>">
                    <?= $profil['nom_quartier'] ? e($profil['nom_quartier']) : 'Non renseigné' ?>
                </div>
            </div>
        </div>
        <div class="ligne-info">
            <i class="fa-solid fa-user-shield"></i>
            <div>
                <div class="libelle">Rôle</div>
                <div class="valeur"><?= e($profil['nom_role'] ?? 'Non défini') ?></div>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>