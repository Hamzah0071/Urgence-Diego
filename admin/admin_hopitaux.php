<?php
require_once './includes/admin_session.php';
require_once '../db_connect.php';

$message = '';
$id_categorie_hopital = 4; // ID pour la catégorie 'Hôpital'

// Gérer l'ajout et la modification des hôpitaux
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
                    'id_categorie' => $id_categorie_hopital,
                    'description_specifique' => $description_specifique
                ]);
                $message = "Hôpital ajouté avec succès !";
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
                $message = "Hôpital mis à jour avec succès !";
            }
        }

        if ($action === 'delete') {
            $id_service = $_POST['id_service'];

            $stmt_delete_service = $pdo->prepare("DELETE FROM services_urgence WHERE id_service = :id_service");
            $stmt_delete_service->execute(['id_service' => $id_service]);

            $message = "Hôpital supprimé avec succès !";
        }
    }
}

// Récupérer tous les hôpitaux
$stmt_hopitaux = $pdo->prepare("
    SELECT su.id_service, su.nom_service, su.numero_telephone, su.adresse, q.nom_quartier, su.description_specifique
    FROM services_urgence su
    JOIN quartiers q ON su.id_quartier = q.id_quartier
    WHERE su.id_categorie = :id_categorie_hopital
    ORDER BY su.nom_service
");
$stmt_hopitaux->execute(['id_categorie_hopital' => $id_categorie_hopital]);
$hopitaux = $stmt_hopitaux->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des quartiers pour les formulaires
$quartiers = getQuartiers($pdo);

include './includes/admin_header.php';
?>

<header>
    <div class="header-title">
        <h1>Gestion des Hôpitaux</h1>
        <p>Ajoutez, modifiez ou supprimez les services hospitaliers.</p>
    </div>
</header>

<?php if ($message): ?>
    <div class="alert success"><?php echo $message; ?></div>
<?php endif; ?>

<section class="card">
    <div class="card-header">
        <h2>Ajouter un nouvel hôpital</h2>
    </div>
    <form action="hopitaux.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="nom_service">Nom de l'Hôpital:</label>
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
        <button type="submit" class="btn-action full-width">Ajouter Hôpital</button>
    </form>
</section>

<section class="card mt-30">
    <div class="card-header">
        <h2>Liste des Hôpitaux</h2>
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
            <?php foreach ($hopitaux as $hopital): ?>
                <tr>
                    <td><?php echo htmlspecialchars($hopital['nom_service']); ?></td>
                    <td><?php echo htmlspecialchars($hopital['numero_telephone']); ?></td>
                    <td><?php echo htmlspecialchars($hopital['adresse']); ?></td>
                    <td><?php echo htmlspecialchars($hopital['nom_quartier']); ?></td>
                    <td>
                        <button class="btn-small edit-btn" data-id_service="<?php echo $hopital['id_service']; ?>" data-nom_service="<?php echo htmlspecialchars($hopital['nom_service']); ?>" data-numero_telephone="<?php echo htmlspecialchars($hopital['numero_telephone']); ?>" data-adresse="<?php echo htmlspecialchars($hopital['adresse']); ?>" data-id_quartier="<?php echo $hopital['id_quartier']; ?>" data-description_specifique="<?php echo htmlspecialchars($hopital['description_specifique']); ?>">Modifier</button>
                        <form action="hopitaux.php" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_service" value="<?php echo $hopital['id_service']; ?>">
                            <button type="submit" class="btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet hôpital ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!-- Modal de modification -->
<div id="editHopitalModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier Hôpital</h2>
        <form action="hopitaux.php" method="POST" class="form-grid">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id_service" name="id_service">
            <div class="form-group">
                <label for="edit_nom_service">Nom de l'Hôpital:</label>
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
        var modal = document.getElementById("editHopitalModal");
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
