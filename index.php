<?php
/**
 * Fichier de redirection de sécurité.
 * Empêche l'accès à la structure des dossiers si l'utilisateur
 * pointe directement sur la racine du projet.
 */
header('Location: public/');
exit;
