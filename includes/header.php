<?php
require_once __DIR__ . '/session.php';
$current_page = basename($_SERVER['PHP_SELF']); // ex: "admin_pharmacies.php"
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Accueil - Urgences Antsiranana</title>
    <link rel="stylesheet" href="asset/css/home.css">
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">

</head>
<body>
       
    <header>
        <div class="container header-inner">
            <!-- <a href="#" style="text-decoration: none;">
                <div class="logo-placeholder">URGENCES</div>
            </a> -->

            <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Ouvrir le menu">
                <i class="fa-solid fa-bars"></i>
            </button>



            <!-- Navigation : liste déroulante sur mobile, barre horizontale sur tablette/PC -->
            <nav class="top-nav" id="topNav">
                <div class="nav-links-scroll">
                    <a href="#" class="<?= $current_page === 'accueil' ? 'active' : '' ?>">
                        <i class="fa-solid fa-house"></i> Accueil</a>
                    <a href="#" class="<?= $current_page === 'articles' ? 'active' : '' ?>">
                        <i class="fa-solid fa-newspaper"></i> Articles</a>
                    <a href="#"  class="<?= $current_page === 'pharmacies' ? 'active' : '' ?>"class="nav-pharmacie">
                        <i class="fa-solid fa-staff-snake" style="color: rgb(99, 230, 190);"></i> Pharmacie</a>
                    <a href="#" class="<?= $current_page === 'ambulance' ? 'active' : '' ?>">
                        <i class="fa-solid fa-truck-medical"></i> Ambulance</a>
                    <a href="#" class="<?= $current_page === 'pompiers' ? 'active' : '' ?>">
                        <i class="fa-solid fa-fire-extinguisher"></i> Pompiers</a>
                    <a href="#" class="<?= $current_page === 'hopitaux' ? 'active' : '' ?>">
                        <i class="fa-solid fa-hospital"></i> Hôpital</a>
                    <a href="#" class="<?= $current_page === 'police' ? 'active' : '' ?>">
                        <i class="fa-solid fa-shield-halved"></i> Police centrale</a>
                    <a href="#" class="<?= $current_page === 'articles' ? 'active' : '' ?>">
                        <i class="fa-solid fa-newspaper"></i> Articles</a>
                    <a href="#"><i class="fa-solid fa-hand-holding-dollar"></i> Faire un don</a>
                </div>
                <div class="nav-actions">
                    <a href="logout.php" class="btn-logout"><i class="fa-solid fa-user"></i> Déconnexion</a>
                </div>
            </nav>

            <div class="nav-backdrop" id="navBackdrop" onclick="toggleMobileMenu()"></div>
        </div>
    </header>

    <script>
        function toggleMobileMenu() {
            document.getElementById('topNav').classList.toggle('open');
            document.getElementById('navBackdrop').classList.toggle('open');
        }

        // Ferme le menu automatiquement quand on clique un lien (mobile)
        document.querySelectorAll('#topNav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 768) {
                    document.getElementById('topNav').classList.remove('open');
                    document.getElementById('navBackdrop').classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>

    

    
