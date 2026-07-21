<?php
require_once '../includes/admin_session.php';
require_once '../includes/db_connect.php';

// Récupérer les données pour les statistiques
// Nombre de pharmacies de garde (pour aujourd'hui)
$today = date('Y-m-d');
$stmt_pharmacies_garde = $pdo->prepare("
    SELECT COUNT(DISTINCT s.id_service)
    FROM garde g
    JOIN service s ON g.id_service = s.id_service
    JOIN type_service t ON s.id_type = t.id_type
    WHERE t.nom_type = 'Pharmacie'
    AND :today BETWEEN g.date_debut AND g.date_fin
");
$stmt_pharmacies_garde->execute(['today' => $today]);
$num_pharmacies_garde = $stmt_pharmacies_garde->fetchColumn();

// Nombre total de services d'urgence actifs (tous types confondus)
$stmt_services_urgence = $pdo->query("SELECT COUNT(*) FROM service WHERE actif = 1");
$num_services_urgence = $stmt_services_urgence->fetchColumn();

// Mises à jour récentes (les 5 dernières modifications de tours de garde)
$stmt_recent_updates = $pdo->prepare("
    SELECT t.nom_type as type, s.libelle as nom, 'Mise à jour garde' as action, g.date_fin as date,
           CASE WHEN :today BETWEEN g.date_debut AND g.date_fin THEN 'Actif' ELSE 'Terminé' END as statut
    FROM garde g
    JOIN service s ON g.id_service = s.id_service
    JOIN type_service t ON s.id_type = t.id_type
    ORDER BY g.date_fin DESC
    LIMIT 5
");
$stmt_recent_updates->execute(['today' => $today]);
$recent_updates = $stmt_recent_updates->fetchAll(PDO::FETCH_ASSOC);

// Inclure l'en-tête et la barre latérale
include '../includes/admin_header.php';
?>

<main class="admin-content">

    <h1>Tableau de bord</h1>

    <div class="admin-stats">
        <div class="stat-card">
            <h3>Pharmacies de garde aujourd'hui</h3>
            <p class="stat-number"><?= htmlspecialchars($num_pharmacies_garde) ?></p>
        </div>
        <div class="stat-card">
            <h3>Services d'urgence actifs</h3>
            <p class="stat-number"><?= htmlspecialchars($num_services_urgence) ?></p>
        </div>
    </div>

    <section class="recent-updates">
        <h2>Mises à jour récentes</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nom</th>
                    <th>Action</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_updates)): ?>
                    <tr><td colspan="5">Aucune mise à jour récente.</td></tr>
                <?php else: ?>
                    <?php foreach ($recent_updates as $update): ?>
                        <tr>
                            <td><?= htmlspecialchars($update['type']) ?></td>
                            <td><?= htmlspecialchars($update['nom']) ?></td>
                            <td><?= htmlspecialchars($update['action']) ?></td>
                            <td><?= htmlspecialchars($update['date']) ?></td>
                            <td><?= htmlspecialchars($update['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</main>
</div>

<?php
// Inclure le pied de page
include '../includes/admin_footer.php';
?>