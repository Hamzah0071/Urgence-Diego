
<?php
require_once './includes/admin_session.php';
require_once '../db_connect.php';

$message = '';

// Gérer l'ajout et la modification de pharmacie 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $nom_service = $_POST['nom_service'];
            $numero_telephone = $_POST['numero_telephone'];
            $adresse = $_POST['adresse'];
            $id_quartier = $_POST['id_quartier'];
            $description_specifique = $_POST['description_specifique'];
            $nom_pharmacien = $_POST['nom_pharmacien'];
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
                    'description_specifique' => $description_specifique
                ]);
                $id_service = $pdo->lastInsertId();

                // Insérer dans pharmacies
                $stmt_pharmacie = $pdo->prepare("INSERT INTO pharmacies (id_service, nom_pharmacien) VALUES (:id_service, :nom_pharmacien)");
                $stmt_pharmacie->execute([
                    'id_service' => $id_service,
                    'nom_pharmacien' => $nom_pharmacien
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
                    'description_specifique' => $description_specifique,
                    'id_service' => $id_service
                ]);

                // Mettre à jour pharmacies
                $stmt_pharmacie = $pdo->prepare("UPDATE pharmacies SET nom_pharmacien = :nom_pharmacien WHERE id_pharmacie = :id_pharmacie");
                $stmt_pharmacie->execute([
                    'nom_pharmacien' => $nom_pharmacien,
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
    SELECT p.id_pharmacie, su.id_service, su.nom_service, su.numero_telephone, su.adresse, q.nom_quartier, su.description_specifique, p.nom_pharmacien
    FROM pharmacies p
    JOIN services_urgence su ON p.id_service = su.id_service
    JOIN quartiers q ON su.id_quartier = q.id_quartier
    ORDER BY su.nom_service
");
$pharmacies = $stmt_pharmacies->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des quartiers pour les formulaires
$quartiers = getQuartiers($pdo);

include './includes/admin_header.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacies</title>
</head>


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
                </div>
            </form>
        </section>
    </div>
</div>


<style>
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

    /* Le tableau peut scroller horizontalement sur petit écran plutôt que d'écraser la mise en page */
    .table-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-scroll table {
        min-width: 720px;
    }
    .actions-cell {
        white-space: nowrap;
    }

    /* Sur mobile, le formulaire d'ajout passe en une seule colonne */
    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

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


    <?php include './includes/admin_footer.php' ?>
    

</body>
</html>