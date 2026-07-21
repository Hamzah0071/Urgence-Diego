<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';

// --- Quartier sélectionné (filtre) ---
$id_quartier = $_GET['id_quartier'] ?? null;

// --- Données ---
$quartiers = getQuartiers($pdo);
$services = getServices($pdo, $id_quartier ?: null);
$pharmacieGarde = getPharmacieGarde($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Services - Urgences Antsiranana</title>

    <link rel="stylesheet" href="../asset/css/client/service-urgence.css">
</head>
<body>
<?php include '../includes/header.php' ?>

    <!-- <section class="hero"> ... </section> -->
     <div class="conteneur">
    <!-- SECTION : Services par quartier -->
        <section class="section-services">
            <h2 class="section-title">Services par quartier</h2>
    
            <form method="GET" class="filtre-quartier">
                <label for="id_quartier">Choisir un quartier :</label>
                <select name="id_quartier" id="id_quartier" onchange="this.form.submit()">
                    <option value="">Tous les quartiers</option>
                    <?php foreach ($quartiers as $q): ?>
                        <option value="<?= $q['id_quartier'] ?>" <?= (string)$id_quartier === (string)$q['id_quartier'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($q['nom_quartier']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
                    
            <?php if (count($services) > 0): ?>
                <div class="grid-cards">
                    <?php foreach ($services as $s): ?>
                        <div class="card">
                            <h3><?= htmlspecialchars($s['nom_service']) ?></h3>
                            <p><?= htmlspecialchars($s['nom_categorie']) ?></p>
                            <p>📍 <?= htmlspecialchars($s['nom_quartier']) ?></p>
                            <?php if (!empty($s['numero_telephone'])): ?>
                                <p>📞 <a href="tel:<?= htmlspecialchars($s['numero_telephone']) ?>"><?= htmlspecialchars($s['numero_telephone']) ?></a></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="aucun-resultat">Aucun service trouvé pour ce quartier.</p>
            <?php endif; ?>
        </section>
            
        <!-- SECTION : Pharmacie de garde -->
        <section class="section-pharmacies">
            <h2 class="section-title">Pharmacie de garde aujourd'hui</h2>
            
            <?php if ($pharmacieGarde): ?>
                <div class="grid-cards">
                    <div class="card">
                        <span class="badge-garde">De garde</span>
                        <h3><?= htmlspecialchars($pharmacieGarde['nom_service']) ?></h3>
                        <p>📍 <?= htmlspecialchars($pharmacieGarde['nom_quartier']) ?></p>
                        <?php if (!empty($pharmacieGarde['numero_telephone'])): ?>
                            <p>📞 <a href="tel:<?= htmlspecialchars($pharmacieGarde['numero_telephone']) ?>"><?= htmlspecialchars($pharmacieGarde['numero_telephone']) ?></a></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="aucun-resultat">Aucune pharmacie de garde enregistrée pour aujourd'hui.</p>
            <?php endif; ?>
            
            <a href="pharmacies.php" class="btn-primary">Voir toutes les pharmacies</a>
        </section>
    </div>

</body>
</html>