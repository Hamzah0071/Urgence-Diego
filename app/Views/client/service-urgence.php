<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Services - Urgences Antsiranana</title>

    <link rel="stylesheet" href="public/asset/css/client/home.css">
    <link rel="stylesheet" href="public/asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* =========================================================
           Compléments propres à cette page : réutilise les variables
           de home.css (--navy, --navy-dk, --paper, --sage, --line,
           --radius) au lieu d'une palette séparée. Cette page ajoute
           juste --pharmacie-color (absente de home.css, 4e catégorie).
           MOBILE FIRST : on part du plus contraint, puis on élargit
           avec des min-width.
        ========================================================= */
        :root {
            --pharmacie-color: #10b981;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--paper);
            color: var(--ink);
        }

        .conteneur {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px 20px 60px;
        }

        /* ---------- Section Pharmacie de garde + Pompiers ---------- */
        .section-pharmacies {
            margin-bottom: 30px;
        }

        .garde-pompier-wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .garde-pompier-item {
            width: 100%;
            min-width: 0;
        }

        .section-title {
            font-family: 'Fraunces', serif;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            color: var(--navy-dk);
        }

        /* ---------- Cartes (communes) ---------- */
        .card {
            background: #ffffff;
            margin-bottom: 12px;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            border-left: 4px solid var(--line);
            min-width: 0;
            animation: fadeIn 0.25s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .card.type-pharmacie { border-left-color: var(--pharmacie-color); }
        .card.type-pompier   { border-left-color: var(--pompier-color); }
        .card.type-police    { border-left-color: var(--police-color); }
        .card.type-hopital   { border-left-color: var(--ambulance-color); }

        .btn-appel-service.btn-pharmacie { background: var(--pharmacie-color); }
        .btn-appel-service.btn-pompier   { background: var(--pompier-color); }
        .btn-appel-service.btn-police    { background: var(--police-color); }
        .btn-appel-service.btn-hopital   { background: var(--ambulance-color); }

        .card h3 {
            font-family: 'Fraunces', serif;
            font-size: 1rem;
            margin: 0 0 6px 0;
            color: var(--navy-dk);
            word-break: break-word;
        }

        .card .quartier {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ---------- Bouton "Appeler" propre à cette page : bloc pleine
           largeur, cible tactile confortable — différent du .btn-call
           "pilule" de home.css, donc nom de classe distinct pour éviter
           toute confusion/collision. ---------- */
        .btn-appel-service {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: var(--navy-dk);
            color: #fff;
            padding: 12px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            width: 100%;
            min-height: 44px;
        }

        .btn-appel-service:active {
            opacity: 0.85;
        }

        /* ---------- Pharmacie de garde ---------- */
        .garde-card {
            background: var(--sage);
            border: 2px solid var(--pharmacie-color);
        }

        .badge-garde {
            background: var(--pharmacie-color);
            color: #fff;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .aucun-resultat {
            color: #64748b;
            font-size: 0.9rem;
            font-style: italic;
        }

        /* ---------- Formulaire de filtre ---------- */
        .filtres-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }

        .filtres-form input[type="text"],
        .filtres-form select {
            flex: 1 1 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--line);
            font-size: 0.95rem;
            min-height: 44px;
            font-family: inherit;
        }

        .filtres-form input[type="text"]:focus,
        .filtres-form select:focus {
            outline: none;
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-search {
            background: var(--navy-dk);
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            min-height: 44px;
            width: 100%;
        }

        .btn-search:hover {
            background: var(--navy);
        }

        /* ---------- Layout 3 colonnes (services) ---------- */
        .services-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .column-title {
            font-family: 'Fraunces', serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--navy-dk);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--line);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .service-column {
            background: var(--sage);
            padding: 14px;
            border-radius: var(--radius);
            height: fit-content;
            min-width: 0;
        }

        /* ---------- Voir plus / Voir moins (colonnes surchargées) ---------- */
        .toggle-more {
            display: none;
        }

        .column-cards.has-more .card:nth-child(n+5) {
            display: none;
        }

        .toggle-more:checked + .column-cards .card {
            display: block;
        }

        .btn-voir-plus,
        .btn-voir-moins {
            display: block;
            text-align: center;
            padding: 10px;
            margin-top: 6px;
            background: #e2e8f0;
            color: var(--ink);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            min-height: 44px;
            line-height: 24px;
            user-select: none;
        }

        .btn-voir-plus:hover,
        .btn-voir-moins:hover {
            background: #cbd5e1;
        }

        .btn-voir-moins {
            display: none;
        }

        .toggle-more:checked ~ .btn-voir-plus {
            display: none;
        }

        .toggle-more:checked ~ .btn-voir-moins {
            display: block;
        }

        /* =========================================================
           TABLETTE (>= 576px) : formulaire sur une seule ligne
        ========================================================= */
        @media (min-width: 576px) {
            .filtres-form input[type="text"] {
                flex: 1 1 auto;
                min-width: 180px;
            }

            .filtres-form select {
                flex: 0 1 220px;
            }

            .btn-search {
                width: auto;
                flex: 0 0 auto;
            }
        }

        /* =========================================================
           TABLETTE (>= 768px) : pharmacie/pompier côte à côte
        ========================================================= */
        @media (min-width: 768px) {
            .garde-pompier-wrapper {
                flex-direction: row;
            }

            .garde-pompier-item {
                flex: 1;
            }
        }

        /* =========================================================
           TABLETTE LARGE (>= 900px) : 2 colonnes de services
        ========================================================= */
        @media (min-width: 900px) {
            .services-layout {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* =========================================================
           DESKTOP (>= 1200px) : 3 colonnes de services
        ========================================================= */
        @media (min-width: 1200px) {
            .services-layout {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        /* =========================================================
           PETIT MOBILE (<= 380px)
        ========================================================= */
        @media (max-width: 380px) {
            .conteneur {
                padding: 8px;
            }

            .card {
                padding: 12px;
            }

            .card h3 {
                font-size: 0.9rem;
            }

            .btn-appel-service,
            .btn-search {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>

    <div class="conteneur">
        <!-- SECTION : Pharmacie de garde & Pompier -->
        <section class="section-pharmacies">
            <div class="garde-pompier-wrapper">

                <!-- Pharmacie de garde -->
                <div class="garde-pompier-item">
                    <h2 class="section-title"><i class="fa-solid fa-house-medical-flag"></i> Pharmacie de garde</h2>
                    <?php if ($pharmacieGarde): ?>
                        <div class="card type-pharmacie garde-card">
                            <div class="card-header" style="margin-bottom: 10px;">
                                <span class="badge-garde">En service</span>
                            </div>
                            <h3><?= htmlspecialchars($pharmacieGarde['nom_service']) ?></h3>
                            <p class="quartier"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($pharmacieGarde['nom_quartier'] ?? '—') ?></p>
                            <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $pharmacieGarde['numero_telephone'])) ?>" class="btn-appel-service btn-pharmacie">
                                <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($pharmacieGarde['numero_telephone']) ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="aucun-resultat">Aucune pharmacie de garde.</p>
                    <?php endif; ?>
                </div>

                <!-- Pompier (à côté de la garde) -->
                <!-- Pompier (à côté de la garde) -->
<div class="garde-pompier-item">
    <h2 class="section-title"><i class="fa-solid fa-fire-extinguisher"></i> Sapeurs-Pompiers</h2>
    <?php if (!empty($pompiers)): ?>
        <?php foreach ($pompiers as $p): ?>
        <div class="card type-pompier">
            <h3><?= htmlspecialchars($p['nom_service']) ?></h3>
            <p class="quartier"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['nom_quartier'] ?? '—') ?></p>
            <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $p['numero_telephone'])) ?>" class="btn-appel-service btn-pompier">
                <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($p['numero_telephone']) ?>
            </a>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="aucun-resultat">Non répertorié.</p>
    <?php endif; ?>
</div>
            </div>
        </section>

        <!-- SECTION : Recherche et Filtres -->
        <section class="section-services">
            <h2 class="section-title"><i class="fa-solid fa-filter"></i> Filtrer les services</h2>
            <form method="GET" action="index.php" class="filtres-form">
                <input type="hidden" name="action" value="service-urgence">
                <input type="text" name="q" placeholder="Rechercher..." value="<?= htmlspecialchars($recherche) ?>">
                

                <select name="id_quartier" onchange="this.form.submit()">
                    <option value="">Tous les quartiers</option>
                    <?php foreach ($quartiers as $q): ?>
                        <option value="<?= $q['id_quartier'] ?>" <?= (string)$idQuartier === (string)$q['id_quartier'] ? 'selected' : '' ?>><?= htmlspecialchars($q['nom_quartier']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-search">Rechercher</button>
            </form>

            <!-- LAYOUT EN COLONNES (1 / 2 / 3 selon l'écran) -->
            <div class="services-layout">

                <!-- GAUCHE : FORCE DE L'ORDRE -->
                <div class="service-column">
                    <h3 class="column-title"><i class="fa-solid fa-user-shield" style="color: var(--police-color);"></i> Police / Gendarmerie</h3>
                    <?php
                        $police = array_filter($services, function($s) {
                            return ($s['nom_categorie'] ?? $s['nom_type']) === "Force de l'ordre";
                        });
                        $nbPolice = count($police);
                    ?>
                    <?php if ($nbPolice > 4): ?>
                        <input type="checkbox" id="toggle-police" class="toggle-more">
                    <?php endif; ?>
                    <div class="column-cards <?= $nbPolice > 4 ? 'has-more' : '' ?>">
                        <?php foreach($police as $s): ?>
                        <div class="card type-police">
                            <h3><?= htmlspecialchars($s['nom_service']) ?></h3>
                            <p class="quartier"><?= htmlspecialchars($s['nom_quartier'] ?? '—') ?></p>
                            <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['numero_telephone'])) ?>" class="btn-appel-service btn-police">Appeler</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($nbPolice > 4): ?>
                        <label for="toggle-police" class="btn-voir-plus">Voir plus (<?= $nbPolice - 4 ?>)</label>
                        <label for="toggle-police" class="btn-voir-moins">Voir moins</label>
                    <?php endif; ?>
                </div>

                <!-- MILIEU : PHARMACIE -->
                <div class="service-column">
                    <h3 class="column-title"><i class="fa-solid fa-pills" style="color: var(--pharmacie-color);"></i> Pharmacies</h3>
                    <?php
                        $pharmacies = array_filter($services, function($s) {
                            return ($s['nom_categorie'] ?? $s['nom_type']) === 'Pharmacie';
                        });
                        $nbPharmacies = count($pharmacies);
                    ?>
                    <?php if ($nbPharmacies > 4): ?>
                        <input type="checkbox" id="toggle-pharmacie" class="toggle-more">
                    <?php endif; ?>
                    <div class="column-cards <?= $nbPharmacies > 4 ? 'has-more' : '' ?>">
                        <?php foreach($pharmacies as $s): ?>
                        <div class="card type-pharmacie">
                            <h3><?= htmlspecialchars($s['nom_service']) ?></h3>
                            <p class="quartier"><?= htmlspecialchars($s['nom_quartier'] ?? '—') ?></p>
                            <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['numero_telephone'])) ?>" class="btn-appel-service btn-pharmacie">Appeler</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($nbPharmacies > 4): ?>
                        <label for="toggle-pharmacie" class="btn-voir-plus">Voir plus (<?= $nbPharmacies - 4 ?>)</label>
                        <label for="toggle-pharmacie" class="btn-voir-moins">Voir moins</label>
                    <?php endif; ?>
                </div>

                <!-- DROITE : HÔPITAL -->
                <div class="service-column">
                    <h3 class="column-title"><i class="fa-solid fa-hospital" style="color: var(--ambulance-color);"></i> Hôpitaux / CSB</h3>
                    <?php
                        $hopitaux = array_filter($services, function($s) {
                            return ($s['nom_categorie'] ?? $s['nom_type']) === 'Hôpital';
                        });
                        $nbHopitaux = count($hopitaux);
                    ?>
                    <?php if ($nbHopitaux > 4): ?>
                        <input type="checkbox" id="toggle-hopital" class="toggle-more">
                    <?php endif; ?>
                    <div class="column-cards <?= $nbHopitaux > 4 ? 'has-more' : '' ?>">
                        <?php foreach($hopitaux as $s): ?>
                        <div class="card type-hopital">
                            <h3><?= htmlspecialchars($s['nom_service']) ?></h3>
                            <p class="quartier"><?= htmlspecialchars($s['nom_quartier'] ?? '—') ?></p>
                            <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['numero_telephone'])) ?>" class="btn-appel-service btn-hopital">Appeler</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($nbHopitaux > 4): ?>
                        <label for="toggle-hopital" class="btn-voir-plus">Voir plus (<?= $nbHopitaux - 4 ?>)</label>
                        <label for="toggle-hopital" class="btn-voir-moins">Voir moins</label>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>