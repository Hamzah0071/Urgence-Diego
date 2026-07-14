<?php
require_once './includes/admin_session.php';
require_once '../db_connect.php';

$message = '';
$id_categorie_force_ordre = 3; // ID pour la catégorie 'Force de l'ordre'

// Gérer l'ajout et la modification des services de police/gendarmerie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $nom_service = $_POST['nom_service'];
            $numero_telephone = $_POST['numero_telephone'];
            $adresse = $_POST['adresse'];
            $id_quartier = $_POST['id_quartier'];
            $description_specifique = $_POST['description_specifique'];

            if ($action === 'add') {
                $stmt_service = $pdo->prepare("INSERT INTO services_urgence (nom_service, numero_telephone, adresse, id_quartier, id_categorie, description_specifique) VALUES (:nom_service, :numero_telephone, :adresse, :id_quartier, :id_categorie, :description_specifique)");
                $stmt_service->execute([
                    'nom_service' => $nom_service,
                    'numero_telephone' => $numero_telephone,
                    'adresse' => $adresse,
                    'id_quartier' => $id_quartier,
                    'id_categorie' => $id_categorie_force_ordre,
                    'description_specifique' => $description_specifique
                ]);
                $message = "Service de force de l'ordre ajouté avec succès !";
            } elseif ($action === 'edit') {
                $id_service = $_POST['id_service'];

                $stmt_service = $pdo->prepare("UPDATE services_urgence SET nom_service = :nom_service, numero_telephone = :numero_telephone, adresse = :adresse, id_quartier = :id_quartier, description_specifique = :description_specifique WHERE id_service = :id_service");
                $stmt_service->execute([
                    'nom_service' => $nom_service,
                    'numero_telephone' => $numero_telephone,
                    'adresse' => $adresse,
                    'id_quartier' => $id_quartier,
                    'description_specifique' => $description_specifique,
                    'id_service' => $id_service
                ]);
                $message = "Service de force de l'ordre mis à jour avec succès !";
            }
        }

        if ($action === 'delete') {
            $id_service = $_POST['id_service'];

            $stmt_delete_service = $pdo->prepare("DELETE FROM services_urgence WHERE id_service = :id_service");
            $stmt_delete_service->execute(['id_service' => $id_service]);

            $message = "Service de force de l'ordre supprimé avec succès !";
        }
    }
}

// Récupérer tous les services de force de l'ordre
$stmt_forces_ordre = $pdo->prepare("
    SELECT su.id_service, su.nom_service, su.numero_telephone, su.adresse, q.nom_quartier, su.description_specifique
    FROM services_urgence su
    JOIN quartiers q ON su.id_quartier = q.id_quartier
    WHERE su.id_categorie = :id_categorie_force_ordre
    ORDER BY su.nom_service
");
$stmt_forces_ordre->execute(['id_categorie_force_ordre' => $id_categorie_force_ordre]);
$forces_ordre = $stmt_forces_ordre->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des quartiers pour les formulaires
$quartiers = getQuartiers($pdo);

include './includes/admin_header.php';

?>

<header>
    <div class="header-title">
        <h1>Gestion Police / Gendarmerie</h1>
        <p>Ajoutez, modifiez ou supprimez les services de police et de gendarmerie.</p>
    </div>
</header>

<?php if ($message): ?>
    <div class="alert success"><?php echo $message; ?></div>
<?php endif; ?>

<section class="card">
    <div class="card-header">
        <h2>Ajouter un nouveau service</h2>
    </div>
    <form action="police_gendarmerie.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="nom_service">Nom du Service:</label>
            <input type="text" id="nom_service" name="nom_service" required>
        </div>
        <div class="form-group">
            <label for="numero_telephone">Numéro de Téléphone:</label>
            <input type="text" id="numero_telephone" name="numero_telephone" required>
        </div>
        <div class="form-group">
            <label for="adresse">Adresse:</label>
            <input type="text" id="adresse" name="adresse" required>
        </div>
        <div class="form-group">
            <label for="id_quartier">Quartier:</label>
            <select id="id_quartier" name="id_quartier" required>
                <?php foreach ($quartiers as $quartier): ?>
                    <option value="<?php echo $quartier['id_quartier']; ?>"><?php echo htmlspecialchars($quartier['nom_quartier']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group full-width">
            <label for="description_specifique">Description Spécifique (optionnel):</label>
            <textarea id="description_specifique" name="description_specifique"></textarea>
        </div>
        <button type="submit" class="btn-action full-width">Ajouter Service</button>
    </form>
</section>

<section class="card mt-30">
    <div class="card-header">
        <h2>Liste des Services Police / Gendarmerie</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Quartier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($forces_ordre as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['nom_service']); ?></td>
                    <td><?php echo htmlspecialchars($service['numero_telephone']); ?></td>
                    <td><?php echo htmlspecialchars($service['adresse']); ?></td>
                    <td><?php echo htmlspecialchars($service['nom_quartier']); ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!-- Modal de modification -->
<div id="editServiceModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier Service</h2>
        <form action="police_gendarmerie.php" method="POST" class="form-grid">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id_service" name="id_service">
            <div class="form-group">
                <label for="edit_nom_service">Nom du Service:</label>
                <input type="text" id="edit_nom_service" name="nom_service" required>
            </div>
            <div class="form-group">
                <label for="edit_numero_telephone">Numéro de Téléphone:</label>
                <input type="text" id="edit_numero_telephone" name="numero_telephone" required>
            </div>
            <div class="form-group">
                <label for="edit_adresse">Adresse:</label>
                <input type="text" id="edit_adresse" name="adresse" required>
            </div>
            <div class="form-group">
                <label for="edit_id_quartier">Quartier:</label>
                <select id="edit_id_quartier" name="id_quartier" required>
                    <?php foreach ($quartiers as $quartier): ?>
                        <option value="<?php echo $quartier['id_quartier']; ?>"><?php echo htmlspecialchars($quartier['nom_quartier']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group full-width">
                <label for="edit_description_specifique">Description Spécifique (optionnel):</label>
                <textarea id="edit_description_specifique" name="description_specifique"></textarea>
            </div>
            <button type="submit" class="btn-action full-width">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<style>
    /* Styles from admin_pharmacies.php for alerts, forms, buttons, and modals */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: bold;
    }
    .alert.success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
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
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-group input, .form-group select, .form-group textarea {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }
    .full-width {
        grid-column: 1 / -1;
    }
    .btn-danger {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-danger:hover {
        background-color: #c0392b;
    }
    .mt-30 { margin-top: 30px; }

    /* Modal Styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto; /* 10% from the top and centered */
        padding: 30px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
        max-width: 700px;
        border-radius: 10px;
        position: relative;
        box-shadow: var(--shadow);
    }

    .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 20px;
    }

    .close-button:hover,
    .close-button:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById("editServiceModal");
        var span = document.getElementsByClassName("close-button")[0];

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_id_service').value = this.dataset.id_service;
                document.getElementById('edit_nom_service').value = this.dataset.nom_service;
                document.getElementById('edit_numero_telephone').value = this.dataset.numero_telephone;
                document.getElementById('edit_adresse').value = this.dataset.adresse;
                document.getElementById('edit_id_quartier').value = this.dataset.id_quartier;
                document.getElementById('edit_description_specifique').value = this.dataset.description_specifique;
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

<?php
include 'admin_footer.php';
?>
