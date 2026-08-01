<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Accueil - Urgences Antsiranana</title>
    <link rel="stylesheet" href="public/asset/css/client/home.css">
    <link rel="stylesheet" href="public/asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <style>
        /* Petit complément propre à cette page : le badge source des actus,
           identique à celui de actualites.php, réutilise --sage/--navy-dk
           déjà définis dans home.css. Idéalement à déplacer dans home.css
           directement puisque les 2 pages l'utilisent maintenant. */
        .news-source-badge {
            background: var(--sage);
            color: var(--navy-dk);
            padding: 3px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.72rem;
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>
<main>

    <!-- ================= HERO ================= -->
    <section class="hero">
        <div class="hero-content">
            <h1>Besoin d'aide rapidement ?</h1>
            <p>
                Accédez rapidement aux services d'urgence,
                trouvez une pharmacie de garde ou un hôpital
                près de chez vous.
            </p>
            <a href="#urgence" class="btn-danger">
                J'ai besoin d'aide
            </a>
        </div>

        <div class="hero-image">
            <img src="public/asset/image/image.jpg" alt="Urgence">
        </div>
    </section>

    <!-- ================= SERVICES ================= -->
    <section class="services" id="urgence">
        <h2>Services d'urgence</h2>

        <div class="cards">
            <!-- Ambulance -->
            <div class="card ambulance">
                <div class="card-top"></div>
                <div class="card-icon">
                    <i class="fa-solid fa-truck-medical"></i>
                </div>
                <h3>Ambulance</h3>
                <p>Intervention médicale rapide en cas d'urgence.</p>

                <div class="urgence-numbers">
                    <?php if (!empty($urgenceServices['ambulance'])):
                        $s = $urgenceServices['ambulance'][0];
                    ?>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['telephone'])) ?>" class="btn-call">
                            <i class="fa-solid fa-phone"></i>
                            <?= htmlspecialchars($s['libelle']) ?> — <?= htmlspecialchars($s['telephone']) ?>
                        </a>
                    <?php else: ?>
                        <p class="urgence-empty">Aucun numéro enregistré.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pompiers -->
            <div class="card pompier">
                <div class="card-top"></div>
                <div class="card-icon">
                    <i class="fa-solid fa-fire-extinguisher"></i>
                </div>
                <h3>Pompiers</h3>
                <p>Incendies, accidents et interventions de secours.</p>

                <div class="urgence-numbers">
                    <?php if (!empty($urgenceServices['pompier'])):
                        $s = $urgenceServices['pompier'][0];
                    ?>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['telephone'])) ?>" class="btn-call">
                            <i class="fa-solid fa-phone"></i>
                            <?= htmlspecialchars($s['libelle']) ?> — <?= htmlspecialchars($s['telephone']) ?>
                        </a>
                    <?php else: ?>
                        <p class="urgence-empty">Aucun numéro enregistré.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Police -->
            <div class="card police">
                <div class="card-top"></div>
                <div class="card-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3>Police</h3>
                <p>Assistance et intervention en cas d'urgence liée à la sécurité.</p>

                <div class="urgence-numbers">
                    <?php if (!empty($urgenceServices['police'])):
                        $s = $urgenceServices['police'][0];
                    ?>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $s['telephone'])) ?>" class="btn-call">
                            <i class="fa-solid fa-phone"></i>
                            <?= htmlspecialchars($s['libelle']) ?> — <?= htmlspecialchars($s['telephone']) ?>
                        </a>
                    <?php else: ?>
                        <p class="urgence-empty">Aucun numéro enregistré.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= PHARMACIE DE GARDE DU JOUR ================= -->
    <section class="section-info garde-section">
        <div class="info-text">
            <h2>Pharmacie(s) de garde aujourd'hui</h2>
            <p>
                Voici la pharmacie assurant la garde
                pour la journée du <?= date('d/m/Y') ?>.
            </p>
        </div>

        <div class="garde-list">
            <?php if (count($pharmaciesGarde) > 0): ?>
                <?php foreach ($pharmaciesGarde as $p): ?>
                    <div class="garde-card">
                        <div class="garde-icon">
                            <i class="fa-solid fa-mortar-pestle"></i>
                        </div>
                        <div class="garde-details">
                            <h4><?= htmlspecialchars($p['libelle']) ?></h4>
                            <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['adresse']) ?></p>
                        </div>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $p['telephone'])) ?>" class="btn-call">
                            <i class="fa-solid fa-phone"></i>
                            <?= htmlspecialchars($p['telephone']) ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="garde-empty">
                    Aucune pharmacie de garde enregistrée pour aujourd'hui.
                    Consultez la liste complète pour plus d'informations.
                </p>
            <?php endif; ?>
        </div>

        <a href="index.php?action=service-urgence" class="btn-primary">
            Voir en détails
        </a>
    </section>

    <!-- ================= DERNIÈRES ACTUALITÉS ================= -->
    <section class="news-section">
        <h2>Dernières actualités</h2>

        <?php if (count($dernieresActus) > 0): ?>
            <div class="news-grid">
                <?php foreach ($dernieresActus as $actu):
                    $url = "index.php?action=article-detail&id=" . (int)$actu['id'];
                ?>
                    <a href="<?= $url ?>" class="news-card">
                        <?php if ($actu['image']): ?>
                            <div class="news-img">
                                <img src="<?= htmlspecialchars($actu['image']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="news-content">
                            <span class="news-date">
                                <span class="news-source-badge"><?= htmlspecialchars($actu['nom_source'] ?? 'Rédaction') ?></span>
                                <i class="fa-regular fa-clock"></i>
                                <?= date('d/m/Y', strtotime($actu['date'])) ?>
                            </span>
                            <h3 class="news-title"><?= htmlspecialchars($actu['titre']) ?></h3>
                            <p class="news-excerpt"><?= htmlspecialchars($actu['extrait']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="news-empty">Aucune actualité disponible pour le moment.</p>
        <?php endif; ?>

        <div class="news-more">
            <a href="index.php?action=actualites" class="btn-primary">
                Voir toutes les actualités
            </a>
        </div>
    </section>

    <!-- ================= DON ================= -->
    <section class="don">
        <h2>Soutenir les services d'urgence</h2>
        <p>
            Votre contribution aide à améliorer
            l'accès aux secours.
        </p>
        <a href="index.php?action=don" class="btn-danger">
            Faire un don
        </a>
    </section>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>