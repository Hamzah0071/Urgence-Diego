<?php
require_once '../includes/admin_session.php';
require_once '../includes/db_connect.php';

$message = '';

// Gérer l'ajout, la modification et la suppression de pharmacie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $nom_service = trim($_POST['nom_service']);
            $numero_telephone = trim($_POST['numero_telephone']);
            $adresse = trim($_POST['adresse']);
            $id_quartier = $_POST['id_quartier'];
            $description_specifique = trim($_POST['description_specifique']);
            $nom_pharmacien = trim($_POST['nom_pharmacien']);
            $id_categorie_pharmacie = 1; // ID pour la catégorie 'Pharmacie'

            if ($action === 'add') {
                // Insérer dans services_urgence
                $stmt_service = $pdo->prepare("INSERT INTO services_urgence (nom_service, numero_telephone, adresse, id_quartier, id_categorie, description_specifique) VALUES (:nom_service, :numero_telephone, :adresse, :id_quartier, :id_categorie, :description_specifique)");
                $stmt_service->execute([
                    'nom_service' => $nom_service,
                    'numero_telephone' => $numero_telephone,
                    'adresse' => $adresse,
                    'id_quartier' => $id_quartier,
                    'id_categorie' => $id_categorie_pharmacie,
                    'description_specifique' => $description_specifique !== '' ? $description_specifique : null
                ]);
                $id_service = $pdo->lastInsertId();

                // Insérer dans pharmacies
                $stmt_pharmacie = $pdo->prepare("INSERT INTO pharmacies (id_service, nom_pharmacien) VALUES (:id_service, :nom_pharmacien)");
                $stmt_pharmacie->execute([
                    'id_service' => $id_service,
                    'nom_pharmacien' => $nom_pharmacien !== '' ? $nom_pharmacien : null
                ]);
                $message = "Pharmacie ajoutée avec succès !";
            } elseif ($action === 'edit') {
                $id_pharmacie = $_POST['id_pharmacie'];
                $id_service = $_POST['id_service'];

                // Mettre à jour services_urgence
                $stmt_service = $pdo->prepare("UPDATE services_urgence SET nom_service = :nom_service, numero_telephone = :numero_telephone, adresse = :adresse, id_quartier = :id_quartier, description_specifique = :description_specifique WHERE id_service = :id_service");
                $stmt_service->execute([
                    'nom_service' => $nom_service,
                    'numero_telephone' => $numero_telephone,
                    'adresse' => $adresse,
                    'id_quartier' => $id_quartier,
                    'description_specifique' => $description_specifique !== '' ? $description_specifique : null,
                    'id_service' => $id_service
                ]);

                // Mettre à jour pharmacies
                $stmt_pharmacie = $pdo->prepare("UPDATE pharmacies SET nom_pharmacien = :nom_pharmacien WHERE id_pharmacie = :id_pharmacie");
                $stmt_pharmacie->execute([
                    'nom_pharmacien' => $nom_pharmacien !== '' ? $nom_pharmacien : null,
                    'id_pharmacie' => $id_pharmacie
                ]);
                $message = "Pharmacie mise à jour avec succès !";
            }
        }

        if ($action === 'delete') {
            $id_pharmacie = $_POST['id_pharmacie'];
            $id_service = $_POST['id_service'];

            // Supprimer de tours_garde (si lié)
            $stmt_delete_tours = $pdo->prepare("DELETE FROM tours_garde WHERE id_pharmacie = :id_pharmacie");
            $stmt_delete_tours->execute(['id_pharmacie' => $id_pharmacie]);

            // Supprimer de pharmacies
            $stmt_delete_pharmacie = $pdo->prepare("DELETE FROM pharmacies WHERE id_pharmacie = :id_pharmacie");
            $stmt_delete_pharmacie->execute(['id_pharmacie' => $id_pharmacie]);

            // Supprimer de services_urgence
            $stmt_delete_service = $pdo->prepare("DELETE FROM services_urgence WHERE id_service = :id_service");
            $stmt_delete_service->execute(['id_service' => $id_service]);

            $message = "Pharmacie supprimée avec succès !";
        }
    }
}

