<?php
session_start();

// Inclure le fichier de connexion à la base de données
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur connecté (avec son rôle)
$id_utilisateur = $_SESSION["id_utilisateur"];
$stmt = $pdo->prepare("SELECT u.id_utilisateur, u.nom, u.email, u.id_role, r.nom_role 
                       FROM utilisateur u 
                       JOIN role r ON u.id_role = r.id_role 
                       WHERE u.id_utilisateur = :id_utilisateur");
$stmt->execute(['id_utilisateur' => $id_utilisateur]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'existe plus en base (ex: supprimé), on détruit la session
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Variable disponible dans toutes les pages client
$current_user = $user;

// On garde aussi le rôle directement en session : ainsi header.php (et
// n'importe quelle autre page) n'a besoin que de lire $_SESSION['id_role']
// pour savoir quoi afficher, sans refaire de requête ni de logique métier.
$_SESSION['id_role']  = $user['id_role'];
$_SESSION['nom_role'] = $user['nom_role'];
?>