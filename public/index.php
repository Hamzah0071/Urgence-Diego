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
require_once __DIR__ . '/../app/Controllers/AdminController.php';

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;

// Connexion à la base de données
$pdo = require __DIR__ . '/../config/database.php';

// On utilise 'action' au lieu de 'page'
$action = $_GET['action'] ?? 'landing';

// Définition des routes par catégorie
$routesInvites   = ['landing', 'login', 'register'];
$routesProtegees = ['home', 'actualites', 'article-detail', 'don', 'profil', 'service-urgence', 'urgence-carte', 'new-article'];
$routesAdmin     = ['admin-dashboard', 'admin-articles', 'admin-import-articles', 'admin-services', 'admin-utilisateurs'];

// --- SYSTÈME DE GARDES (SÉCURITÉ) ---

if (in_array($action, $routesInvites, true)) {
    requireGuest();
} elseif (in_array($action, $routesProtegees, true)) {
    requireAuth($pdo);
} elseif (in_array($action, $routesAdmin, true)) {
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

    // --- Client connecté ---
    case 'home':
        (new HomeController($pdo))->home();
        break;

    case 'actualites':
        (new HomeController($pdo))->actualites();
        break;

    case 'article-detail':
        (new HomeController($pdo))->articleDetail();
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

    // --- Admin ---
    case 'admin-dashboard':
        (new AdminController($pdo))->dashboard();
        break;

    case 'admin-articles':
        (new AdminController($pdo))->articles();
        break;

    case 'admin-import-articles':
        (new AdminController($pdo))->importArticles();
        break;

    case 'admin-services':
        (new AdminController($pdo))->services();
        break;

    case 'admin-utilisateurs':
        (new AdminController($pdo))->dashboard();
        break;

    default:
        http_response_code(404);
        echo 'Page introuvable.';
        break;
}