<?php
/**
 * admin_utilisateurs.php
 * -------------------------------------------------------------
 * Gestion des utilisateurs de la plateforme (table `utilisateur`).
 * Les utilisateurs sont répartis par rôle (table `role`), affichés
 * via des onglets (paramètre GET "role"), comme pour les services.
 * -------------------------------------------------------------
 */

require_once '../includes/admin_session.php';
require_once '../includes/db_connect.php';
require_once '../includes/fonction.php';

$erreur = '';
$succes = '';

/* -------------------------------------------------------------
 * Liste des rôles (pour générer les onglets dynamiquement)
 * ----------------------------------------------------------- */
$roles = $pdo->query("SELECT * FROM role ORDER BY id_role")->fetchAll(PDO::FETCH_ASSOC);

if (empty($roles)) {
    die("Aucun rôle configuré dans la table role.");
}

// Rôle actuellement sélectionné (onglet actif) via ?role=ID
$id_role_actif = isset($_GET['role']) ? (int)$_GET['role'] : (int)$roles[0]['id_role'];

// Vérifie que le rôle demandé existe bien
$role_valide = false;
foreach ($roles as $r) {
    if ((int)$r['id_role'] === $id_role_actif) {
        $role_valide = true;
        break;
    }
}
if (!$role_valide) {
    $id_role_actif = (int)$roles[0]['id_role'];
}

/* -------------------------------------------------------------
 * Liste des quartiers (pour le select dans les formulaires)
 * ----------------------------------------------------------- */
$quartiers = $pdo->query("SELECT * FROM quartier ORDER BY nom_quartier")->fetchAll(PDO::FETCH_ASSOC);

/* -------------------------------------------------------------
 * ID de l'utilisateur actuellement connecté (pour empêcher
 * l'auto-suppression / auto-désactivation depuis cette page)
 * ----------------------------------------------------------- */
$id_utilisateur_connecte = $_SESSION['id_utilisateur'] ?? null;

