<?php

/**
 * public/index.php
 * ------------------------------------------------------------
 * Point d'entrée unique sécurisé. Routage via ?action=xxx.
 * ------------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion des fonctions de garde et des contrôleurs
require_once __DIR__ . '/../app/includes/session.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;

// Connexion à la base de données
$pdo = require __DIR__ . '/../config/database.php';

// On utilise 'action' au lieu de 'page'
$action = $_GET['action'] ?? 'landing';

// Définition des routes par catégorie
$routesInvites   = ['landing', 'login', 'register'];
$routesProtegees = ['home', 'actualites', 'don', 'profil', 'service-urgence', 'urgence-carte', 'new-article'];
$routesAdmin     = ['admin-dashboard', 'admin-articles', 'admin-services', 'admin-utilisateurs'];

// --- SYSTÈME DE GARDES (SÉCURITÉ) ---

if (in_array($action, $routesInvites, true)) {
    // Si on est sur login/register mais déjà connecté, on redirige vers home
    requireGuest();
} elseif (in_array($action, $routesProtegees, true)) {
    // Si on accède à une page protégée, on vérifie l'auth
    requireAuth($pdo);
} elseif (in_array($action, $routesAdmin, true)) {
    // Si on accède à l'admin, on vérifie le rôle Admin
    requireAdmin($pdo);
}

// --- DISPATCHER ---

switch ($action) {
    // --- Invités ---
    case 'landing':
        (new AuthController($pdo))->landing();
        break;

    case 'login':
        (new AuthController($pdo))->login();
        break;

    case 'register':
        (new AuthController($pdo))->register();
        break;

    // --- Sans garde particulière ---
    case 'logout':
        (new AuthController($pdo))->logout();
        break;

    // --- Client connecté (déjà filtré par requireAuth) ---
    case 'home':
        (new HomeController($pdo))->home();
        break;

    case 'actualites':
        (new HomeController($pdo))->actualites();
        break;

    case 'don':
        (new HomeController($pdo))->don();
        break;

    case 'profil':
        (new HomeController($pdo))->profile();
        break;

    case 'service-urgence':
        (new HomeController($pdo))->serviceUrgence();
        break;

    case 'urgence-carte':
        (new HomeController($pdo))->urgenceCarte();
        break;

    case 'new-article':
        (new HomeController($pdo))->newArticle();
        break;

    // --- Admin (déjà filtré par requireAdmin) ---
    case 'admin-dashboard':
    case 'admin-articles':
    case 'admin-services':
    case 'admin-utilisateurs':
        http_response_code(501);
        echo 'Espace admin en cours de construction.';
        break;

    default:
        http_response_code(404);
        echo 'Page introuvable.';
        break;
}
