<?php
// La session est déjà démarrée et vérifiée par HomeController (demarrerSession()).
// On se contente ici de lire l'action courante et le rôle pour savoir quoi afficher.
$current_action = $_GET['action'] ?? 'home';

$id_role = $_SESSION['id_role'] ?? null;
if ($id_role === null) {
    $nomRole = $_SESSION['user_role'] ?? '';
    $id_role = $nomRole === 'Administrateur' ? 3 : ($nomRole === 'Redacteur' ? 2 : 1);
}
$est_redacteur = ((int) $id_role === 2);
?>

<style>
    /* ==========================================================
   HEADER — réutilise les variables de home.css (--navy, --navy-dk,
   --red-urgency, --paper, --sage, --line, --ink, --radius).
   Fichier UNIQUE : ne pas dupliquer ce CSS ailleurs.
   ========================================================== */
:root {
    --header-height: 77px; /* ≈ padding 1rem + hauteur logo */
    --color-border: var(--line, #e2e8f0);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ---------- Header ---------- */
.site-header {
    padding: 1rem 0;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: box-shadow 0.3s ease;
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
    background: linear-gradient(135deg, var(--navy-dk) 0%, var(--navy) 100%);
    border: none;
    border-radius: 8px;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: transform 0.2s ease;
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
    color: var(--ink);
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
    color: var(--ink);
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
    color: #64748b;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
}

#topNav .nav-links-scroll a:hover {
    background: var(--sage);
    color: var(--navy-dk);
}

#topNav .nav-links-scroll a.active {
    background: var(--sage);
    color: var(--navy-dk);
    font-weight: 600;
}

#topNav .nav-links-scroll a i {
    font-size: 0.9rem;
    color: inherit;
}

/* ---------- Actions à droite : Profil + petite déconnexion ---------- */
.nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: 16px;
}

.btn-profile {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
    background: var(--navy-dk);
    color: #ffffff;
    text-decoration: none;
    transition: background 0.2s ease;
}

.btn-profile:hover {
    background: var(--navy);
}

.btn-logout-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid var(--color-border);
    color: var(--red-urgency);
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s ease;
    flex-shrink: 0;
}

.btn-logout-icon:hover {
    background: #fdecea;
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
        background: #ffffff;
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
        z-index: 950;
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

    #topNav .nav-links-scroll a:active,
    #topNav .nav-links-scroll a:focus {
        background: var(--sage);
        color: var(--navy-dk);
    }

    .nav-actions {
        margin-left: 0;
        margin-top: 16px;
        border-top: 1px solid var(--color-border);
        padding-top: 16px;
    }

    .btn-profile {
        flex: 1;
        justify-content: center;
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
    <div class="container">
        <a href="index.php?action=home" class="logo-placeholder">
            <i class="fa-solid fa-truck-medical logo-icon"></i>
            URGENCES
        </a>

        <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Ouvrir le menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Navigation : liste déroulante sur mobile, barre horizontale sur tablette/PC -->
        <nav class="top-nav" id="topNav">
            <div class="nav-links-scroll">
                <a href="index.php?action=home" class="<?= $current_action === 'home' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i> Accueil
                </a>

                <!-- Ce lien n'est visible que pour le rôle Redacteur (id_role = 2) -->
                <?php if ($est_redacteur): ?>
                <a href="index.php?action=new-article" class="<?= $current_action === 'new-article' ? 'active' : '' ?>">
                    <i class="fa-solid fa-pen-fancy"></i> Publier
                </a>
                <?php endif; ?>

                <a href="index.php?action=actualites" class="<?= $current_action === 'actualites' ? 'active' : '' ?>">
                    <i class="fa-solid fa-newspaper"></i> Articles
                </a>
                <a href="index.php?action=service-urgence" class="<?= $current_action === 'service-urgence' ? 'active' : '' ?>">
                    <i class="fa-solid fa-tower-broadcast"></i> Service d'urgence
                </a>
                <a href="index.php?action=urgence-carte" class="<?= $current_action === 'urgence-carte' ? 'active' : '' ?>">
                    <i class="fa-solid fa-map-location-dot"></i> Carte
                </a>
            </div>

            <div class="nav-actions">
                <a href="index.php?action=profil" class="btn-profile <?= $current_action === 'profil' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user"></i> Mon profil
                </a>
                <a href="index.php?action=logout" class="btn-logout-icon" title="Déconnexion" aria-label="Déconnexion">
                    <i class="fa-solid fa-right-from-bracket"></i>
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

    document.querySelectorAll('#topNav a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < MOBILE_BREAKPOINT) {
                document.getElementById('topNav').classList.remove('open');
                document.getElementById('navBackdrop').classList.remove('open');
            }
        });
    });

    window.addEventListener('scroll', function () {
        const header = document.querySelector('.site-header');
        if (window.scrollY > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>