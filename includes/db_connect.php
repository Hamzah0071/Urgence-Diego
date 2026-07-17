<!-- db-connection -->
<?php
$host = 'localhost';
$dbname = 'urgences_antsiranana';
$user = 'root';
$password = ''; // vide par défaut (XAMPP)

try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Erreur de connexion : " . $e->getMessage());
}
// teste si il est connecte ou pas 
// echo "Connexion réussie à la base de données !";

/**
 * Fonctions utilitaires partagées (utilisent PDO, pas mysqli)
 */

// Retourne la liste de tous les quartiers
function getQuartiers($pdo) {
    $stmt = $pdo->query("SELECT id_quartier, nom_quartier FROM quartiers ORDER BY nom_quartier");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retourne la pharmacie de garde en cours (basée sur la date du jour)
function getPharmacieGarde($pdo) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT su.nom_service, su.numero_telephone, q.nom_quartier
        FROM tours_garde tg
        JOIN pharmacies p ON tg.id_pharmacie = p.id_pharmacie
        JOIN services_urgence su ON p.id_service = su.id_service
        JOIN quartiers q ON su.id_quartier = q.id_quartier
        WHERE :today BETWEEN tg.date_debut AND tg.date_fin
        ORDER BY tg.date_debut DESC
        LIMIT 1
    ");
    $stmt->execute(['today' => $today]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

// Retourne les services d'urgence avec leur catégorie et leur quartier.
// Si $id_quartier est fourni, ne retourne que les services de ce quartier.
function getServices($pdo, $id_quartier = null) {
    $sql = "
        SELECT su.nom_service, su.numero_telephone, cs.nom_categorie, q.nom_quartier
        FROM services_urgence su
        JOIN categories_service cs ON su.id_categorie = cs.id_categorie
        JOIN quartiers q ON su.id_quartier = q.id_quartier
    ";

    if ($id_quartier) {
        $sql .= " WHERE su.id_quartier = :id_quartier";
    }

    $sql .= " ORDER BY cs.nom_categorie, su.nom_service";

    $stmt = $pdo->prepare($sql);
    if ($id_quartier) {
        $stmt->execute(['id_quartier' => $id_quartier]);
    } else {
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>