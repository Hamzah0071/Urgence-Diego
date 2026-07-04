<?php
/**
 * Page d'accueil - Urgences Antsiranana
 * Optimisée Mobile-First pour les utilisateurs en situation d'urgence
 */

require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);

// Récupérer les données
$pharmacie_garde = getPharmacieGarde($pdo);

// Si l'utilisateur est connecté, récupérer tous les services
if ($is_logged_in) {
    $services = getServices($pdo);
    // Organiser les services par catégorie
    $services_by_category = [];
    foreach ($services as $service) {
        $cat = $service['nom_categorie'];
        if (!isset($services_by_category[$cat])) {
            $services_by_category[$cat] = [];
        }
        $services_by_category[$cat][] = $service;
    }
}

// Définir les icônes par catégorie
$category_icons = [
    'Pharmacie' => '💊',
    'Pompier' => '🚒',
    'Force de l\'ordre' => '🛡️',
    'Hôpital' => '🏥'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Urgences Antsiranana">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 192 192'><rect fill='%231e40af' width='192' height='192'/><text x='50%' y='50%' font-size='100' fill='white' text-anchor='middle' dy='.3em'>🚨</text></svg>">
    <title>Urgence Antsiranana - Services d'Information</title>
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --blue-deep: #1e40af;
            --red-emergency: #dc2626;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --transition: all 0.3s ease;
            --safe-area-inset-bottom: env(safe-area-inset-bottom, 0);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        .phone-number {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header Mobile-First */
        header {
            padding: 1rem 0;
            background: var(--bg-white);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-placeholder {
            width: 120px;
            height: 45px;
            background: linear-gradient(135deg, var(--blue-deep) 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: white;
            font-size: 0.7rem;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .logo-placeholder:active {
            transform: scale(0.95);
        }

        .nav-links {
            display: none;
        }

        .mobile-menu-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hero Section Mobile-First */
        .hero {
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .hero-content h1 {
            font-size: 2rem;
            line-height: 1.2;
            margin-bottom: 1rem;
            color: var(--blue-deep);
        }

        .hero-content p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .emergency-banner {
            background: var(--red-emergency);
            color: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
            position: relative;
            overflow: hidden;
            order: -1;
        }

        .emergency-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .emergency-banner h2 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .emergency-banner .phone-number {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 1rem;
        }

        .btn-call {
            display: inline-block;
            background: white;
            color: var(--red-emergency);
            padding: 0.9rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            width: 100%;
            text-align: center;
        }

        .btn-call:active {
            transform: scale(0.98);
        }

        /* Services Grid */
        .services-section {
            padding: 2rem 0;
        }

        .section-title {
            margin-bottom: 1.5rem;
        }

        .section-title h2 {
            font-size: 1.5rem;
            color: var(--text-dark);
        }

        .services-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .service-card {
            background: var(--bg-white);
            padding: 1.25rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: var(--transition);
            border: 2px solid transparent;
            cursor: pointer;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .service-card:active {
            transform: scale(0.98);
            border-color: var(--blue-deep);
        }

        .service-icon {
            width: 45px;
            height: 45px;
            background: #eff6ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue-deep);
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .service-card h3 {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .service-card p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .service-card .phone-number {
            font-size: 1.1rem;
            color: var(--blue-deep);
        }

        /* Pharmacy Focus */
        .pharmacy-guard {
            background: var(--blue-deep);
            color: white;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 20px;
        }

        .pharmacy-content {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .pharmacy-info h2 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .pharmacy-card-highlight {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .pharmacy-name {
            font-size: 1.8rem;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 0.5rem;
        }

        .pharmacy-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.9;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .pharmacy-dates {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 1rem;
        }

        /* Restricted Content */
        .restricted-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 2rem 1rem;
            margin: 2rem 0;
            border-radius: 20px;
        }

        .restricted-content {
            text-align: center;
        }

        .restricted-content h2 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--blue-deep);
        }

        .restricted-content p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .cta-buttons a {
            padding: 1rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            display: block;
        }

        .cta-primary {
            background: var(--blue-deep);
            color: white;
        }

        .cta-primary:active {
            transform: scale(0.98);
        }

        .cta-secondary {
            background: white;
            color: var(--blue-deep);
            border: 2px solid var(--blue-deep);
        }

        .cta-secondary:active {
            background: var(--blue-deep);
            color: white;
        }

        .no-data {
            text-align: center;
            padding: 1.5rem;
            background: #fef2f2;
            border-radius: 12px;
            color: #991b1b;
            font-size: 0.9rem;
        }

        footer {
            padding: 2rem 0;
            text-align: center;
            color: var(--text-muted);
            border-top: 1px solid #e2e8f0;
            margin-bottom: calc(80px + var(--safe-area-inset-bottom));
            font-size: 0.85rem;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-white);
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
            z-index: 99;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
        }

        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 0;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.75rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .nav-item.active {
            color: var(--blue-deep);
        }

        .nav-item-icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        main {
            padding-bottom: 1rem;
        }

        /* Desktop Styles */
        @media (min-width: 768px) {
            .hero {
                grid-template-columns: 1.2fr 0.8fr;
                gap: 2rem;
                display: grid;
            }

            .emergency-banner {
                order: 0;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .emergency-banner .phone-number {
                font-size: 2.5rem;
            }

            .services-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }

            .nav-links {
                display: flex;
                gap: 1.5rem;
            }

            .nav-links a {
                text-decoration: none;
                color: var(--text-dark);
                font-weight: 500;
                transition: var(--transition);
            }

            .nav-links a:hover {
                color: var(--blue-deep);
            }

            .auth-buttons {
                display: flex;
                gap: 1rem;
            }

            .btn-login, .btn-register {
                padding: 0.6rem 1.2rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: var(--transition);
                font-size: 0.9rem;
                border: none;
                cursor: pointer;
            }

            .btn-login {
                color: var(--blue-deep);
                border: 2px solid var(--blue-deep);
                background: transparent;
            }

            .btn-login:hover {
                background: var(--blue-deep);
                color: white;
            }

            .btn-register {
                background: var(--blue-deep);
                color: white;
            }

            .btn-register:hover {
                background: #1e3a8a;
            }

            .bottom-nav {
                display: none;
            }

            main {
                padding-bottom: 2rem;
            }

            footer {
                margin-bottom: 0;
            }

            .mobile-menu-btn {
                display: none;
            }

            .services-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }

            .cta-buttons {
                flex-direction: row;
            }

            .cta-buttons a {
                flex: 1;
            }
        }

        @media (min-width: 1024px) {
            .services-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <a href="index.php" class="logo-placeholder">URGENCES</a>
            <nav class="nav-links">
                <a href="#services">Services</a>
                <a href="#pharmacie">Pharmacie</a>
                
                <?php if ($is_logged_in): ?>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="dashboard.php" style="color: var(--blue-deep); font-weight: 600;">👤 <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></a>
                        <a href="logout.php" style="background: var(--red-emergency); color: white; padding: 0.6rem 1.2rem; border-radius: 8px; text-decoration: none; font-weight: 600;">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn-login">Connexion</a>
                        <a href="register.php" class="btn-register">Inscription</a>
                    </div>
                <?php endif; ?>
            </nav>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        </div>
    </header>

    <main class="container">
        <!-- Hero Section -->
        <section class="hero fade-in">
            <div class="hero-content">
                <h1>Urgences<br>Antsiranana</h1>
                <p>Accès instantané aux services d'urgence de Diego-Suarez. En situation d'urgence, chaque seconde compte.</p>
                <?php if (!$is_logged_in): ?>
                    <div class="cta-buttons">
                        <a href="register.php" class="cta-primary">S'inscrire</a>
                        <a href="login.php" class="cta-secondary">Se connecter</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="emergency-banner">
                <h2><i class="fa-solid fa-car-on" style="color: rgb(250, 0, 0);"></i> Pompiers</h2>
                <!-- automaique avec les numero -->
                 <!-- php echo str_replace(' ', '', $pharmacie_garde['numero_telephone']); ?> -->
                <span class="phone-number">032 63 505 56</span>
                <a href="tel:0326350556" class="btn-call">APPELER</a>
            </div>
        </section>

        <!-- Pharmacy Guard (Visible pour tous) -->
        <section id="pharmacie" class="pharmacy-guard fade-in" style="animation-delay: 0.2s;">
            <div class="pharmacy-content">
                <div class="pharmacy-info">
                    <h2><i class="fa-solid fa-staff-snake" style="color: rgb(99, 230, 190);"></i> Pharmacie de Garde</h2>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Cette semaine :</p>
                </div>
                
                <?php if ($pharmacie_garde): ?>
                    <div class="pharmacy-card-highlight">
                        <div class="pharmacy-name"><?php echo htmlspecialchars($pharmacie_garde['nom_service']); ?></div>
                        <div class="pharmacy-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($pharmacie_garde['nom_quartier']); ?></div>
                        <a href="tel:<?php echo str_replace(' ', '', $pharmacie_garde['numero_telephone']); ?>" style="display: inline-block; background: white; color: var(--blue-deep); padding: 0.75rem 1rem; border-radius: 12px; text-decoration: none; font-weight: 700; margin-top: 0.75rem;">
                            <span class="phone-number"><?php echo htmlspecialchars($pharmacie_garde['numero_telephone']); ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pharmacy-card-highlight">
                        <p><i class="fa-solid fa-exclamation-triangle"></i> Aucune pharmacie assignée.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Services Section (Restreint aux utilisateurs connectés) -->
        <?php if ($is_logged_in): ?>
            <section id="services" class="services-section fade-in" style="animation-delay: 0.4s;">
                <div class="section-title">
                    <h2>Services d'Intervention</h2>
                </div>
                
                <?php if (count($services) > 0): ?>
                    <div class="services-grid">
                        <?php foreach ($services as $service): 
                            $icon = isset($category_icons[$service['nom_categorie']]) 
                                    ? $category_icons[$service['nom_categorie']] 
                                    : '<i class="fa-solid fa-phone"></i>';
                        ?>
                            <a href="tel:<?php echo str_replace(' ', '', $service['numero_telephone']); ?>" style="text-decoration: none; color: inherit;">
                                <div class="service-card">
                                    <div>
                                        <div class="service-icon"><?php echo $icon; ?></div>
                                        <h3><?php echo htmlspecialchars($service['nom_service']); ?></h3>
                                        <p><?php echo htmlspecialchars($service['nom_categorie']); ?></p>
                                    </div>
                                    <div class="phone-number"><?php echo htmlspecialchars($service['numero_telephone']); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p><i class="fa-solid fa-ban"></i> Aucun service disponible.</p>
                    </div>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <!-- Section Restreinte pour les visiteurs -->
            <section class="restricted-section fade-in" style="animation-delay: 0.4s;">
                <div class="restricted-content">
                    <h2><i class="fa-solid fa-lock"></i> Services Détaillés</h2>
                    <p>Inscrivez-vous pour accéder à la liste complète des services d'urgence de votre quartier.</p>
                    <div class="cta-buttons">
                        <a href="register.php" class="cta-primary">Créer un compte</a>
                        <a href="login.php" class="cta-secondary">Se connecter</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- Bottom Navigation Bar (Mobile) -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <div class="nav-item-icon">
                <i class="fa-solid fa-house"></i>
            </div>
            <div>Accueil</div>
        </a>
        <?php if ($is_logged_in): ?>
            <a href="dashboard.php" class="nav-item">
                <div class="nav-item-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>Profil</div>
            </a>
            <a href="logout.php" class="nav-item">
                <div class="nav-item-icon">
                    <i class="fa-solid fa-door-closed"></i>
                </div>
                <div>Quitter</div>
            </a>
        <?php else: ?>
            <a href="login.php" class="nav-item">
                <div class="nav-item-icon"><i class="fa-solid fa-door-open" "></i></div>
                <div>Connexion</div>
            </a>
            <a href="register.php" class="nav-item">
                <div class="nav-item-icon"><i class="fa-solid fa-user-plus"></i></i></div>
                <div>Inscription</div>
            </a>
        <?php endif; ?>
    </nav>

    <footer>
        <div class="container">
            <p>&copy; 2026 Urgences Antsiranana</p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem;">Pour votre sécurité</p>
        </div>
    </footer>

    <script>
        // Service Worker pour PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').catch(() => {});
        }

        function toggleMobileMenu() {
            // À implémenter selon les besoins
        }

        // Empêcher le zoom sur double-tap
        document.addEventListener('touchend', function(event) {
            if (event.touches.length === 0) {
                document.body.style.zoom = 1;
            }
        }, false);
    </script>
</body>
</html>