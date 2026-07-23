<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';

// ===================================================
// 1) PHARMACIE(S) DE GARDE DU JOUR
// ===================================================
$pharmaciesGarde = [];
try {
    $sqlGarde = "SELECT s.libelle, s.telephone, s.adresse
                 FROM garde g
                 INNER JOIN service s ON g.id_service = s.id_service
                 WHERE s.id_type = 1
                   AND s.actif = 1
                   AND CURDATE() BETWEEN g.date_debut AND g.date_fin
                 ORDER BY s.libelle";
    $stmtGarde = $pdo->query($sqlGarde);
    $pharmaciesGarde = $stmtGarde->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pharmaciesGarde = [];
}

// ===================================================
// 0) NUMÉROS D'URGENCE (Pompier, Police, Ambulance) - DYNAMIQUE
// ===================================================
$urgenceServices = ['pompier' => [], 'police' => [], 'ambulance' => []];
try {
    $sqlUrgence = "SELECT s.libelle, s.telephone, t.nom_type
                   FROM service s
                   INNER JOIN type_service t ON s.id_type = t.id_type
                   WHERE s.actif = 1
                     AND s.id_type IN (2, 3, 4)
                   ORDER BY t.nom_type, s.libelle";
    $stmtUrgence = $pdo->query($sqlUrgence);
    $rawUrgence = $stmtUrgence->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawUrgence as $s) {
        switch ($s['nom_type']) {
            case 'Pompier':
                $urgenceServices['pompier'][] = $s;
                break;
            case "Force de l'ordre":
                $urgenceServices['police'][] = $s;
                break;
            case 'Hôpital':
                $urgenceServices['ambulance'][] = $s;
                break;
        }
    }
} catch (PDOException $e) {
    $urgenceServices = ['pompier' => [], 'police' => [], 'ambulance' => []];
}

// ===================================================
// 2) DERNIÈRES ACTUALITÉS (3 articles)
// ===================================================
$dernieresActus = [];
try {
    // Sélection de lien_source pour la redirection externe direct
    $sqlActus = "SELECT id_article, titre, contenu, lien_source, date_publication
                 FROM article
                 WHERE statut = 'publie'
                 ORDER BY date_publication DESC
                 LIMIT 3";
    $stmtActus = $pdo->query($sqlActus);
    $rawActus = $stmtActus->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawActus as $a) {
        // Extraire la 1ère image du contenu HTML
        $image = null;
        if (preg_match('/<img[^>]+src="([^"]+)"/i', $a['contenu'], $m)) {
            $image = $m[1];
        }

        // Extraire un extrait texte (sans balises HTML)
        $texte = trim(strip_tags($a['contenu']));
        $extrait = mb_substr($texte, 0, 110);
        if (mb_strlen($texte) > 110) {
            $extrait .= '...';
        }

        $dernieresActus[] = [
            'id'          => $a['id_article'],
            'titre'       => $a['titre'],
            'image'       => $image,
            'extrait'     => $extrait,
            'lien_source' => $a['lien_source'],
            'date'        => $a['date_publication']
        ];
    }
} catch (PDOException $e) {
    $dernieresActus = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Accueil - Urgences Antsiranana</title>
    <link rel="stylesheet" href="../asset/css/client/home.css">
    <link rel="stylesheet" href="../asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php' ?>
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
            <img src="../asset/image/image.jpg" alt="Urgence">
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

        <a href="./service-urgence.php" class="btn-primary">
            Voir en détails
        </a>
    </section>

    <!-- ================= DERNIÈRES ACTUALITÉS (DYNAMIQUE) ================= -->
    <section class="news-section">
        <h2>Dernières actualités</h2>

        <?php if (count($dernieresActus) > 0): ?>
            <div class="news-grid">
                <?php foreach ($dernieresActus as $actu): 
                    // Redirection externe si lien_source existe, sinon lien interne vers l'article
                    if (!empty($actu['lien_source'])) {
                        $url = htmlspecialchars($actu['lien_source']);
                        $target = 'target="_blank" rel="noopener noreferrer"';
                    } else {
                        $url = "article.php?id=" . (int)$actu['id'];
                        $target = '';
                    }
                ?>
                    <a href="<?= $url ?>" <?= $target ?> class="news-card">
                        <?php if ($actu['image']): ?>
                            <div class="news-img">
                                <img src="<?= htmlspecialchars($actu['image']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="news-content">
                            <span class="news-date">
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
            <a href="./actualites.php" class="btn-primary">
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
        <a href="don.php" class="btn-danger">
            Faire un don
        </a>
    </section>

</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>