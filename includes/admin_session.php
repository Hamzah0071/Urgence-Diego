<?php
// admin_session.php
session_start();

// Inclure le fichier de connexion à la base de données
require_once __DIR__ . '/db_connect.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: ../login.php");
    exit();
}

// Vérifier le rôle de l'utilisateur (doit être Administrateur)
$id_utilisateur = $_SESSION["id_utilisateur"];

$stmt = $pdo->prepare("
    SELECT u.id_role, r.nom_role 
    FROM utilisateur u 
    JOIN role r ON u.id_role = r.id_role 
    WHERE u.id_utilisateur = :id_utilisateur
");
$stmt->execute(['id_utilisateur' => $id_utilisateur]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user["nom_role"] !== "Administrateur") {
    // Rediriger si l'utilisateur n'est pas administrateur
    header("Location: ../index.php"); // Ou une page d'erreur/accès refusé
    exit();
}

// L'utilisateur est connecté et est administrateur
$admin_user = $user;
?>