/* -------------------------------------------------------------
 * Traitement des actions
 * ----------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // --- Ajout d'un utilisateur ---
    if ($action === 'ajouter') {
        $nom            = trim($_POST['nom'] ?? '');
        $prenom         = trim($_POST['prenom'] ?? '');
        $date_naissance = trim($_POST['date_naissance'] ?? '');
        $id_quartier    = (int)($_POST['id_quartier'] ?? 0);
        $email          = trim($_POST['email'] ?? '');
        $mot_de_passe   = trim($_POST['mot_de_passe'] ?? '');
        $id_role        = (int)($_POST['id_role'] ?? $id_role_actif);

        if ($nom === '' || $prenom === '' || $email === '' || $mot_de_passe === '') {
            $erreur = "Merci de renseigner le nom, le prénom, l'email et le mot de passe.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = "L'adresse email n'est pas valide.";
        } else {
            // Vérifie l'unicité de l'email
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
            $stmt_check->execute(['email' => $email]);

            if ($stmt_check->fetchColumn() > 0) {
                $erreur = "Cet email est déjà utilisé par un autre utilisateur.";
            } else {
                $hash = hash('sha256', $mot_de_passe);

                $stmt = $pdo->prepare("
                    INSERT INTO utilisateur (nom, prenom, date_naissance, id_quartier, email, mot_de_passe, id_role)
                    VALUES (:nom, :prenom, :date_naissance, :id_quartier, :email, :mot_de_passe, :id_role)
                ");
                $stmt->execute([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'date_naissance' => $date_naissance !== '' ? $date_naissance : null,
                    'id_quartier' => $id_quartier > 0 ? $id_quartier : null,
                    'email' => $email,
                    'mot_de_passe' => $hash,
                    'id_role' => $id_role,
                ]);
                $succes = "Utilisateur ajouté avec succès.";
                $id_role_actif = $id_role; // reste sur le bon onglet après ajout
            }
        }
    }

    // --- Modification d'un utilisateur ---
    if ($action === 'modifier' && isset($_POST['id_utilisateur'])) {
        $id_utilisateur = (int)$_POST['id_utilisateur'];
        $nom            = trim($_POST['nom'] ?? '');
        $prenom         = trim($_POST['prenom'] ?? '');
        $date_naissance = trim($_POST['date_naissance'] ?? '');
        $id_quartier    = (int)($_POST['id_quartier'] ?? 0);
        $email          = trim($_POST['email'] ?? '');
        $mot_de_passe   = trim($_POST['mot_de_passe'] ?? '');

        if ($nom === '' || $prenom === '' || $email === '') {
            $erreur = "Merci de renseigner le nom, le prénom et l'email pour la modification.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = "L'adresse email n'est pas valide.";
        } else {
            // Vérifie l'unicité de l'email (hors utilisateur courant)
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email AND id_utilisateur != :id");
            $stmt_check->execute(['email' => $email, 'id' => $id_utilisateur]);

            if ($stmt_check->fetchColumn() > 0) {
                $erreur = "Cet email est déjà utilisé par un autre utilisateur.";
            } else {
                if ($mot_de_passe !== '') {
                    // Un nouveau mot de passe a été saisi : on le met à jour aussi
                    $hash = hash('sha256', $mot_de_passe);
                    $stmt = $pdo->prepare("
                        UPDATE utilisateur
                        SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance,
                            id_quartier = :id_quartier, email = :email, mot_de_passe = :mot_de_passe
                        WHERE id_utilisateur = :id
                    ");
                    $stmt->execute([
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'date_naissance' => $date_naissance !== '' ? $date_naissance : null,
                        'id_quartier' => $id_quartier > 0 ? $id_quartier : null,
                        'email' => $email,
                        'mot_de_passe' => $hash,
                        'id' => $id_utilisateur,
                    ]);
                } else {
                    // Pas de nouveau mot de passe : on ne touche pas à la colonne
                    $stmt = $pdo->prepare("
                        UPDATE utilisateur
                        SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance,
                            id_quartier = :id_quartier, email = :email
                        WHERE id_utilisateur = :id
                    ");
                    $stmt->execute([
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'date_naissance' => $date_naissance !== '' ? $date_naissance : null,
                        'id_quartier' => $id_quartier > 0 ? $id_quartier : null,
                        'email' => $email,
                        'id' => $id_utilisateur,
                    ]);
                }
                $succes = "Utilisateur modifié avec succès.";
            }
        }
    }

    // --- Suppression d'un utilisateur ---
    if ($action === 'supprimer' && isset($_POST['id_utilisateur'])) {
        $id_utilisateur = (int)$_POST['id_utilisateur'];

        if ($id_utilisateur_connecte !== null && $id_utilisateur === (int)$id_utilisateur_connecte) {
            $erreur = "Vous ne pouvez pas supprimer votre propre compte.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
            $stmt->execute(['id' => $id_utilisateur]);
            $succes = "Utilisateur supprimé.";
        }
    }

    // --- Activation / désactivation d'un utilisateur ---
    if ($action === 'toggle' && isset($_POST['id_utilisateur'])) {
        $id_utilisateur = (int)$_POST['id_utilisateur'];

        if ($id_utilisateur_connecte !== null && $id_utilisateur === (int)$id_utilisateur_connecte) {
            $erreur = "Vous ne pouvez pas désactiver votre propre compte.";
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateur SET actif = 1 - actif WHERE id_utilisateur = :id");
            $stmt->execute(['id' => $id_utilisateur]);
            $succes = "Statut mis à jour.";
        }
    }

    // --- Changement de rôle d'un utilisateur ---
    if ($action === 'changer_role' && isset($_POST['id_utilisateur'], $_POST['nouveau_role'])) {
        $stmt = $pdo->prepare("UPDATE utilisateur SET id_role = :id_role WHERE id_utilisateur = :id");
        $stmt->execute([
            'id_role' => (int)$_POST['nouveau_role'],
            'id' => (int)$_POST['id_utilisateur'],
        ]);
        $succes = "Rôle mis à jour.";
    }
}

/* -------------------------------------------------------------
 * Récupération des utilisateurs du rôle actuellement affiché
 * ----------------------------------------------------------- */
