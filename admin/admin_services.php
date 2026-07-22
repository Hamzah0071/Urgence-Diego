<?php
/**
 * admin_services.php
 * -------------------------------------------------------------
 * Gestion unifiée de tous les services d'urgence stockés dans la
 * table `service` (Pharmacies, Pompiers, Force de l'ordre, Hôpitaux).
 * Le type affiché est choisi via des onglets (paramètre GET "type"),
 * qui correspondent aux lignes de la table `type_service`.
 * -------------------------------------------------------------
 */

require_once '../includes/admin_session.php';

$erreur = '';
$succes = '';

/* -------------------------------------------------------------
 * Liste des types de service (pour générer les onglets dynamiquement)
 * ----------------------------------------------------------- */
$types = $pdo->query("SELECT * FROM type_service ORDER BY nom_type")->fetchAll(PDO::FETCH_ASSOC);

if (empty($types)) {
    die("Aucun type de service configuré dans la table type_service.");
}

// Type actuellement sélectionné (onglet actif) via ?type=ID
$id_type_actif = isset($_GET['type']) ? (int)$_GET['type'] : (int)$types[0]['id_type'];

// Vérifie que le type demandé existe bien
$type_valide = false;
foreach ($types as $t) {
    if ((int)$t['id_type'] === $id_type_actif) {
        $type_valide = true;
        break;
    }
}
if (!$type_valide) {
    $id_type_actif = (int)$types[0]['id_type'];
}

/* -------------------------------------------------------------
 * Liste des quartiers (pour le select dans les formulaires)
 * ----------------------------------------------------------- */
$quartiers = $pdo->query("SELECT * FROM quartier ORDER BY nom_quartier")->fetchAll(PDO::FETCH_ASSOC);

/* -------------------------------------------------------------
 * Traitement des actions
 * ----------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // --- Ajout d'un service ---
    if ($action === 'ajouter') {
        $libelle    = trim($_POST['libelle'] ?? '');
        $telephone  = trim($_POST['telephone'] ?? '');
        $adresse    = trim($_POST['adresse'] ?? '');
        $id_quartier = (int)($_POST['id_quartier'] ?? 0);
        $id_type    = (int)($_POST['id_type'] ?? $id_type_actif);
        $description = trim($_POST['description'] ?? '');

        if ($libelle === '' || $telephone === '' || $adresse === '' || $id_quartier === 0) {
            $erreur = "Merci de renseigner le nom, le téléphone, l'adresse et le quartier.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO service (libelle, telephone, adresse, id_quartier, id_type, description)
                VALUES (:libelle, :telephone, :adresse, :id_quartier, :id_type, :description)
            ");
            $stmt->execute([
                'libelle' => $libelle,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'id_quartier' => $id_quartier,
                'id_type' => $id_type,
                'description' => $description !== '' ? $description : null,
            ]);
            $succes = "Service ajouté avec succès.";
            $id_type_actif = $id_type; // reste sur le bon onglet après ajout
        }
    }

    // --- Modification d'un service ---
    if ($action === 'modifier' && isset($_POST['id_service'])) {
        $libelle    = trim($_POST['libelle'] ?? '');
        $telephone  = trim($_POST['telephone'] ?? '');
        $adresse    = trim($_POST['adresse'] ?? '');
        $id_quartier = (int)($_POST['id_quartier'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($libelle === '' || $telephone === '' || $adresse === '' || $id_quartier === 0) {
            $erreur = "Merci de renseigner le nom, le téléphone, l'adresse et le quartier pour la modification.";
        } else {
            $stmt = $pdo->prepare("
                UPDATE service
                SET libelle = :libelle, telephone = :telephone, adresse = :adresse,
                    id_quartier = :id_quartier, description = :description
                WHERE id_service = :id
            ");
            $stmt->execute([
                'libelle' => $libelle,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'id_quartier' => $id_quartier,
                'description' => $description !== '' ? $description : null,
                'id' => $_POST['id_service'],
            ]);
            $succes = "Service modifié avec succès.";
        }
    }

    // --- Suppression d'un service ---
    if ($action === 'supprimer' && isset($_POST['id_service'])) {
        $stmt = $pdo->prepare("DELETE FROM service WHERE id_service = :id");
        $stmt->execute(['id' => $_POST['id_service']]);
        $succes = "Service supprimé.";
    }

    // --- Activation / désactivation d'un service ---
    if ($action === 'toggle' && isset($_POST['id_service'])) {
        $stmt = $pdo->prepare("UPDATE service SET actif = 1 - actif WHERE id_service = :id");
        $stmt->execute(['id' => $_POST['id_service']]);
        $succes = "Statut mis à jour.";
    }
}

/* -------------------------------------------------------------
 * Récupération des services du type actuellement affiché
 * ----------------------------------------------------------- */