// Récupérer toutes les pharmacies avec leurs informations de service et de quartier
$stmt_pharmacies = $pdo->query("
    SELECT p.id_pharmacie, su.id_service, su.nom_service, su.numero_telephone, su.adresse, q.id_quartier, q.nom_quartier, su.description_specifique, p.nom_pharmacien
    FROM pharmacies p
    JOIN services_urgence su ON p.id_service = su.id_service
    JOIN quartiers q ON su.id_quartier = q.id_quartier
    ORDER BY su.nom_service
");
$pharmacies = $stmt_pharmacies->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des quartiers pour les formulaires
$quartiers = getQuartiers($pdo);

include '../includes/admin_header.php';

?>
<title>Pharmacies</title>

<main class="admin-content">
    <div class="admin-container">
        <div class="admin-content">
            <header>
                <h1>Gestion des Pharmacies</h1>
                <p>Ajouter, modifier ou supprimer des pharmacies de garde.</p>
            </header>

            <?php if ($message): ?>
                <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Formulaire d'ajout de pharmacie -->
            <section class="add-pharmacy-section">
                <h2>Ajouter une nouvelle pharmacie</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nom_service">Nom de la pharmacie</label>
                            <input type="text" id="nom_service" name="nom_service" required>
                        </div>

                        <div class="form-group">
                            <label for="numero_telephone">Numéro de téléphone</label>
                            <input type="text" id="numero_telephone" name="numero_telephone" required>
                        </div>

                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" required>
                        </div>

                        <div class="form-group">
                            <label for="id_quartier">Quartier</label>
                            <select id="id_quartier" name="id_quartier" required>
                                <option value="">-- Sélectionner un quartier --</option>
                                <?php foreach ($quartiers as $quartier): ?>
                                    <option value="<?php echo htmlspecialchars($quartier['id_quartier']); ?>">
                                        <?php echo htmlspecialchars($quartier['nom_quartier']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nom_pharmacien">Nom du pharmacien</label>
                            <input type="text" id="nom_pharmacien" name="nom_pharmacien">
                        </div>

                        <div class="form-group full-width">
                            <label for="description_specifique">Description / Remarques</label>
                            <textarea id="description_specifique" name="description_specifique" placeholder="Ex : Appelée aussi Issa dans le calendrier"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary mt-30">Ajouter la pharmacie</button>
                </form>
            </section>

            <!-- Liste des pharmacies -->
            <section class="pharmacy-list-section">
                <h2>Liste des pharmacies</h2>

                <?php if (count($pharmacies) === 0): ?>
                    <p class="empty-state">Aucune pharmacie enregistrée pour le moment.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Téléphone</th>
                                    <th>Adresse</th>
                                    <th>Quartier</th>
                                    <th>Pharmacien</th>
                                    <th>Description</th>
                                    <th class="actions-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pharmacies as $pharmacie): ?>
                                    <tr>
                                        <td data-label="Nom"><?php echo htmlspecialchars($pharmacie['nom_service']); ?></td>
                                        <td data-label="Téléphone"><?php echo htmlspecialchars($pharmacie['numero_telephone']); ?></td>
                                        <td data-label="Adresse"><?php echo htmlspecialchars($pharmacie['adresse']); ?></td>
                                        <td data-label="Quartier"><?php echo htmlspecialchars($pharmacie['nom_quartier']); ?></td>
                                        <td data-label="Pharmacien"><?php echo htmlspecialchars($pharmacie['nom_pharmacien'] ?? '—'); ?></td>
                                        <td data-label="Description"><?php echo htmlspecialchars($pharmacie['description_specifique'] ?? '—'); ?></td>
                                        <td class="actions-cell" data-label="Actions">
                                            <button
                                                type="button"
                                                class="edit-btn"
                                                data-id_pharmacie="<?php echo htmlspecialchars($pharmacie['id_pharmacie']); ?>"
                                                data-id_service="<?php echo htmlspecialchars($pharmacie['id_service']); ?>"
                                                data-nom_service="<?php echo htmlspecialchars($pharmacie['nom_service']); ?>"
                                                data-numero_telephone="<?php echo htmlspecialchars($pharmacie['numero_telephone']); ?>"
                                                data-adresse="<?php echo htmlspecialchars($pharmacie['adresse']); ?>"
                                                data-id_quartier="<?php echo htmlspecialchars($pharmacie['id_quartier']); ?>"
                                                data-description_specifique="<?php echo htmlspecialchars($pharmacie['description_specifique'] ?? ''); ?>"
                                                data-nom_pharmacien="<?php echo htmlspecialchars($pharmacie['nom_pharmacien'] ?? ''); ?>"
                                            >
                                                Modifier
                                            </button>

                                            <form method="POST" action="" class="inline-form" onsubmit="return confirm('Supprimer cette pharmacie ?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id_pharmacie" value="<?php echo htmlspecialchars($pharmacie['id_pharmacie']); ?>">
                                                <input type="hidden" name="id_service" value="<?php echo htmlspecialchars($pharmacie['id_service']); ?>">
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
<div id="editPharmacyModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier la pharmacie</h2>

        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id_pharmacie" name="id_pharmacie">
            <input type="hidden" id="edit_id_service" name="id_service">

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_nom_service">Nom de la pharmacie</label>
                    <input type="text" id="edit_nom_service" name="nom_service" required>
                </div>

                <div class="form-group">
                    <label for="edit_numero_telephone">Numéro de téléphone</label>
                    <input type="text" id="edit_numero_telephone" name="numero_telephone" required>
                </div>

                <div class="form-group">
                    <label for="edit_adresse">Adresse</label>
                    <input type="text" id="edit_adresse" name="adresse" required>
                </div>

                <div class="form-group">
                    <label for="edit_id_quartier">Quartier</label>
                    <select id="edit_id_quartier" name="id_quartier" required>
                        <?php foreach ($quartiers as $quartier): ?>
                            <option value="<?php echo htmlspecialchars($quartier['id_quartier']); ?>">
                                <?php echo htmlspecialchars($quartier['nom_quartier']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_nom_pharmacien">Nom du pharmacien</label>
                    <input type="text" id="edit_nom_pharmacien" name="nom_pharmacien">
                </div>

                <div class="form-group full-width">
                    <label for="edit_description_specifique">Description / Remarques</label>
                    <textarea id="edit_description_specifique" name="description_specifique"></textarea>
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
        --border-color: #dcdfe3;
        --text-main: #2d3436;
        --text-muted: #6b7280;
        --radius: 8px;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-lg: 0 10px 30px rgba(0,0,0,0.2);
    }

    .admin-content {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px;
        box-sizing: border-box;
        color: var(--text-main);
    }

    .admin-container,
    .add-pharmacy-section,
    .pharmacy-list-section {
        box-sizing: border-box;
    }

    .admin-content header h1 {
        margin: 0 0 5px;
        font-size: 1.8rem;
    }

    .admin-content header p {
        margin: 0 0 25px;
        color: var(--text-muted);
    }

    /* Alerte */
    .alert {
        padding: 14px 18px;
        margin-bottom: 20px;
        border-radius: var(--radius);
        font-weight: 600;
        border: 1px solid transparent;
    }
    .alert.success {
        background-color: var(--success-bg);
        color: var(--success-text);
        border-color: var(--success-border);
    }

    /* Sections */
    .add-pharmacy-section,
    .pharmacy-list-section {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 25px;
        margin-bottom: 30px;
    }

    .add-pharmacy-section h2,
    .pharmacy-list-section h2 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 1.3rem;
        border-bottom: 2px solid var(--primary);
        padding-bottom: 10px;
    }

    .empty-state {
        color: var(--text-muted);
        font-style: italic;
    }

    /* Formulaires */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-main);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        width: 100%;
        font-size: 0.95rem;
        font-family: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(44, 122, 123, 0.15);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    /* Boutons */
    .btn-primary {
        display: inline-block;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        background-color: var(--primary);
        color: #fff;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-primary:hover {
        background-color: var(--primary-dark);
    }
    .btn-primary:active {
        transform: scale(0.97);
    }

    .btn-danger {
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        background-color: var(--danger);
        color: white;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-danger:hover {
        background-color: var(--danger-dark);
    }
    .btn-danger:active {
        transform: scale(0.97);
    }

    .edit-btn {
        background-color: var(--primary);
        color: #fff;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        margin-right: 6px;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .edit-btn:hover {
        background-color: var(--primary-dark);
    }
    .edit-btn:active {
        transform: scale(0.97);
    }

    .inline-form {
        display: inline-block;
    }

    .mt-30 { margin-top: 30px; }

    /* Tableau */
    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
    }
    .table-scroll table {
        min-width: 820px;
        width: 100%;
        border-collapse: collapse;
    }
    .table-scroll th,
    .table-scroll td {
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
        vertical-align: top;
    }
    .table-scroll th {
        background-color: #f7f8f9;
        font-weight: 700;
        color: var(--text-main);
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.03em;
    }
    .table-scroll tbody tr:hover {
        background-color: #fafbfc;
    }
    .table-scroll tbody tr:last-child td {
        border-bottom: none;
    }

    .actions-cell {
        white-space: nowrap;
    }

    /* Responsive - Tablette */
    @media (max-width: 900px) {
        .admin-content {
            padding: 16px;
        }

        .add-pharmacy-section,
        .pharmacy-list-section {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
    }

    /* Responsive - Petites tablettes / grands mobiles */
    @media (max-width: 700px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .admin-content header h1 {
            font-size: 1.5rem;
        }
    }

    /* Responsive - Mobile : le tableau devient une liste de cartes */
    @media (max-width: 600px) {
        .admin-content {
            padding: 12px;
        }

        .add-pharmacy-section,
        .pharmacy-list-section {
            padding: 16px;
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
        }

        /* Table -> cartes */
        .table-scroll {
            overflow-x: visible;
            border: none;
            border-radius: 0;
        }

        .table-scroll table {
            min-width: 0;
            width: 100%;
        }

        .table-scroll thead {
            display: none;
        }

        .table-scroll tbody,
        .table-scroll tr,
        .table-scroll td {
            display: block;
            width: 100%;
        }

        .table-scroll tr {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            margin-bottom: 14px;
            padding: 10px 14px;
            box-shadow: var(--shadow);
        }

        .table-scroll td {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            text-align: right;
        }

        .table-scroll td:last-child {
            border-bottom: none;
        }

        .table-scroll td::before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-muted);
            text-align: left;
            flex-shrink: 0;
        }

        .actions-cell {
            flex-direction: column;
            align-items: stretch !important;
            gap: 8px !important;
        }

        .actions-cell::before {
            margin-bottom: 4px;
        }

        .edit-btn,
        .inline-form,
        .inline-form .btn-danger {
            width: 100%;
        }

        .inline-form {
            display: block;
        }
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.45);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 6% auto;
        padding: 30px;
        border: none;
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        overflow-y: auto;
        border-radius: 10px;
        position: relative;
        box-shadow: var(--shadow-lg);
        box-sizing: border-box;
    }

    @media (max-width: 600px) {
        .modal-content {
            width: 100%;
            max-width: 100%;
            height: 100%;
            max-height: 100%;
            margin: 0;
            border-radius: 0;
            padding: 20px;
        }

        .modal-content .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .modal-content h2 {
        margin-top: 0;
        border-bottom: 2px solid var(--primary);
        padding-bottom: 10px;
    }

    .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 20px;
        line-height: 1;
        cursor: pointer;
    }

    .close-button:hover,
    .close-button:focus {
        color: var(--danger);
        text-decoration: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById("editPharmacyModal");
        var span = document.getElementsByClassName("close-button")[0];

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_id_pharmacie').value = this.dataset.id_pharmacie;
                document.getElementById('edit_id_service').value = this.dataset.id_service;
                document.getElementById('edit_nom_service').value = this.dataset.nom_service;
                document.getElementById('edit_numero_telephone').value = this.dataset.numero_telephone;
                document.getElementById('edit_adresse').value = this.dataset.adresse;
                document.getElementById('edit_id_quartier').value = this.dataset.id_quartier;
                document.getElementById('edit_description_specifique').value = this.dataset.description_specifique;
                document.getElementById('edit_nom_pharmacien').value = this.dataset.nom_pharmacien;
                modal.style.display = "block";
            });
        });

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    });
</script>

<?php include '../includes/admin_footer.php' ?>