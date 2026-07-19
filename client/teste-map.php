<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';

/*
 * Structure de table attendue (adaptez les noms de colonnes si besoin) :
 *
 * CREATE TABLE pharmacies (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     nom VARCHAR(150) NOT NULL,
 *     adresse VARCHAR(255) NOT NULL,
 *     latitude DECIMAL(10, 7) NOT NULL,
 *     longitude DECIMAL(10, 7) NOT NULL,
 *     telephone VARCHAR(30) DEFAULT NULL,
 *     horaires VARCHAR(150) DEFAULT NULL,
 *     statut ENUM('open','limited','unknown') DEFAULT 'unknown',
 *     note DECIMAL(2,1) DEFAULT NULL,
 *     nombre_avis INT DEFAULT NULL
 * );
 */

$pharmacies = [];

try {
    $stmt = $pdo->query("SELECT id, nom, adresse, latitude, longitude, telephone, horaires, statut, note, nombre_avis FROM pharmacies ORDER BY nom ASC");
    $pharmacies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En production, loggez l'erreur au lieu de l'afficher
    error_log("Erreur chargement pharmacies : " . $e->getMessage());
}

// Prépare les données pour la carte (JS)
$pharmaciesJson = json_encode(array_map(function ($p) {
    return [
        'id'     => (string) $p['id'],
        'name'   => $p['nom'],
        'address'=> $p['adresse'],
        'lat'    => (float) $p['latitude'],
        'lng'    => (float) $p['longitude'],
        'phone'  => $p['telephone'],
        'hours'  => $p['horaires'],
        'status' => $p['statut'],
        'rating' => $p['note'] !== null ? (float) $p['note'] : null,
        'ratingCount' => $p['nombre_avis'] !== null ? (int) $p['nombre_avis'] : null,
    ];
}, $pharmacies), JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Pharmacies - Urgences Antsiranana</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --ink:#1c231e;
            --paper:#f6f4ee;
            --forest:#1b4332;
            --forest-dk:#0f2921;
            --sage:#e7efe6;
            --sage-line:#c9d8c6;
            --ylang:#e8b944;
            --ylang-dk:#a97e1f;
            --closed:#b23b3b;
            --radius:14px;
        }
        #pharma-app *{box-sizing:border-box;}
        #pharma-app{
            font-family:'Inter',sans-serif;
            color:var(--ink);
            background:var(--paper);
            display:grid;
            grid-template-columns:380px 1fr;
            height:calc(100vh - 0px);
            min-height:600px;
        }
        #pharma-sidebar{
            background:var(--paper);
            border-right:1px solid var(--sage-line);
            display:flex;
            flex-direction:column;
            overflow:hidden;
        }
        #pharma-sidebar header{
            padding:22px 22px 16px 22px;
            border-bottom:1px solid var(--sage-line);
        }
        .eyebrow{
            font-family:'Space Mono',monospace;
            font-size:11px;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--forest);
            opacity:.7;
        }
        #pharma-sidebar h1{
            font-family:'Fraunces',serif;
            font-weight:600;
            font-size:26px;
            margin:4px 0 2px 0;
            color:var(--forest-dk);
            letter-spacing:-0.01em;
        }
        #pharma-sidebar .sub{
            font-size:13px;
            color:#5b6b5e;
            margin:0;
        }
        #pharma-list{
            overflow-y:auto;
            flex:1;
            padding:10px 12px 24px 12px;
        }
        .card{
            background:#fff;
            border:1px solid var(--sage-line);
            border-radius:var(--radius);
            padding:14px 16px;
            margin-bottom:10px;
            cursor:pointer;
            transition:border-color .15s ease, transform .12s ease, box-shadow .15s ease;
        }
        .card:hover{
            border-color:var(--forest);
            transform:translateY(-1px);
            box-shadow:0 4px 14px rgba(27,67,50,0.08);
        }
        .card.active{
            border-color:var(--forest);
            background:var(--sage);
            box-shadow:0 4px 14px rgba(27,67,50,0.12);
        }
        .card-top{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:8px;
        }
        .card-name{
            font-family:'Fraunces',serif;
            font-weight:600;
            font-size:16px;
            color:var(--forest-dk);
            line-height:1.25;
        }
        .badge{
            font-family:'Space Mono',monospace;
            font-size:10px;
            letter-spacing:.05em;
            text-transform:uppercase;
            padding:3px 7px;
            border-radius:20px;
            white-space:nowrap;
            flex-shrink:0;
        }
        .badge.open{ background:var(--ylang); color:var(--forest-dk); }
        .badge.open .dot{
            display:inline-block; width:6px;height:6px;border-radius:50%;
            background:var(--forest-dk); margin-right:5px; animation:pulse 1.8s infinite;
        }
        @keyframes pulse{0%{opacity:1;}50%{opacity:.35;}100%{opacity:1;}}
        .badge.limited{ background:#eee2c9; color:var(--ylang-dk); }
        .card-address{ font-size:12.5px; color:#5b6b5e; margin-top:4px; }
        .card-meta{
            display:flex; align-items:center; gap:10px; margin-top:9px;
            font-family:'Space Mono',monospace; font-size:11.5px; color:var(--forest);
        }
        .rating{ color:var(--ylang-dk); }
        .no-rating{ color:#9aa79b; }
        .card-phone{
            margin-top:6px; font-family:'Space Mono',monospace; font-size:12px; color:var(--forest-dk);
        }
        .empty-state{
            padding:24px 16px;
            text-align:center;
            color:#5b6b5e;
            font-size:13px;
        }
        #pharma-map{ height:100%; width:100%; }
        .leaflet-popup-content-wrapper{ border-radius:10px; font-family:'Inter',sans-serif; }
        .popup-title{
            font-family:'Fraunces',serif; font-weight:600; font-size:15px; color:var(--forest-dk); margin-bottom:2px;
        }
        .popup-address{ font-size:12px; color:#5b6b5e; }

        @media (max-width:760px){
            #pharma-app{ grid-template-columns:1fr; grid-template-rows:auto 1fr; height:auto; }
            #pharma-sidebar{ border-right:none; border-bottom:1px solid var(--sage-line); max-height:46vh; }
            #pharma-map{ min-height:54vh; }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php' ?>

<div id="pharma-app">
    <div id="pharma-sidebar">
        <header>
            <div class="eyebrow">Antsiranana · Diego Suarez</div>
            <h1>Trouver une pharmacie</h1>
            <p class="sub"><?= count($pharmacies) ?> pharmacie<?= count($pharmacies) > 1 ? 's' : '' ?> référencée<?= count($pharmacies) > 1 ? 's' : '' ?> — sélectionnez-en une pour la localiser</p>
        </header>
        <div id="pharma-list">
            <?php if (empty($pharmacies)): ?>
                <div class="empty-state">Aucune pharmacie enregistrée pour le moment.</div>
            <?php else: ?>
                <?php foreach ($pharmacies as $p): ?>
                    <div class="card" id="card-<?= (int) $p['id'] ?>" data-id="<?= (int) $p['id'] ?>">
                        <div class="card-top">
                            <div class="card-name"><?= htmlspecialchars($p['nom']) ?></div>
                            <?php if ($p['statut'] === 'open'): ?>
                                <span class="badge open"><span class="dot"></span>24/7</span>
                            <?php elseif ($p['statut'] === 'limited'): ?>
                                <span class="badge limited">Horaires limités</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-address"><?= htmlspecialchars($p['adresse']) ?></div>
                        <div class="card-meta">
                            <?php if ($p['note'] !== null): ?>
                                <span class="rating">★ <?= number_format((float) $p['note'], 1) ?></span>
                                <span>· <?= (int) $p['nombre_avis'] ?> avis</span>
                            <?php else: ?>
                                <span class="no-rating">Pas d'avis</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($p['telephone'])): ?>
                            <div class="card-phone"><?= htmlspecialchars($p['telephone']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="pharma-map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const pharmacies = <?= $pharmaciesJson ?: '[]' ?>;

const map = L.map('pharma-map', { zoomControl: true }).setView([-12.284, 49.293], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

function pinIcon(status){
    const color = status === 'open' ? '#e8b944' : (status === 'limited' ? '#b23b3b' : '#1b4332');
    const svg = `<svg width="30" height="38" viewBox="0 0 30 38" xmlns="http://www.w3.org/2000/svg">
        <path d="M15 0C6.7 0 0 6.7 0 15c0 10.5 15 23 15 23s15-12.5 15-23C30 6.7 23.3 0 15 0z" fill="${color}"/>
        <circle cx="15" cy="15" r="6" fill="#f6f4ee"/>
    </svg>`;
    return L.divIcon({ html: svg, className: '', iconSize: [30,38], iconAnchor: [15,38], popupAnchor: [0,-34] });
}

const markers = {};
if (pharmacies.length) {
    const bounds = [];
    pharmacies.forEach(p => {
        const marker = L.marker([p.lat, p.lng], { icon: pinIcon(p.status) }).addTo(map);
        marker.bindPopup(`
            <div class="popup-title">${p.name}</div>
            <div class="popup-address">${p.address}</div>
        `);
        marker.on('click', () => selectPharmacy(p.id, false));
        markers[p.id] = marker;
        bounds.push([p.lat, p.lng]);
    });
    if (bounds.length > 1) map.fitBounds(bounds, { padding: [40, 40] });
}

document.querySelectorAll('#pharma-list .card').forEach(card => {
    card.addEventListener('click', () => selectPharmacy(card.dataset.id, true));
});

let activeId = null;
function selectPharmacy(id, flyMap){
    if (activeId) {
        document.getElementById('card-' + activeId)?.classList.remove('active');
    }
    activeId = id;
    const cardEl = document.getElementById('card-' + id);
    cardEl?.classList.add('active');
    cardEl?.scrollIntoView({ behavior:'smooth', block:'nearest' });

    const p = pharmacies.find(x => String(x.id) === String(id));
    if (!p) return;
    if (flyMap) {
        map.flyTo([p.lat, p.lng], 16, { duration: 0.8 });
    }
    markers[id].openPopup();
}
</script>
</body>
</html>