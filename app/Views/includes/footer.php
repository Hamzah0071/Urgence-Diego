<?php
// $current_action et $est_redacteur sont déjà définis par includes/header.php,
// requis juste avant dans la même page. Filet de sécurité si jamais ce
// footer était inclus seul un jour.
$current_action = $current_action ?? ($_GET['action'] ?? 'home');
$est_redacteur = $est_redacteur ?? false;
?>
<style>
    /* ==========================================================
   FOOTER — réutilise les variables de home.css (--navy-dk, --paper,
   --sage, --line, --ink). Fichier UNIQUE : ne pas dupliquer ailleurs.
   ========================================================== */
.site-footer {
    background: linear-gradient(135deg, var(--navy-dk) 0%, #0f172a 100%);
    color: #ffffff;
    margin-top: 60px;
}

.site-footer .footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 48px 24px 24px;
}

.footer-top {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr;
    gap: 32px;
    padding-bottom: 32px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}

.footer-brand .logo-placeholder {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0.6rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    margin-bottom: 14px;
}

.footer-brand p {
    font-size: 0.88rem;
    line-height: 1.6;
    color: #cbd5e1;
    margin: 0;
    max-width: 320px;
}

.footer-col h4 {
    font-family: 'Fraunces', serif;
    font-size: 1rem;
    margin: 0 0 16px;
    color: #ffffff;
}

.footer-col ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer-col a {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #cbd5e1;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s ease;
}

.footer-col a:hover,
.footer-col a.active {
    color: #ffffff;
}

.footer-col a i {
    width: 16px;
    text-align: center;
    font-size: 0.85rem;
    color: #93a5c9;
}

.footer-urgence a {
    font-family: 'Space Mono', monospace;
    font-weight: 700;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 20px;
    font-size: 0.8rem;
    color: #94a3b8;
}

.footer-bottom a {
    color: #cbd5e1;
    text-decoration: none;
}

.footer-bottom a:hover {
    color: #ffffff;
}

@media (max-width: 768px) {
    .footer-top {
        grid-template-columns: 1fr;
        gap: 28px;
    }

    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="index.php?action=home" class="logo-placeholder">
                    <i class="fa-solid fa-truck-medical"></i> URGENCES
                </a>
                <p>
                    Urgences Antsiranana centralise les numéros et adresses des services
                    d'urgence de Diego-Suarez : pharmacies de garde, pompiers, police et hôpitaux.
                </p>
            </div>

            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="index.php?action=home" class="<?= $current_action === 'home' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Accueil</a></li>
                    <li><a href="index.php?action=actualites" class="<?= $current_action === 'actualites' ? 'active' : '' ?>"><i class="fa-solid fa-newspaper"></i> Actualités</a></li>
                    <li><a href="index.php?action=service-urgence" class="<?= $current_action === 'service-urgence' ? 'active' : '' ?>"><i class="fa-solid fa-tower-broadcast"></i> Services d'urgence</a></li>
                    <li><a href="index.php?action=urgence-carte" class="<?= $current_action === 'urgence-carte' ? 'active' : '' ?>"><i class="fa-solid fa-map-location-dot"></i> Carte</a></li>
                    <?php if ($est_redacteur): ?>
                        <li><a href="index.php?action=new-article" class="<?= $current_action === 'new-article' ? 'active' : '' ?>"><i class="fa-solid fa-pen-fancy"></i> Publier un article</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?action=don" class="<?= $current_action === 'don' ? 'active' : '' ?>"><i class="fa-solid fa-hand-holding-heart"></i> Faire un don</a></li>
                    <li><a href="index.php?action=profil" class="<?= $current_action === 'profil' ? 'active' : '' ?>"><i class="fa-solid fa-user"></i> Mon profil</a></li>
                </ul>
            </div>

            <div class="footer-col footer-urgence">
                <h4>Numéros utiles</h4>
                <ul>
                    <li><a href="tel:0326350556"><i class="fa-solid fa-truck-medical"></i> Pompiers </a></li>
                    <li><a href="index.php?action=service-urgence"><i class="fa-solid fa-house-medical-flag"></i> Pharmacie de garde</a></li>
                    <li><a href="index.php?action=service-urgence"><i class="fa-solid fa-shield-halved"></i> Police centrale</a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Urgences Antsiranana — Pour votre sécurité.</span>
            <a href="index.php?action=logout"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </div>
    </div>
</footer>

<script>
    // Menu mobile (header.php gère déjà toggleMobileMenu() lui-même ;
    // ce script n'est nécessaire ici que si footer.php est un jour
    // inclus sans header.php).
    if (typeof toggleMobileMenu !== 'function') {
        window.toggleMobileMenu = function () {
            document.getElementById('topNav')?.classList.toggle('open');
            document.getElementById('navBackdrop')?.classList.toggle('open');
        };
    }
</script>
</body>
</html>