$stmt_services = $pdo->prepare("
    SELECT s.*, q.nom_quartier
    FROM service s
    LEFT JOIN quartier q ON s.id_quartier = q.id_quartier
    WHERE s.id_type = :id_type
    ORDER BY s.libelle
");
$stmt_services->execute(['id_type' => $id_type_actif]);
$services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

// Icônes par type (correspond aux libellés de ta table type_service)
$icones_type = [
    'Pharmacie' => 'fa-pills',
    'Pompier' => 'fa-fire-extinguisher',
    "Force de l'ordre" => 'fa-user-shield',
    'Hôpital' => 'fa-hospital',
];

// Classe couleur par type (pour les onglets)
$classes_type = [
    'Pharmacie' => 'onglet-pharmacie',
    'Pompier' => 'onglet-pompier',
    "Force de l'ordre" => 'onglet-police',
    'Hôpital' => 'onglet-hopital',
];

include '../includes/admin_header.php';
?>
<title>Services d'urgence</title>

<main class="admin-content">
    <div class="admin-container">
        <div class="admin-content">
            <header>
                <h1>Gestion des Services d'Urgence</h1>
                <p>Ajouter, modifier ou supprimer les pharmacies, pompiers, forces de l'ordre et hôpitaux.</p>
            </header>

            <?php if ($erreur): ?><div class="alert erreur"><?php echo htmlspecialchars($erreur); ?></div><?php endif; ?>
            <?php if ($succes): ?><div class="alert success"><?php echo htmlspecialchars($succes); ?></div><?php endif; ?>

            <!-- Onglets par type de service -->
            <div class="onglets">
                <?php foreach ($types as $t):
                    $icone = $icones_type[$t['nom_type']] ?? 'fa-building';
                    $classe_couleur = $classes_type[$t['nom_type']] ?? 'onglet-defaut';
                    $actif = ((int)$t['id_type'] === $id_type_actif) ? 'actif' : '';
                ?>
                    <a href="?type=<?php echo (int)$t['id_type']; ?>" class="onglet-btn <?php echo $classe_couleur; ?> <?php echo $actif; ?>">
                        <i class="fas <?php echo $icone; ?>"></i> <?php echo htmlspecialchars($t['nom_type']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Formulaire d'ajout -->
            <section class="add-pharmacy-section">
                <h2>Ajouter un(e) <?php echo htmlspecialchars(array_values(array_filter($types, fn($t) => (int)$t['id_type'] === $id_type_actif))[0]['nom_type']); ?></h2>

                <form method="post">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="id_type" value="<?php echo $id_type_actif; ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="libelle">Nom</label>
                            <input type="text" id="libelle" name="libelle" placeholder="Ex : Pharmacie Mora" required>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" placeholder="Ex : 032 78 826 04" required>
                        </div>
                        <div class="form-group full-width">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" placeholder="Ex : P7HR+5XW, Place Kabary, Antsiranana 201" required>
                        </div>
                        <div class="form-group">
                            <label for="id_quartier">Quartier</label>
                            <select id="id_quartier" name="id_quartier" required>
                                <option value="">-- Choisir un quartier --</option>
                                <?php foreach ($quartiers as $q): ?>
                                    <option value="<?php echo (int)$q['id_quartier']; ?>"><?php echo htmlspecialchars($q['nom_quartier']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label for="description">Description (optionnel)</label>
                            <input type="text" id="description" name="description" placeholder="Ex : Située à côté de BOA Tanambao Sud">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary mt-30">Ajouter</button>
                </form>
            </section>

            <!-- Liste des services -->
            <section class="pharmacy-list-section">
                <h2>Liste actuelle</h2>

                <?php if (empty($services)): ?>
                    <p class="empty-state">Aucun service enregistré pour ce type.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Téléphone</th>
                                    <th>Adresse</th>
                                    <th>Quartier</th>
                                    <th>Statut</th>
                                    <th class="actions-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $s): ?>
                                <tr>
                                    <td data-label="Nom"><?php echo htmlspecialchars($s['libelle']); ?></td>
                                    <td data-label="Téléphone"><?php echo htmlspecialchars($s['telephone']); ?></td>
                                    <td data-label="Adresse" style="word-break:break-word;"><?php echo htmlspecialchars($s['adresse']); ?></td>
                                    <td data-label="Quartier"><?php echo htmlspecialchars($s['nom_quartier'] ?? '—'); ?></td>
                                    <td data-label="Statut">
                                        <?php if ($s['actif']): ?>
                                            <span class="etat-actif">Actif</span>
                                        <?php else: ?>
                                            <span class="etat-inactif">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell" data-label="Actions">
                                        <button
                                            type="button"
                                            class="edit-btn"
                                            data-id_service="<?php echo htmlspecialchars($s['id_service']); ?>"
                                            data-libelle="<?php echo htmlspecialchars($s['libelle']); ?>"
                                            data-telephone="<?php echo htmlspecialchars($s['telephone']); ?>"
                                            data-adresse="<?php echo htmlspecialchars($s['adresse']); ?>"
                                            data-id_quartier="<?php echo htmlspecialchars($s['id_quartier']); ?>"
                                            data-description="<?php echo htmlspecialchars($s['description'] ?? ''); ?>"
                                        >
                                            Modifier
                                        </button>

                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id_service" value="<?php echo (int)$s['id_service']; ?>">
                                            <button type="submit" class="btn-toggle"><?php echo $s['actif'] ? 'Désactiver' : 'Activer'; ?></button>
                                        </form>

                                        <form method="post" class="inline-form" onsubmit="return confirm('Supprimer ce service ?');">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id_service" value="<?php echo (int)$s['id_service']; ?>">
                                            <button type="submit" class="btn-danger">Supprimer</button>
                                        </form>
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
<div id="editServiceModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier le service</h2>

        <form method="post">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" id="edit_id_service" name="id_service">

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_libelle">Nom</label>
                    <input type="text" id="edit_libelle" name="libelle" required>
                </div>
                <div class="form-group">
                    <label for="edit_telephone">Téléphone</label>
                    <input type="text" id="edit_telephone" name="telephone" required>
                </div>
                <div class="form-group full-width">
                    <label for="edit_adresse">Adresse</label>
                    <input type="text" id="edit_adresse" name="adresse" required>
                </div>
                <div class="form-group">
                    <label for="edit_id_quartier">Quartier</label>
                    <select id="edit_id_quartier" name="id_quartier" required>
                        <option value="">-- Choisir un quartier --</option>
                        <?php foreach ($quartiers as $q): ?>
                            <option value="<?php echo (int)$q['id_quartier']; ?>"><?php echo htmlspecialchars($q['nom_quartier']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="edit_description">Description (optionnel)</label>
                    <input type="text" id="edit_description" name="description">
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

    /* Règle générale pour l'état actif : seulement la couleur du texte.
       Le fond et la bordure sont gérés individuellement par chaque
       .onglet-XXX.actif ci-dessous pour éviter tout conflit de priorité CSS. */
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

    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid var(--border-color); border-radius: var(--radius); }
    .table-scroll table { min-width: 800px; width: 100%; border-collapse: collapse; }
    .table-scroll th, .table-scroll td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; vertical-align: top; }
    .table-scroll th { background-color: #f7f8f9; font-weight: 700; color: var(--text-main); text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.03em; }
    .table-scroll tbody tr:hover { background-color: #fafbfc; }
    .table-scroll tbody tr:last-child td { border-bottom: none; }
    .actions-cell { white-space: nowrap; }

    /* ==================================================== */
    /* Couleurs par type de service (couleur pleine par défaut) */
    /* ==================================================== */

    .onglet-pharmacie,
    .onglet-pompier,
    .onglet-police {
        border: none;
    }

    /* Pharmacie = vert */
    .onglet-pharmacie { background: #2c7a7b; color: #fff; }
    .onglet-pharmacie:hover { background: #1f5b5c; color: #fff; }
    .onglet-pharmacie.actif { background: #1f5b5c; color: #fff; box-shadow: inset 0 0 0 2px #163e3f; }

    /* Pompier = rouge */
    .onglet-pompier { background: #e74c3c; color: #fff; }
    .onglet-pompier:hover { background: #c0392b; color: #fff; }
    .onglet-pompier.actif { background: #c0392b; color: #fff; box-shadow: inset 0 0 0 2px #922b21; }

    /* Force de l'ordre = bleu */
    .onglet-police { background: #2563eb; color: #fff; }
    .onglet-police:hover { background: #1d4ed8; color: #fff; }
    .onglet-police.actif { background: #1d4ed8; color: #fff; box-shadow: inset 0 0 0 2px #1e3a8a; }

    /* Hôpital = blanc */
    .onglet-hopital { background: #ffffff; color: #2d3436; border: 1px solid #b0b4b8; }
    .onglet-hopital:hover { background: #f1f2f4; color: #000; border-color: #7a8a99; }
    .onglet-hopital.actif { background: #ffffff; color: #000; border: 1px solid #2d3436; box-shadow: inset 0 0 0 1px #2d3436; }

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
    var modal = document.getElementById("editServiceModal");
    var closeBtn = document.querySelector('.close-button');

    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('edit_id_service').value = this.dataset.id_service;
            document.getElementById('edit_libelle').value = this.dataset.libelle;
            document.getElementById('edit_telephone').value = this.dataset.telephone;
            document.getElementById('edit_adresse').value = this.dataset.adresse;
            document.getElementById('edit_id_quartier').value = this.dataset.id_quartier;
            document.getElementById('edit_description').value = this.dataset.description;
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