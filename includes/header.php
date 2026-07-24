<?php
require_once __DIR__ . '/session.php';
$current_page = basename($_SERVER['PHP_SELF']);

// Rôle de l'utilisateur connecté (2 = Redacteur, d'après la table `role`)
// posé par session.php dans $_SESSION['id_role'] : le header se contente
// de le lire pour savoir quel menu afficher, sans logique supplémentaire.
$id_role = $_SESSION['id_role'] ?? null;
$est_redacteur = ((int) $id_role === 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/icon/fontAwesome/all.min.css">
</head>

<style>
    /* ==========================================================
   HEADER — cohérent avec la landing page (index.php)
   Fichier UNIQUE : ne pas dupliquer ce CSS ailleurs.
   ========================================================== */
:root {
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --blue-deep: #1e40af;
    --blue-deep-dark: #1e3a8a;
    --red-emergency: #dc2626;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --transition: all 0.3s ease;
    --safe-area-inset-bottom: env(safe-area-inset-bottom, 0);

    /* alias pour rester compatible avec le CSS existant du header */
    --header-bg: var(--bg-white);
    --header-bg-scrolled: var(--bg-white);
    --header-height: 77px; /* ≈ padding 1rem + hauteur logo, comme la landing page */
    --transition-fast: var(--transition);
    --color-text: var(--text-dark);
    --color-text-muted: var(--text-muted);
    --color-primary: var(--blue-deep);
    --color-primary-dark: var(--blue-deep-dark);
    --color-danger: var(--red-emergency);
    --color-danger-dark: #b91c1c;
    --color-border: #e2e8f0;
}

* {
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ---------- Header (identique à la landing page) ---------- */
.site-header {
    padding: 1rem 0;
    background: var(--bg-white);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: var(--transition);
}

.site-header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.site-header.scrolled {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
}

/* ---------- Logo : même badge dégradé que la landing page ---------- */
.logo-placeholder {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.6rem 1rem;
    min-width: 120px;
    height: 45px;
    background: linear-gradient(135deg, var(--blue-deep) 0%, #3b82f6 100%);
    border: none;
    border-radius: 8px;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: var(--transition);
}

.logo-placeholder:active {
    transform: scale(0.95);
}

.logo-icon {
    color: #ffffff;
    font-size: 1rem;
}

/* ---------- Bouton menu mobile ---------- */
.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.4rem;
    color: var(--color-text);
    cursor: pointer;
    padding: 8px;
}

/* ---------- Navigation ---------- */
.top-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1;
    margin-left: 32px;
}

#topNav.top-nav a {
    color: var(--color-text);
    text-decoration: none;
}

.nav-links-scroll {
    display: flex;
    align-items: center;
    gap: 8px;
}

#topNav .nav-links-scroll a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--color-text-muted);
    text-decoration: none;
    white-space: nowrap;
    transition: var(--transition-fast);
}

#topNav .nav-links-scroll a:hover {
    background: rgba(30, 64, 175, 0.08);
    color: var(--color-primary);
}

#topNav .nav-links-scroll a.active {
    background: rgba(30, 64, 175, 0.1);
    color: var(--color-primary);
    font-weight: 600;
}

#topNav .nav-links-scroll a i {
    font-size: 0.9rem;
    color: inherit;
}

.btn-donate {
    background: var(--color-primary);
    color: #ffffff !important;
    font-weight: 600;
}

.btn-donate:hover {
    background: var(--color-primary-dark);
    color: #ffffff !important;
}

/* ---------- Actions à droite ---------- */
.nav-actions {
    display: flex;
    align-items: center;
    margin-left: 16px;
}

.btn-logout {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border: 1px solid var(--color-danger);
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
    background: var(--color-danger) !important;
    color: #ffffff !important;
    text-decoration: none;
    transition: var(--transition-fast);
}

.btn-logout i {
    color: #ffffff !important;
}

.btn-logout:hover {
    background: var(--color-danger-dark) !important;
    opacity: 0.9;
}

/* ---------- Backdrop mobile ---------- */
.nav-backdrop {
    display: none;
}

/* ==========================================================
   RESPONSIVE — le breakpoint (900px) doit rester identique
   à MOBILE_BREAKPOINT dans le <script> plus bas
   ========================================================== */
