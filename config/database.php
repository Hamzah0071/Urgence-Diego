<?php
/**
 * config/database.php
 * ------------------------------------------------------------
 * Crée et retourne l'instance PDO unique de l'application.
 * Ce fichier ne fait QUE la connexion : pas de session, pas de
 * logique métier. Il est appelé une seule fois depuis
 * public/index.php, puis $pdo est injecté dans les Controllers.
 * ------------------------------------------------------------
 */

$hote      = 'localhost';
$base      = 'urgences_antsiranana';
$utilisateur = 'root';
$motDePasse  = '';
$charset   = 'utf8mb4';

$dsn = "mysql:host={$hote};dbname={$base};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $utilisateur, $motDePasse, $options);
} catch (PDOException $e) {
    // En production : logger l'erreur plutôt que l'afficher.
    error_log('Connexion BDD échouée : ' . $e->getMessage());
    http_response_code(500);
    die('Erreur de connexion à la base de données.');
}

return $pdo;