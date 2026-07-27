<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Urgences Antsiranana">

    <title>Urgence Antsiranana - Services d'Information</title>
    <link rel="stylesheet" href="asset/icon/fontAwesome/all.min.css">
    <link rel="stylesheet" href="asset/css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <div class="container">
            <a href="index.php" class="logo-placeholder">URGENCES</a>
        </div>
    </header>

    <main class="container">
        <!-- Hero Section -->
        <section class="hero fade-in">
            <div class="hero-content">
                <h1>Urgences<br>Antsiranana</h1>
                <p>Accès instantané aux services d'urgence de Diego-Suarez. En situation d'urgence, chaque seconde compte.</p>
                <div class="cta-buttons">
                    <a href="index.php?action=register" class="cta-primary">S'inscrire</a>
                    <a href="index.php?action=login" class="cta-secondary">Se connecter</a>
                </div>
            </div>
            <div class="emergency-banner">
                <h2><i class="fa-solid fa-car-on" style="color: rgb(250, 0, 0);"></i> Pompiers</h2>
                <span class="phone-number">032 63 505 56</span>
                <a href="tel:0326350556" class="btn-call">APPELER</a>
            </div>
        </section>

        <!-- Pharmacie de Garde + Police Centrale -->
        <div class="guard-row">
            <section id="pharmacie" class="guard-block pharmacy-guard fade-in" style="animation-delay: 0.2s;">
                <div class="guard-content">
                    <div class="guard-info">
                        <h2><i class="fa-solid fa-staff-snake" style="color: rgb(99, 230, 190);"></i> Pharmacie de Garde</h2>
                        <p style="font-size: 0.9rem; opacity: 0.9;">Cette semaine :</p>
                    </div>

                    <?php if ($pharmacieGarde): ?>
                        <div class="guard-card-highlight">
                            <div class="guard-name"><?php echo htmlspecialchars($pharmacieGarde['nom_service']); ?></div>
                            <div class="guard-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($pharmacieGarde['nom_quartier'] ?? '—'); ?></div>
                            <a href="tel:<?php echo str_replace(' ', '', $pharmacieGarde['numero_telephone']); ?>" class="guard-call">
                                <span class="phone-number"><?php echo htmlspecialchars($pharmacieGarde['numero_telephone']); ?></span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="guard-card-highlight no-data">
                            <p><i class="fa-solid fa-exclamation-triangle"></i> Aucune pharmacie assignée.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="police" class="guard-block police-guard fade-in" style="animation-delay: 0.3s;">
                <div class="guard-content">
                    <div class="guard-info">
                        <h2><i class="fa-solid fa-shield-halved"></i> Police Centrale</h2>
                    </div>

                    <?php if ($policeCentrale): ?>
                        <div class="guard-card-highlight">
                            <div class="guard-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($policeCentrale['adresse']); ?> (<?php echo htmlspecialchars($policeCentrale['nom_quartier']); ?>)</div>
                            <a href="tel:<?php echo str_replace(' ', '', $policeCentrale['numero_telephone']); ?>" class="guard-call">
                                <span class="phone-number"><?php echo htmlspecialchars($policeCentrale['numero_telephone']); ?></span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="guard-card-highlight no-data">
                            <p><i class="fa-solid fa-exclamation-triangle"></i> Numéro non disponible.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- ================= CE QUE PROPOSE LA PLATEFORME ================= -->
        <section class="services-section fade-in" style="animation-delay: 0.35s;">
            <div class="section-title">
                <h2>Ce que propose la plateforme</h2>
            </div>
            <div class="services-grid">
                <?php foreach ($fonctionnalites as $f): ?>
                    <div class="service-card">
                        <div>
                            <div class="service-icon"><i class="fa-solid <?php echo htmlspecialchars($f['icone']); ?>"></i></div>
                            <h3><?php echo htmlspecialchars($f['titre']); ?></h3>
                            <p><?php echo htmlspecialchars($f['texte']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ================= ACCROCHE INSCRIPTION ================= -->
        <section class="restricted-section fade-in" style="animation-delay: 0.4s;">
            <div class="restricted-content">
                <h2>Débloque l'accès complet</h2>
                <p>Crée un compte gratuit pour consulter l'annuaire complet des services, la carte interactive et les actualités locales.</p>
                <div class="cta-buttons">
                    <a href="index.php?action=register" class="cta-primary">Créer un compte</a>
                    <a href="index.php?action=login" class="cta-secondary">Se connecter</a>
                </div>
            </div>
        </section>

    </main>

    <!-- Bottom Navigation Bar (Mobile) -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <div class="nav-item-icon">
                <i class="fa-solid fa-house"></i>
            </div>
            <div>Accueil</div>
        </a>
        <a href="index.php?action=login" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-door-open"></i></div>
            <div>Connexion</div>
        </a>
        <a href="index.php?action=register" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-user-plus"></i></div>
            <div>Inscription</div>
        </a>
    </nav>

    <footer>
        <div class="container">
            <p>&copy; 2026 Urgences Antsiranana</p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem;">Pour votre sécurité</p>
        </div>
    </footer>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').catch(() => {});
        }

        document.addEventListener('touchend', function(event) {
            if (event.touches.length === 0) {
                document.body.style.zoom = 1;
            }
        }, false);
    </script>
</body>
</html>
