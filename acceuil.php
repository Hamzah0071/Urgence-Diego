<!-- version responsive : mobile / tablette / PC -->
<?php
/**
 * Page d'accueil - Urgences Antsiranana
 * Page réservée aux utilisateurs connectés (client)
 */

require_once 'db_connect.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$user_nom = $_SESSION['user_nom'];
$user_prenom = $_SESSION['user_prenom'];
$user_quartier = $_SESSION['user_quartier'] ?? null;

// Récupérer les services du quartier de l'utilisateur (tous les services si pas de quartier défini)
$services = getServices($pdo, $user_quartier);

// Récupérer la pharmacie de garde
$pharmacie_garde = getPharmacieGarde($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Accueil - Urgences Antsiranana</title>
    <link rel="stylesheet" href="asset/css/style.css">
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">

</head>
<body>
    <header>
        <div class="container header-inner">
            <a href="#" style="text-decoration: none;">
                <div class="logo-placeholder">URGENCES</div>
            </a>

            <!-- Navigation horizontale visible uniquement sur tablette/PC -->
            <nav class="top-nav">
                <a href="#" class="active"><i class="fa-solid fa-house"></i> Accueil</a>
                <a href="#"><i class="fa-solid fa-location-dot"></i> Services</a>
            </nav>

            <div class="user-info">
                <span class="user-name"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($user_prenom); ?></span>
                <a href="logout.php" class="btn-logout">Déconnexion</a>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- En-tête -->
            <div class="dashboard-header">
                <h1>Bienvenue, <?php echo htmlspecialchars($user_prenom); ?> !</h1>
                <p>Voici les services d'urgence disponibles dans votre quartier.</p>
            </div>

            <div class="content-layout">
                <!-- Colonne principale : Services -->
                <div class="content-main">
                    <div class="card">
                        <h2><i class="fa-solid fa-house-fire" style="color: rgb(250, 0, 0);"></i> Services d'Urgence de votre Quartier</h2>

                        <?php if (count($services) > 0): ?>
                            <div class="services-grid">
                                <?php foreach ($services as $service): ?>
                                    <a href="tel:<?php echo str_replace(' ', '', $service['numero_telephone']); ?>" style="text-decoration: none; color: inherit;">
                                        <div class="service-item">
                                            <h3><?php echo htmlspecialchars($service['nom_service']); ?></h3>
                                            <p><?php echo htmlspecialchars($service['nom_categorie']); ?> • <?php echo htmlspecialchars($service['nom_quartier']); ?></p>
                                            <div class="service-phone">
                                                <?php echo htmlspecialchars($service['numero_telephone']); ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <p><i class="fa-solid fa-ban"></i> Aucun service disponible actuellement pour votre quartier.</p>
                                <p style="font-size: 0.85rem;">Les services seront affichés dès qu'ils seront ajoutés.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Colonne latérale : Profil + Pharmacie de garde -->
                <div class="content-side">
                    <?php if ($pharmacie_garde): ?>
                        <div class="pharmacy-card">
                            <h2><i class="fa-solid fa-staff-snake" style="color: rgb(99, 230, 190);"></i> Pharmacie de Garde Cette Semaine</h2>
                            <div class="pharmacy-name"><?php echo htmlspecialchars($pharmacie_garde['nom_service']); ?></div>
                            <div class="pharmacy-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($pharmacie_garde['nom_quartier']); ?></div>
                            <div class="pharmacy-phone">
                                <a href="tel:<?php echo str_replace(' ', '', $pharmacie_garde['numero_telephone']); ?>">
                                    <?php echo htmlspecialchars($pharmacie_garde['numero_telephone']); ?>
                                </a>
                            </div>
                            <a href="tel:<?php echo str_replace(' ', '', $pharmacie_garde['numero_telephone']); ?>" class="btn-call">
                                APPELER
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <h2><i class="fa-solid fa-user"></i> Votre Profil</h2>
                        <div class="card-content">
                            <div class="info-row">
                                <span class="info-label">Nom complet</span>
                                <span class="info-value"><?php echo htmlspecialchars($user_nom . ' ' . $user_prenom); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($user_email); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Navigation basse (Mobile uniquement, cachée dès 768px) -->
    <nav class="bottom-nav">
        <a href="accueil.php" class="nav-item active">
            <div class="nav-item-icon"><i class="fa-solid fa-house"></i></div>
            <div>Accueil</div>
        </a>
        <a href="index.php" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div>Services</div>
        </a>
        <a href="logout.php" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-door-open"></i></div>
            <div>Quitter</div>
        </a>
    </nav>

    <footer>
        <p>&copy; 2026 Urgences Antsiranana. Tous droits réservés.</p>
    </footer>
</body>
</html>
