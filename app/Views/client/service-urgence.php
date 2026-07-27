<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Services - Urgences Antsiranana</title>

    <link rel="stylesheet" href="asset/css/client/service-urgence.css">
    <link rel="stylesheet" href="asset/icon/fontAwesome/all.min.css">
</head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>

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
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($pharmacieGarde['nom_quartier'] ?? '—') ?>
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
                <a href="index.php?action=urgence-carte" class="btn-primary">
                    <i class="fa-solid fa-map"></i> Voir sur une carte
                </a>
            </div>
        </section>


        <!-- SECTION : Recherche et Filtres -->
        <section class="section-services">
            <h2 class="section-title">
                <i class="fa-solid fa-building-shield"></i> Services d'urgence
            </h2>

            <form method="GET" action="index.php" class="filtres-form">
                <input type="hidden" name="action" value="service-urgence">

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
                            <option value="<?= $t['id_type'] ?>" <?= (string)$idType === (string)$t['id_type'] ? 'selected' : '' ?>>
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
                            <option value="<?= $q['id_quartier'] ?>" <?= (string)$idQuartier === (string)$q['id_quartier'] ? 'selected' : '' ?>>
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

                    <?php if (!empty($idQuartier) || !empty($idType) || !empty($recherche)): ?>
                        <a href="index.php?action=service-urgence" class="btn-reset">
                            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                        </a>
                    <?php endif; ?>
                </div>

            </form>

            <!-- Résultats -->
            <?php if (count($services) > 0): ?>
                <div class="grid-cards">
                    <?php foreach ($services as $s):
                        $meta = ['class' => 'type-default', 'icon' => 'fa-solid fa-hospital']; // Service::typeMeta n'existe pas
                    ?>
                        <div class="card <?= $meta['class'] ?>">
                            <div class="card-header">
                                <span class="card-type-badge">
                                    <i class="<?= $meta['icon'] ?>"></i> <?= htmlspecialchars($s['nom_type']) ?>
                                </span>
                            </div>
                            <h3><?= htmlspecialchars($s['nom_service']) ?></h3>
                            <p class="quartier">
                                <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($s['nom_quartier'] ?? '—') ?>
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

<?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
