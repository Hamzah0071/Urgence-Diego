<?php
require_once '../includes/admin_session.php';
require_once '../includes/db_connect.php';

$message = '';

// Gérer l'ajout, la modification et la suppression d'utilisateurs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $id_role = $_POST['id_role'];
            $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
            $id_quartier = !empty($_POST['id_quartier']) ? $_POST['id_quartier'] : null;

            if ($action === 'add') {
                $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hacher le mot de passe
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, id_quartier, email, mot_de_passe, id_role) VALUES (:nom, :prenom, :date_naissance, :id_quartier, :email, :mot_de_passe, :id_role)");
                $stmt->execute([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'date_naissance' => $date_naissance,
                    'id_quartier' => $id_quartier,
                    'email' => $email,
                    'mot_de_passe' => $mot_de_passe,
                    'id_role' => $id_role
                ]);
                $message = "Utilisateur ajouté avec succès !";
            } elseif ($action === 'edit') {
                $id_utilisateur = $_POST['id_utilisateur'];
                $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, id_quartier = :id_quartier, email = :email, id_role = :id_role";
                $params = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'date_naissance' => $date_naissance,
                    'id_quartier' => $id_quartier,
                    'email' => $email,
                    'id_role' => $id_role,
                    'id_utilisateur' => $id_utilisateur
                ];

                if (!empty($_POST['mot_de_passe'])) {
                    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                    $sql .= ", mot_de_passe = :mot_de_passe";
                    $params['mot_de_passe'] = $mot_de_passe;
                }
                $sql .= " WHERE id_utilisateur = :id_utilisateur";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = "Utilisateur mis à jour avec succès !";
            }
        }

        if ($action === 'delete') {
            $id_utilisateur = $_POST['id_utilisateur'];
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = :id_utilisateur");
            $stmt->execute(['id_utilisateur' => $id_utilisateur]);
            $message = "Utilisateur supprimé avec succès !";
        }
    }
}

// Récupérer tous les utilisateurs avec leurs rôles et quartiers
$stmt_utilisateurs = $pdo->query("
    SELECT u.id_utilisateur, u.nom, u.prenom, u.email, r.nom_role, q.nom_quartier, u.date_naissance
    FROM utilisateurs u
    JOIN roles r ON u.id_role = r.id_role
    LEFT JOIN quartiers q ON u.id_quartier = q.id_quartier
    ORDER BY u.nom, u.prenom
");
$utilisateurs = $stmt_utilisateurs->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des rôles et quartiers pour les formulaires
$roles = $pdo->query("SELECT id_role, nom_role FROM roles ORDER BY nom_role")->fetchAll(PDO::FETCH_ASSOC);
$quartiers = getQuartiers($pdo);

include '../includes/admin_header.php';

?>

<header>
    <div class="header-title">
        <h1>Gestion des Utilisateurs</h1>
        <p>Ajoutez, modifiez ou supprimez les utilisateurs du système.</p>
    </div>
</header>

<?php if ($message): ?>
    <div class="alert success"><?php echo $message; ?></div>
<?php endif; ?>

<section class="card">
    <div class="card-header">
        <h2>Ajouter un nouvel utilisateur</h2>
    </div>
    <form action="utilisateurs.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe:</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <div class="form-group">
            <label for="id_role">Rôle:</label>
            <select id="id_role" name="id_role" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id_role']; ?>"><?php echo htmlspecialchars($role['nom_role']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date_naissance">Date de Naissance (optionnel):</label>
            <input type="date" id="date_naissance" name="date_naissance">
        </div>
        <div class="form-group">
            <label for="id_quartier">Quartier (optionnel):</label>
            <select id="id_quartier" name="id_quartier">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($quartiers as $quartier): ?>
                    <option value="<?php echo $quartier['id_quartier']; ?>"><?php echo htmlspecialchars($quartier['nom_quartier']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-action full-width">Ajouter Utilisateur</button>
    </form>
</section>

<section class="card mt-30">
    <div class="card-header">
        <h2>Liste des Utilisateurs</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Quartier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['nom_role']); ?></td>
                    <td><?php echo htmlspecialchars($user['nom_quartier'] ?? 'N/A'); ?></td>
                    <td>
                        <button class="btn-small edit-btn" 
                                data-id_utilisateur="<?php echo $user['id_utilisateur']; ?>" 
                                data-nom="<?php echo htmlspecialchars($user['nom']); ?>" 
                                data-prenom="<?php echo htmlspecialchars($user['prenom']); ?>" 
                                data-email="<?php echo htmlspecialchars($user['email']); ?>" 
                                data-id_role="<?php echo array_search($user['nom_role'], array_column($roles, 'nom_role', 'id_role')) + 1; ?>" 
                                data-date_naissance="<?php echo htmlspecialchars($user['date_naissance'] ?? ''); ?>"
                                data-id_quartier="<?php echo htmlspecialchars($user['id_quartier'] ?? ''); ?>"
                                >Modifier</button>
                        <form action="utilisateurs.php" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_utilisateur" value="<?php echo $user['id_utilisateur']; ?>">
                            <button type="submit" class="btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!-- Modal de modification -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier Utilisateur</h2>
        <form action="utilisateurs.php" method="POST" class="form-grid">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id_utilisateur" name="id_utilisateur">
            <div class="form-group">
                <label for="edit_nom">Nom:</label>
                <input type="text" id="edit_nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="edit_prenom">Prénom:</label>
                <input type="text" id="edit_prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="edit_mot_de_passe">Nouveau Mot de passe (laisser vide pour ne pas changer):</label>
                <input type="password" id="edit_mot_de_passe" name="mot_de_passe">
            </div>
            <div class="form-group">
                <label for="edit_id_role">Rôle:</label>
                <select id="edit_id_role" name="id_role" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id_role']; ?>"><?php echo htmlspecialchars($role['nom_role']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_date_naissance">Date de Naissance (optionnel):</label>
                <input type="date" id="edit_date_naissance" name="date_naissance">
            </div>
            <div class="form-group">
                <label for="edit_id_quartier">Quartier (optionnel):</label>
                <select id="edit_id_quartier" name="id_quartier">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($quartiers as $quartier): ?>
                        <option value="<?php echo $quartier['id_quartier']; ?>"><?php echo htmlspecialchars($quartier['nom_quartier']); ?></option>
                    <?php endforeach; ?>
                </select>
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
        var modal = document.getElementById("editUserModal");
        var span = document.getElementsByClassName("close-button")[0];

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_id_utilisateur').value = this.dataset.id_utilisateur;
                document.getElementById('edit_nom').value = this.dataset.nom;
                document.getElementById('edit_prenom').value = this.dataset.prenom;
                document.getElementById('edit_email').value = this.dataset.email;
                document.getElementById('edit_id_role').value = this.dataset.id_role;
                document.getElementById('edit_date_naissance').value = this.dataset.date_naissance;
                document.getElementById('edit_id_quartier').value = this.dataset.id_quartier;
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
include '../includes/admin_footer.php';
?>
