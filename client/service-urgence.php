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
    <style>
        :root {
            --primary: #1e40af;
            --primary-dark: #1a3a99;
            --border-color: #dcdfe3;
            --text-main: #2d3436;
            --text-muted: #6b7280;
            --radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
         body {
            font-family: 'Segoe UI', Arial, sans-serif;
          
        }

        .section-services, .section-pharmacies {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .section-title {
            font-size: 1.5rem;
            color: #1e40af;
            margin-bottom: 1rem;
        }
        .filtre-quartier {
            margin-bottom: 1.5rem;
        }
        .filtre-quartier select {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .card h3 {
            margin: 0 0 0.4rem;
            color: #1e40af;
        }
        .card p {
            margin: 0.2rem 0;
            color: #374151;
            font-size: 0.95rem;
        }
        .badge-garde {
            display: inline-block;
            background: #16a34a;
            color: #fff;
            font-size: 0.75rem;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            margin-bottom: 0.4rem;
        }
        .btn-primary {
            display: inline-block;
            background: #1e40af;
            color: #fff;
            padding: 0.7rem 1.4rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            background: #1d3a99;
        }
        .aucun-resultat {
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php' ?>

    <!-- <section class="hero"> ... </section> -->

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

</body>
</html>