@media (max-width: 900px) {
    .mobile-menu-btn {
        display: block;
    }

    .top-nav {
        position: fixed;
        top: var(--header-height);
        right: 0;
        width: min(320px, 85vw);
        height: calc(100dvh - var(--header-height));
        background: var(--bg-white);
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
        margin-left: 0;
        padding: 24px;
        gap: 16px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        box-shadow: -8px 0 24px rgba(0, 0, 0, 0.08);
        overflow-y: auto;
        z-index: 950; /* doit rester au-dessus de .nav-backdrop (900) sinon le backdrop intercepte les clics */
    }

    .top-nav.open {
        transform: translateX(0);
    }

    .nav-links-scroll {
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
    }

    #topNav .nav-links-scroll a {
        padding: 14px 16px;
        border-radius: 10px;
    }

    /* Effet au tap, équivalent du :hover PC */
    #topNav .nav-links-scroll a:active,
    #topNav .nav-links-scroll a:focus {
        background: rgba(30, 64, 175, 0.1);
        color: var(--color-primary);
    }

    .nav-actions {
        margin-left: 0;
        margin-top: 16px;
        border-top: 1px solid var(--color-border);
        padding-top: 16px;
    }

    .btn-logout {
        justify-content: center;
        width: 100%;
    }

    /* Effet au tap sur le bouton déconnexion */
    .btn-logout:active {
        background: var(--color-danger-dark) !important;
        opacity: 0.9;
    }

    .nav-backdrop {
        display: block;
        position: fixed;
        inset: 0;
        top: var(--header-height);
        background: rgba(0, 0, 0, 0.35);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 900;
    }

    .nav-backdrop.open {
        opacity: 1;
        pointer-events: auto;
    }
}
</style>

<header class="site-header">
    <div class="container ">
        <a href="../client/home.php" class="logo-placeholder">
            <i class="fa-solid fa-truck-medical logo-icon"></i>
            URGENCES
        </a>

        <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Ouvrir le menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Navigation : liste déroulante sur mobile, barre horizontale sur tablette/PC -->
        <nav class="top-nav" id="topNav">
            <div class="nav-links-scroll">
                <a href="../client/home.php" class="<?= $current_page === 'accueil.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i> Accueil
                </a>

                <!-- Ce lien n'est visible que pour le rôle Redacteur (id_role = 2) -->
                <?php if ($est_redacteur): ?>
                <a href="../client/new-actualites.php" class="<?= $current_page === 'new-actualites.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-pen-fancy"></i> Publier
                </a>
                <?php endif; ?>

                <!-- ne change pas  -->
                <a href="../client/actualites.php" class="<?= $current_page === 'actualites.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-newspaper"></i> Articles
                </a>
                <a href="../client/service-urgence.php" class="<?= $current_page === 'service-urgence.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-tower-broadcast"></i> Service d'urgence
                </a>
                <a href="../client/urgences-carte.php" class="<?= $current_page === 'urgences-carte.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-map-location-dot"></i> Carte
                </a>

            </div>
            <div class="nav-actions">
                <a href="../logout.php" class="btn-logout">
                    <i class="fa-solid fa-user"></i> Déconnexion
                </a>
            </div>
        </nav>

        <div class="nav-backdrop" id="navBackdrop" onclick="toggleMobileMenu()"></div>
    </div>
</header>

<script>
    var MOBILE_BREAKPOINT = 900; // doit correspondre au @media du CSS

    function toggleMobileMenu() {
        document.getElementById('topNav').classList.toggle('open');
        document.getElementById('navBackdrop').classList.toggle('open');
    }

    // Ferme le menu automatiquement quand on clique un lien (mobile)
    document.querySelectorAll('#topNav a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < MOBILE_BREAKPOINT) {
                document.getElementById('topNav').classList.remove('open');
                document.getElementById('navBackdrop').classList.remove('open');
            }
        });
    });

    // Effet "sticky" léger : petite ombre au scroll pour rester cohérent
    // avec l'ambiance de la landing page sans effet brutal de changement de page
    window.addEventListener('scroll', function () {
        const header = document.querySelector('.site-header');
        if (window.scrollY > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>