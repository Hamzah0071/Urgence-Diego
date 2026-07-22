<?php
require_once __DIR__ . '/admin_session.php';
$current_page = basename($_SERVER['PHP_SELF']); // ex: "admin_pharmacies.php"
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Urgence</title>

    <link rel="stylesheet" href="../asset/css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="admin-container">

        <!-- Barre du haut (visible uniquement sur mobile) -->
        <div class="mobile-topbar">
            <div class="mobile-topbar-title">
                <i class="fas fa-shield-alt"></i>
                <span>Admin Urgence</span>
            </div>
            <button class="mobile-menu-btn" onclick="toggleAdminMenu()" aria-label="Ouvrir le menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleAdminMenu()"></div>

        <!-- Sidebar -->
        <header class="sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <i class="fas fa-shield-alt"></i>
                <span>Admin Urgence</span>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-links">

                    <li class="<?= $current_page === 'admin_index.php' ? 'active' : '' ?>">
                        <a href="./admin_index.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="<?= $current_page === 'admin_articles.php' ? 'active' : '' ?>">
                        <a href="./admin_articles.php"><i class="fas fa-newspaper"></i> Articles</a>
                    </li>
                    <li class="<?= $current_page === 'admin_services.php' ? 'active' : '' ?>">
                        <a href="./admin_services.php"><i class="fas fa-pills"></i> Services d'urgence</a>
                    </li>

                    <li class="<?= $current_page === 'admin_utilisateurs.php' ? 'active' : '' ?>">
                        <a href="./admin_utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a>
                    </li>
                    <li class="logout">
                        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </li>
                </ul>
            </nav>
        </header>

<script>
    function toggleAdminMenu() {
        document.getElementById('adminSidebar').classList.toggle('open');
        document.getElementById('sidebarBackdrop').classList.toggle('open');
    }

    // Ferme le menu automatiquement après avoir choisi un lien (mobile)
    document.querySelectorAll('#adminSidebar .nav-links a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                document.getElementById('adminSidebar').classList.remove('open');
                document.getElementById('sidebarBackdrop').classList.remove('open');
            }
        });
    });
</script>