<?php
require_once '../includes/admin_session.php';
require_once '../includes/db_connect.php';

// Récupérer les données pour les statistiques
// Nombre de pharmacies de garde (pour aujourd'hui)
$today = date('Y-m-d');
$stmt_pharmacies_garde = $pdo->prepare("
    SELECT COUNT(DISTINCT p.id_pharmacie)
    FROM tours_garde tg
    JOIN pharmacies p ON tg.id_pharmacie = p.id_pharmacie
    WHERE :today BETWEEN tg.date_debut AND tg.date_fin
");
$stmt_pharmacies_garde->execute(['today' => $today]);
$num_pharmacies_garde = $stmt_pharmacies_garde->fetchColumn();

// Nombre total de services d'urgence (pour les numéros d'urgence)
$stmt_services_urgence = $pdo->query("SELECT COUNT(*) FROM services_urgence");
$num_services_urgence = $stmt_services_urgence->fetchColumn();

// Mises à jour récentes (exemple : les 5 dernières modifications de tours de garde ou ajouts de services)
$stmt_recent_updates = $pdo->query("
    SELECT 'Pharmacie' as type, su.nom_service as nom, 'Mise à jour garde' as action, tg.date_fin as date, 'Actif' as statut
    FROM tours_garde tg
    JOIN pharmacies p ON tg.id_pharmacie = p.id_pharmacie
    JOIN services_urgence su ON p.id_service = su.id_service
    ORDER BY tg.date_fin DESC
    LIMIT 5
");
$recent_updates = $stmt_recent_updates->fetchAll(PDO::FETCH_ASSOC);

// Inclure l'en-tête et la barre latérale
include '../includes/admin_header.php';
?>
<section class="hero">
    
    
</section>


    
   

<?php
// Inclure le pied de page
include '../includes/admin_footer.php';
?>