$stmt_utilisateurs = $pdo->prepare("
    SELECT u.*, q.nom_quartier
    FROM utilisateur u
    LEFT JOIN quartier q ON u.id_quartier = q.id_quartier
    WHERE u.id_role = :id_role
    ORDER BY u.nom, u.prenom
");
$stmt_utilisateurs->execute(['id_role' => $id_role_actif]);
$utilisateurs = $stmt_utilisateurs->fetchAll(PDO::FETCH_ASSOC);

// Icônes et classes couleur par rôle
$icones_role = [
    'Administrateur' => 'fa-user-shield',
    'Redacteur' => 'fa-pen-nib',
    'Visiteur' => 'fa-user',
];

$classes_role = [
    'Administrateur' => 'onglet-admin',
    'Redacteur' => 'onglet-redacteur',
    'Visiteur' => 'onglet-visiteur',
];

include '../includes/admin_header.php';
?>
<title>Gestion des Utilisateurs</title>

<main class="admin-content">
    <div class="admin-container">
        <div class="admin-content">
            <header>
                <h1>Gestion des Utilisateurs</h1>
                <p>Ajouter, modifier, supprimer ou changer le rôle des utilisateurs de la plateforme.</p>
            </header>

            <?php if ($erreur): ?><div class="alert erreur"><?php echo htmlspecialchars($erreur); ?></div><?php endif; ?>
            <?php if ($succes): ?><div class="alert success"><?php echo htmlspecialchars($succes); ?></div><?php endif; ?>

            <!-- Onglets par rôle -->
            <div class="onglets">
                <?php foreach ($roles as $r):
                    $icone = $icones_role[$r['nom_role']] ?? 'fa-user';
                    $classe_couleur = $classes_role[$r['nom_role']] ?? 'onglet-defaut';
                    $actif = ((int)$r['id_role'] === $id_role_actif) ? 'actif' : '';
                ?>
                    <a href="?role=<?php echo (int)$r['id_role']; ?>" class="onglet-btn <?php echo $classe_couleur; ?> <?php echo $actif; ?>">
                        <i class="fas <?php echo $icone; ?>"></i> <?php echo htmlspecialchars($r['nom_role']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Formulaire d'ajout -->
            <section class="add-pharmacy-section">
                <h2>Ajouter un(e) <?php echo htmlspecialchars(array_values(array_filter($roles, fn($r) => (int)$r['id_role'] === $id_role_actif))[0]['nom_role']); ?></h2>

                <form method="post">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="id_role" value="<?php echo $id_role_actif; ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" placeholder="Ex : Razakaria" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" placeholder="Ex : Brenda Anissa" required>
                        </div>
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance (optionnel)</label>
                            <input type="date" id="date_naissance" name="date_naissance">
                        </div>
                        <div class="form-group">
                            <label for="id_quartier">Quartier (optionnel)</label>
                            <select id="id_quartier" name="id_quartier">
                                <option value="">-- Choisir un quartier --</option>
                                <?php foreach ($quartiers as $q): ?>
                                    <option value="<?php echo (int)$q['id_quartier']; ?>"><?php echo htmlspecialchars($q['nom_quartier']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Ex : nom@exemple.com" required>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary mt-30">Ajouter</button>
                </form>
            </section>

            <!-- Liste des utilisateurs -->
            <section class="pharmacy-list-section">
                <h2>Liste actuelle</h2>

                <?php if (empty($utilisateurs)): ?>
                    <p class="empty-state">Aucun utilisateur enregistré pour ce rôle.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Quartier</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th class="actions-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($utilisateurs as $u): ?>
                                <tr>
                                    <td data-label="Nom"><?php echo htmlspecialchars($u['nom']); ?></td>
                                    <td data-label="Prénom"><?php echo htmlspecialchars($u['prenom']); ?></td>
                                    <td data-label="Email" style="word-break:break-word;"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td data-label="Quartier"><?php echo htmlspecialchars($u['nom_quartier'] ?? '—'); ?></td>
                                    <td data-label="Rôle">
                                        <form method="post" class="inline-form role-select-form">
                                            <input type="hidden" name="action" value="changer_role">
                                            <input type="hidden" name="id_utilisateur" value="<?php echo (int)$u['id_utilisateur']; ?>">
                                            <select name="nouveau_role" class="role-select" onchange="this.form.submit()">
                                                <?php foreach ($roles as $r): ?>
                                                    <option value="<?php echo (int)$r['id_role']; ?>" <?php echo ((int)$r['id_role'] === (int)$u['id_role']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($r['nom_role']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td data-label="Statut">
                                        <?php if ($u['actif']): ?>
                                            <span class="etat-actif">Actif</span>
                                        <?php else: ?>
                                            <span class="etat-inactif">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell" data-label="Actions">
                                        <button
                                            type="button"
                                            class="edit-btn"
                                            data-id_utilisateur="<?php echo htmlspecialchars($u['id_utilisateur']); ?>"
                                            data-nom="<?php echo htmlspecialchars($u['nom']); ?>"
                                            data-prenom="<?php echo htmlspecialchars($u['prenom']); ?>"
                                            data-date_naissance="<?php echo htmlspecialchars($u['date_naissance'] ?? ''); ?>"
                                            data-id_quartier="<?php echo htmlspecialchars($u['id_quartier'] ?? ''); ?>"
                                            data-email="<?php echo htmlspecialchars($u['email']); ?>"
                                        >
                                            Modifier
                                        </button>

                                        <?php if ($id_utilisateur_connecte === null || (int)$u['id_utilisateur'] !== (int)$id_utilisateur_connecte): ?>
                                            <form method="post" class="inline-form">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id_utilisateur" value="<?php echo (int)$u['id_utilisateur']; ?>">
                                                <button type="submit" class="btn-toggle"><?php echo $u['actif'] ? 'Désactiver' : 'Activer'; ?></button>
                                            </form>

                                            <form method="post" class="inline-form" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                <input type="hidden" name="action" value="supprimer">
                                                <input type="hidden" name="id_utilisateur" value="<?php echo (int)$u['id_utilisateur']; ?>">
                                                <button type="submit" class="btn-danger">Supprimer</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge-vous">Vous</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>

<!-- Modal de modification -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier l'utilisateur</h2>

        <form method="post">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" id="edit_id_utilisateur" name="id_utilisateur">

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_nom">Nom</label>
                    <input type="text" id="edit_nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="edit_prenom">Prénom</label>
                    <input type="text" id="edit_prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="edit_date_naissance">Date de naissance</label>
                    <input type="date" id="edit_date_naissance" name="date_naissance">
                </div>
                <div class="form-group">
                    <label for="edit_id_quartier">Quartier</label>
                    <select id="edit_id_quartier" name="id_quartier">
                        <option value="">-- Choisir un quartier --</option>
                        <?php foreach ($quartiers as $q): ?>
                            <option value="<?php echo (int)$q['id_quartier']; ?>"><?php echo htmlspecialchars($q['nom_quartier']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group full-width">
                    <label for="edit_mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" id="edit_mot_de_passe" name="mot_de_passe" placeholder="••••••••" autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-primary mt-30">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<style>
    :root {
        --primary: #2c7a7b;
        --primary-dark: #1f5b5c;
        --danger: #e74c3c;
        --danger-dark: #c0392b;
        --success-bg: #d4edda;
        --success-text: #155724;
        --success-border: #c3e6cb;
        --erreur-bg: #fdecea;
        --erreur-text: #a12b2b;
        --border-color: #dcdfe3;
        --text-main: #2d3436;
        --text-muted: #6b7280;
        --radius: 8px;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-lg: 0 10px 30px rgba(0,0,0,0.2);
    }

    .admin-content { width: 100%; max-width: 1100px; margin: 0 auto; padding: 20px; box-sizing: border-box; color: var(--text-main); }
    .admin-container, .add-pharmacy-section, .pharmacy-list-section { box-sizing: border-box; }
    .admin-content header h1 { margin: 0 0 5px; font-size: 1.8rem; }
    .admin-content header p { margin: 0 0 25px; color: var(--text-muted); }

    .alert { padding: 14px 18px; margin-bottom: 20px; border-radius: var(--radius); font-weight: 600; border: 1px solid transparent; }
    .alert.success { background-color: var(--success-bg); color: var(--success-text); border-color: var(--success-border); }
    .alert.erreur { background-color: var(--erreur-bg); color: var(--erreur-text); border-color: #f5c6cb; }

    .onglets { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .onglet-btn {
        padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;
        background: #f3f4f6; font-weight: 600; color: var(--text-main); text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px; font-size: 0.9rem;
        transition: background-color 0.15s ease;
    }
    .onglet-btn:hover { background: #e5e7eb; }
    .onglet-btn.actif { color: #fff; }

    .add-pharmacy-section, .pharmacy-list-section {
        background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius);
        box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px;
    }
    .add-pharmacy-section h2, .pharmacy-list-section h2 { margin-top: 0; margin-bottom: 20px; font-size: 1.3rem; border-bottom: 2px solid var(--primary); padding-bottom: 10px; }
    .empty-state { color: var(--text-muted); font-style: italic; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: var(--text-main); }
    .form-group input, .form-group select {
        padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; width: 100%;
        font-size: 0.95rem; font-family: inherit; transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(44, 122, 123, 0.15);
    }
    .full-width { grid-column: 1 / -1; }

    .btn-primary {
        display: inline-block; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer;
        font-weight: 600; font-size: 0.9rem; background-color: var(--primary); color: #fff;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-primary:hover { background-color: var(--primary-dark); }
    .btn-primary:active { transform: scale(0.97); }

    .btn-danger {
        border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
        background-color: var(--danger); color: white; transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-danger:hover { background-color: var(--danger-dark); }
    .btn-danger:active { transform: scale(0.97); }

    .btn-toggle {
        border: 1px solid var(--border-color); background: #f3f4f6; color: var(--text-main);
        padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-toggle:hover { background-color: #e5e7eb; }
    .btn-toggle:active { transform: scale(0.97); }

    .edit-btn {
        background-color: var(--primary); color: #fff; border: none; padding: 8px 14px; border-radius: 6px;
        cursor: pointer; font-weight: 600; font-size: 0.85rem; margin-right: 6px;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .edit-btn:hover { background-color: var(--primary-dark); }
    .edit-btn:active { transform: scale(0.97); }

    .inline-form { display: inline-block; }
    .mt-30 { margin-top: 30px; }

    .etat-actif { color: #1a7a2e; font-weight: 700; }
    .etat-inactif { color: #a12b2b; font-weight: 700; }

    .badge-vous {
        display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
        background-color: #eef1f4; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em;
    }

    .role-select-form { display: inline-block; }
    .role-select {
        padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem;
        font-family: inherit; cursor: pointer; background-color: #fff; color: var(--text-main);
    }
    .role-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(44, 122, 123, 0.15); }

    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid var(--border-color); border-radius: var(--radius); }
    .table-scroll table { min-width: 900px; width: 100%; border-collapse: collapse; }
    .table-scroll th, .table-scroll td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; vertical-align: top; }
    .table-scroll th { background-color: #f7f8f9; font-weight: 700; color: var(--text-main); text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.03em; }
    .table-scroll tbody tr:hover { background-color: #fafbfc; }
    .table-scroll tbody tr:last-child td { border-bottom: none; }
    .actions-cell { white-space: nowrap; }

    /* ==================================================== */
    /* Couleurs par rôle (couleur pleine par défaut)          */
    /* ==================================================== */

    .onglet-admin,
    .onglet-redacteur {
        border: none;
    }

    /* Administrateur = bleu */
    .onglet-admin { background: #2563eb; color: #fff; }
    .onglet-admin:hover { background: #1d4ed8; color: #fff; }
    .onglet-admin.actif { background: #1d4ed8; color: #fff; box-shadow: inset 0 0 0 2px #1e3a8a; }

    /* Rédacteur = vert */
    .onglet-redacteur { background: #2c7a7b; color: #fff; }
    .onglet-redacteur:hover { background: #1f5b5c; color: #fff; }
    .onglet-redacteur.actif { background: #1f5b5c; color: #fff; box-shadow: inset 0 0 0 2px #163e3f; }

    /* Visiteur = gris */
    .onglet-visiteur { background: #6b7280; color: #fff; border: none; }
    .onglet-visiteur:hover { background: #4b5563; color: #fff; }
    .onglet-visiteur.actif { background: #4b5563; color: #fff; box-shadow: inset 0 0 0 2px #374151; }

    @media (max-width: 900px) {
        .admin-content { padding: 16px; }
        .add-pharmacy-section, .pharmacy-list-section { padding: 20px; }
        .form-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
    }
    @media (max-width: 700px) {
        .form-grid { grid-template-columns: 1fr; }
        .admin-content header h1 { font-size: 1.5rem; }
        .onglets { flex-direction: column; }
    }
    @media (max-width: 600px) {
        .admin-content { padding: 12px; }
        .add-pharmacy-section, .pharmacy-list-section { padding: 16px; margin-bottom: 20px; }
        .btn-primary { width: 100%; }

        .table-scroll { overflow-x: visible; border: none; border-radius: 0; }
        .table-scroll table { min-width: 0; width: 100%; }
        .table-scroll thead { display: none; }
        .table-scroll tbody, .table-scroll tr, .table-scroll td { display: block; width: 100%; }
        .table-scroll tr {
            background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius);
            margin-bottom: 14px; padding: 10px 14px; box-shadow: var(--shadow);
        }
        .table-scroll td {
            border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between;
            align-items: flex-start; gap: 12px; text-align: right;
        }
        .table-scroll td:last-child { border-bottom: none; }
        .table-scroll td::before {
            content: attr(data-label); font-weight: 700; font-size: 0.78rem; text-transform: uppercase;
            letter-spacing: 0.03em; color: var(--text-muted); text-align: left; flex-shrink: 0;
        }
        .actions-cell { flex-direction: column; align-items: stretch !important; gap: 8px !important; }
        .actions-cell::before { margin-bottom: 4px; }
        .edit-btn, .btn-toggle, .inline-form, .inline-form .btn-danger { width: 100%; }
        .inline-form { display: block; }
        .role-select { width: 100%; }
    }

    .modal {
        display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
        overflow: auto; background-color: rgba(0,0,0,0.45);
    }
    .modal-content {
        background-color: #fefefe; margin: 6% auto; padding: 30px; border: none; width: 90%; max-width: 700px;
        max-height: 85vh; overflow-y: auto; border-radius: 10px; position: relative; box-shadow: var(--shadow-lg);
        box-sizing: border-box;
    }
    @media (max-width: 600px) {
        .modal-content { width: 100%; max-width: 100%; height: 100%; max-height: 100%; margin: 0; border-radius: 0; padding: 20px; }
        .modal-content .form-grid { grid-template-columns: 1fr; }
    }
    .modal-content h2 { margin-top: 0; border-bottom: 2px solid var(--primary); padding-bottom: 10px; }
    .close-button {
        color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px;
        line-height: 1; cursor: pointer;
    }
    .close-button:hover, .close-button:focus { color: var(--danger); text-decoration: none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById("editUserModal");
    var closeBtn = document.querySelector('.close-button');

    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('edit_id_utilisateur').value = this.dataset.id_utilisateur;
            document.getElementById('edit_nom').value = this.dataset.nom;
            document.getElementById('edit_prenom').value = this.dataset.prenom;
            document.getElementById('edit_date_naissance').value = this.dataset.date_naissance;
            document.getElementById('edit_id_quartier').value = this.dataset.id_quartier;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_mot_de_passe').value = '';
            modal.style.display = "block";
        });
    });

    closeBtn.onclick = function () { modal.style.display = "none"; };
    window.onclick = function (event) {
        if (event.target === modal) { modal.style.display = "none"; }
    };
});
</script>

<?php include '../includes/admin_footer.php'; ?>