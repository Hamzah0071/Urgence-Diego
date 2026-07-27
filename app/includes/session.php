<?php

/**
 * app/includes/session.php
 * ------------------------------------------------------------
 * Fonctions de garde appelées depuis public/index.php AVANT de
 * dispatcher vers un Controller. Ce ne sont plus des fichiers
 * qu'on "include" en haut de chaque page.
 * ------------------------------------------------------------
 */

use App\Models\User;

require_once __DIR__ . '/../Models/User.php';

/**
 * Bloque l'accès aux visiteurs déjà connectés (landing, login, register).
 */
function requireGuest(): void
{
    if (isset($_SESSION['id_utilisateur'])) {
        header('Location: index.php?action=home');
        exit;
    }
}

/**
 * Bloque l'accès aux visiteurs non connectés (pages client protégées).
 * Pose $_SESSION['id_role'] / ['nom_role'] pour que header.php sache
 * quel menu afficher, sans refaire de requête.
 *
 * @return array Les infos de l'utilisateur connecté
 */
function requireAuth(PDO $pdo): array
{
    if (!isset($_SESSION['id_utilisateur'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $userModel = new User($pdo);
    $utilisateur = $userModel->trouverParIdAvecRole((int) $_SESSION['id_utilisateur']);

    // L'utilisateur a été supprimé en base entre-temps -> on ferme la session.
    if (!$utilisateur) {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    $_SESSION['id_role']  = $utilisateur['id_role'];
    $_SESSION['nom_role'] = $utilisateur['nom_role'];

    return $utilisateur;
}

/**
 * Bloque l'accès à tout ce qui n'est pas Administrateur.
 *
 * @return array Les infos de l'administrateur connecté
 */
function requireAdmin(PDO $pdo): array
{
    $utilisateur = requireAuth($pdo);

    if ($utilisateur['nom_role'] !== 'Administrateur') {
        header('Location: index.php?action=home');
        exit;
    }

    return $utilisateur;
}
