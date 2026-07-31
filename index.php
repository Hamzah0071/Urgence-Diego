<?php
/**
 * Fichier de redirection de sécurité.
 * Redirige vers le dossier public tout en conservant les paramètres GET.
 */
$queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: public/index.php' . $queryString);
exit;
