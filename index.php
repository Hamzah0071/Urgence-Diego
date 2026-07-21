<!-- landign page -->
<?php
/**
 * Page d'accueil - Urgences Antsiranana
 * Optimisée Mobile-First pour les utilisateurs en situation d'urgence
 */

require_once './includes/db_connect.php';

// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION["id_utilisateur"]);

// Récupérer les données
$pharmacie_garde = getPharmacieGarde($pdo);

// La police n'a pas de rotation "de garde" : on affiche le poste central fixe
// enregistré dans la table service (libelle = 'Police Centrale')
$stmt_police = $pdo->prepare(
    "SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, s.adresse, q.nom_quartier
     FROM service s
     JOIN quartier q ON s.id_quartier = q.id_quartier
     WHERE s.libelle = 'Police Centrale'
     LIMIT 1"
);
$stmt_police->execute();
$police_centrale = $stmt_police->fetch(PDO::FETCH_ASSOC);

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

    <title>Urgence Antsiranana - Services d'Information</title>
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">
    <link rel="stylesheet" href="./asset/css/index.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    
</head>
<body>

    <header>
        <div class="container">
            <a href="index.php" class="logo-placeholder">URGENCES</a>
            <nav class="nav-links">
                
                    <div class="auth-buttons">
                        <a href="login.php" class="btn-login">Connexion</a>
                        <a href="register.php" class="btn-register">Inscription</a>
                    </div>
                
            </nav>
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

        <!-- Pharmacie de Garde + Police Centrale : même famille de bleu -->
        <div class="guard-row">
            <!-- Pharmacy Guard (Visible pour tous) -->
            <section id="pharmacie" class="guard-block pharmacy-guard fade-in" style="animation-delay: 0.2s;">
                <div class="guard-content">
                    <div class="guard-info">
                        <h2><i class="fa-solid fa-staff-snake" style="color: rgb(99, 230, 190);"></i> Pharmacie de Garde</h2>
                        <p style="font-size: 0.9rem; opacity: 0.9;">Cette semaine :</p>
                    </div>

                    <?php if ($pharmacie_garde): ?>
                        <div class="guard-card-highlight">
                            <div class="guard-name"><?php echo htmlspecialchars($pharmacie_garde['nom_service']); ?></div>
                            <div class="guard-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($pharmacie_garde['nom_quartier']); ?></div>
                            <a href="tel:<?php echo str_replace(' ', '', $pharmacie_garde['numero_telephone']); ?>" class="guard-call">
                                <span class="phone-number"><?php echo htmlspecialchars($pharmacie_garde['numero_telephone']); ?></span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="guard-card-highlight">
                            <p><i class="fa-solid fa-exclamation-triangle"></i> Aucune pharmacie assignée.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Police Centrale (poste fixe, visible pour tous) -->
            <section id="police" class="guard-block police-guard fade-in" style="animation-delay: 0.3s;">
                <div class="guard-content">
                    <div class="guard-info">
                        <h2><i class="fa-solid fa-shield-halved"></i> Police Centrale</h2>
                    </div>

                    <?php if ($police_centrale): ?>
                        <div class="guard-card-highlight">
                            <div class="guard-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($police_centrale['adresse']); ?> (<?php echo htmlspecialchars($police_centrale['nom_quartier']); ?>)</div>
                            <a href="tel:<?php echo str_replace(' ', '', $police_centrale['numero_telephone']); ?>" class="guard-call">
                                <span class="phone-number"><?php echo htmlspecialchars($police_centrale['numero_telephone']); ?></span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="guard-card-highlight">
                            <p><i class="fa-solid fa-exclamation-triangle"></i> Numéro non disponible.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

       
    </main>

    <!-- Bottom Navigation Bar (Mobile) -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <div class="nav-item-icon">
                <i class="fa-solid fa-house"></i>
            </div>
            <div>Accueil</div>
        </a>
    
        <a href="login.php" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-door-open"></i></div>
            <div>Connexion</div>
        </a>
        <a href="register.php" class="nav-item">
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
        // Service Worker pour PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').catch(() => {});
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