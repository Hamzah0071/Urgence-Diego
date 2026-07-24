<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';
require_once '../includes/fonction.php';

// --- Récupération des filtres (GET) ---
$id_quartier   = $_GET['id_quartier'] ?? null;
$id_type       = $_GET['id_type'] ?? null;
$recherche     = trim($_GET['q'] ?? '');

// --- Données pour les listes déroulantes ---
$quartiers = getQuartiers($pdo);

$typesServices = [];
try {
    $stmtTypes = $pdo->query("SELECT id_type, nom_type FROM type_service ORDER BY nom_type");
    $typesServices = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $typesServices = [];
}

// --- Données filtrées ---
$services = getServices($pdo, $id_quartier ?: null, $id_type ?: null, $recherche ?: null);
$pharmacieGarde = getPharmacieGarde($pdo);

// Fonction helper pour assigner les icônes et classes de couleur FontAwesome selon le type
function getServiceTypeMeta($nomType) {
    $typeLower = mb_strtolower($nomType);
    if (str_contains($typeLower, 'pharmacie')) {
        return ['class' => 'type-pharmacie', 'icon' => 'fa-solid fa-pills'];
    } elseif (str_contains($typeLower, 'pompier')) {
        return ['class' => 'type-pompier', 'icon' => 'fa-solid fa-fire-extinguisher'];
    } elseif (str_contains($typeLower, 'police') || str_contains($typeLower, 'ordre') || str_contains($typeLower, 'securite')) {
        return ['class' => 'type-police', 'icon' => 'fa-solid fa-shield-halved'];
    } elseif (str_contains($typeLower, 'hopital') || str_contains($typeLower, 'hôpital') || str_contains($typeLower, 'ambulance') || str_contains($typeLower, 'sante')) {
        return ['class' => 'type-hopital', 'icon' => 'fa-solid fa-hospital'];
    }
    return ['class' => 'type-default', 'icon' => 'fa-solid fa-square-plus'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Services - Urgences Antsiranana</title>

    <link rel="stylesheet" href="../asset/css/client/service-urgence.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="../asset/icon/fontAwesome/all.min.css">
</head>
<body>
<?php include '../includes/header.php' ?>

    <div class="conteneur">
        <!-- SECTION : Pharmacie de garde -->
        <section class="section-pharmacies">
            <h2 class="section-title">
                <i class="fa-solid fa-house-medical-flag"></i> Pharmacie de garde aujourd'hui
            </h2>
            
            <?php if ($pharmacieGarde): ?>
                <div class="grid-cards">
                    <div class="card type-pharmacie garde-card">
                        <div class="card-header">
                            <span class="badge-garde">
                                <i class="fa-solid fa-clock-rotate-left"></i> De garde aujourd'hui
                            </span>
                        </div>
                        <h3><?= htmlspecialchars($pharmacieGarde['nom_service']) ?></h3>
                        <p class="quartier">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($pharmacieGarde['nom_quartier']) ?>
                        </p>
                        <?php if (!empty($pharmacieGarde['numero_telephone'])): ?>
                            <div class="card-call">
                                <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $pharmacieGarde['numero_telephone'])) ?>" class="btn-call">
                                    <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($pharmacieGarde['numero_telephone']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="aucun-resultat">
                    <i class="fa-solid fa-circle-info"></i> Aucune pharmacie de garde enregistrée pour aujourd'hui.
                </p>
            <?php endif; ?>
            
            <div class="pharmacies-more">
                <a href="./urgences-carte.php" class="btn-primary">
                    <i class="fa-solid fa-map"></i> Voir sur une mapes
                </a>
            </div>
        </section>


        <!-- SECTION : Recherche et Filtres -->
        <section class="section-services">
            <h2 class="section-title">
                <i class="fa-solid fa-building-shield"></i> Services d'urgence
            </h2>

            <form method="GET" action="service-urgence.php" class="filtres-form">
                
                <!-- Recherche textuelle -->
                <div class="filter-group search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" 
                           name="q" 
                           id="q" 
                           placeholder="Rechercher par nom, téléphone..." 
                           value="<?= htmlspecialchars($recherche) ?>">
                </div>

                <!-- Filtre type de service -->
                <div class="filter-group">
                    <select name="id_type" id="id_type" onchange="this.form.submit()">
                        <option value="">Tous les types de services</option>
                        <?php foreach ($typesServices as $t): ?>
                            <option value="<?= $t['id_type'] ?>" <?= (string)$id_type === (string)$t['id_type'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nom_type']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtre quartier -->
                <div class="filter-group">
                    <select name="id_quartier" id="id_quartier" onchange="this.form.submit()">
                        <option value="">Tous les quartiers</option>
                        <?php foreach ($quartiers as $q): ?>
                            <option value="<?= $q['id_quartier'] ?>" <?= (string)$id_quartier === (string)$q['id_quartier'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($q['nom_quartier']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="filter-actions">
                    <button type="submit" class="btn-search">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    
                    <?php if (!empty($id_quartier) || !empty($id_type) || !empty($recherche)): ?>
                        <a href="service-urgence.php" class="btn-reset">
                            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                        </a>
                    <?php endif; ?>
                </div>

            </form>

            <!-- Résultats -->
            <?php if (count($services) > 0): ?>
                <div class="grid-cards">
                    <?php foreach ($services as $s): 
                        $nomType = $s['nom_categorie'] ?? $s['nom_type'] ?? '';
                        $meta = getServiceTypeMeta($nomType);
                    ?>
                        <div class="card <?= $meta['class'] ?>">
                            <div class="card-header">
                                <span class="card-type-badge">
                                    <i class="<?= $meta['icon'] ?>"></i> <?= htmlspecialchars($nomType) ?>
                                </span>
                            </div>
                            <h3><?= htmlspecialchars($s['nom_service']) ?></h3>
                            <p class="quartier">
                                <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($s['nom_quartier']) ?>
                            </p>
                            <?php if (!empty($s['numero_telephone'])): ?>
                                <div class="card-call">
                                    <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['numero_telephone'])) ?>" class="btn-call">
                                        <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($s['numero_telephone']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="aucun-resultat">
                    <i class="fa-solid fa-triangle-exclamation"></i> Aucun service ne correspond à votre recherche.
                </p>
            <?php endif; ?>
        </section>
            
        
    </div>

</body>
</html>