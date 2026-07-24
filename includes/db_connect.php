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
