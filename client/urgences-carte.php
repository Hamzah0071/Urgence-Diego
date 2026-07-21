<?php
require_once '../includes/session.php';
require_once '../includes/db_connect.php';

/*
 * Basé sur votre schéma réel :
 *   service (id_service, libelle, telephone, adresse, latitude, longitude,
 *            id_quartier, id_type, actif, description)
 *   type_service (id_type, nom_type)  -> 1 Pharmacie, 2 Pompier, 3 Force de l'ordre, 4 Hôpital
 *   quartier (id_quartier, nom_quartier, latitude, longitude)
 *
 * On utilise les coordonnées du service si elles existent, sinon celles
 * de son quartier (COALESCE), en attendant que chaque service ait ses
 * propres coordonnées précises.
 */

$typeToCategorie = [
    'Pharmacie'          => 'pharmacie',
    'Pompier'            => 'pompier',
    "Force de l'ordre"   => 'police',
    'Hôpital'            => 'hopital',
];

$services = [];

try {
    $sql = "SELECT
                s.id_service,
                s.libelle,
                s.adresse,
                s.telephone,
                s.description,
                COALESCE(s.latitude, q.latitude)   AS latitude,
                COALESCE(s.longitude, q.longitude) AS longitude,
                ts.nom_type,
                q.nom_quartier
            FROM service s
            JOIN type_service ts ON s.id_type = ts.id_type
            LEFT JOIN quartier q ON s.id_quartier = q.id_quartier
            WHERE s.actif = 1
            ORDER BY ts.nom_type, s.libelle";
    $stmt = $pdo->query($sql);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur chargement service : " . $e->getMessage());
}

$servicesForJs = [];
foreach ($services as $s) {
    $categorie = $typeToCategorie[$s['nom_type']] ?? 'pharmacie';
    $servicesForJs[] = [
        'id'          => (string) $s['id_service'],
        'categorie'   => $categorie,
        'name'        => $s['libelle'],
        'address'     => $s['adresse'],
        'quartier'    => $s['nom_quartier'],
        'lat'         => $s['latitude'] !== null ? (float) $s['latitude'] : null,
        'lng'         => $s['longitude'] !== null ? (float) $s['longitude'] : null,
        'phone'       => (!empty($s['telephone']) && $s['telephone'] !== '000 00 000 00') ? $s['telephone'] : null,
        'description' => $s['description'],
    ];
}
$servicesJson = json_encode($servicesForJs, JSON_UNESCAPED_UNICODE);

$categories = [
    'tous'      => 'Tous',
    'pharmacie' => 'Pharmacies',
    'hopital'   => 'Hôpitaux',
    'police'    => 'Police',
    'pompier'   => 'Pompiers',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Carte des urgences - Urgences Antsiranana</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --ink:#1c231e;
            --paper:#f6f4ee;
            --navy:#1e40af;
            --navy-dk:#132b78;
            --sage:#eef1f6;
            --line:#cfd6e2;
            --pharmacie:#e8b944;
            --hopital:#c0392b;
            --police:#1e40af;
            --pompier:#d9631e;
            --radius:14px;
        }
        #urg-app *{box-sizing:border-box;}
        #urg-app{
            font-family:'Inter',sans-serif;
            color:var(--ink);
            background:var(--paper);
            display:grid;
            grid-template-columns:400px 1fr;
            height:calc(100vh - var(--header-height, 77px));
            min-height:640px;
            position:relative;
            overflow:hidden;
        }
        #urg-sidebar{
            background:var(--paper);
            border-right:1px solid var(--line);
            display:flex;
            flex-direction:column;
            overflow:hidden;
        }
        #urg-sidebar header{
            padding:22px 22px 14px 22px;
            border-bottom:1px solid var(--line);
        }
        .eyebrow{
            font-family:'Space Mono',monospace;
            font-size:11px;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--navy);
            opacity:.75;
        }
        #urg-sidebar h1{
            font-family:'Fraunces',serif;
            font-weight:600;
            font-size:25px;
            margin:4px 0 12px 0;
            color:var(--navy-dk);
            letter-spacing:-0.01em;
        }
        .search-wrap{ position:relative; }
        .search-wrap svg{
            position:absolute; left:13px; top:50%; transform:translateY(-50%);
            width:16px; height:16px; opacity:.5;
        }
        #urg-search{
            width:100%;
            padding:11px 14px 11px 38px;
            border:1.5px solid var(--line);
            border-radius:10px;
            font-family:'Inter',sans-serif;
            font-size:14px;
            background:#fff;
            color:var(--ink);
            outline:none;
            transition:border-color .15s ease;
        }
        #urg-search:focus{ border-color:var(--navy); }
        #urg-search::placeholder{ color:#93a0ae; }
        .pills{ display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }
        .pill{
            font-family:'Space Mono',monospace;
            font-size:11px;
            letter-spacing:.03em;
            padding:6px 11px;
            border-radius:20px;
            border:1.5px solid var(--line);
            background:#fff;
            color:#5b6674;
            cursor:pointer;
            display:flex;
            align-items:center;
            gap:6px;
            transition:all .15s ease;
            user-select:none;
        }
        .pill .swatch{ width:8px; height:8px; border-radius:50%; }
        .pill:hover{ border-color:var(--navy); }
        .pill.active{ background:var(--navy); border-color:var(--navy); color:#fff; }
        .pill.active .swatch{ background:#fff !important; }
        #urg-count{ font-size:12px; color:#5b6674; margin-top:10px; }
        #urg-list{ overflow-y:auto; flex:1; padding:12px 12px 24px 12px; }
        .card{
            background:#fff;
            border:1px solid var(--line);
            border-left:4px solid var(--navy);
            border-radius:var(--radius);
            padding:13px 15px;
            margin-bottom:10px;
            cursor:pointer;
            transition:transform .12s ease, box-shadow .15s ease;
        }
        .card:hover{ transform:translateY(-1px); box-shadow:0 4px 14px rgba(30,64,175,0.08); }
        .card.active{ background:var(--sage); box-shadow:0 4px 14px rgba(30,64,175,0.12); }
        .card.no-position{ opacity:.65; cursor:default; }
        .card-top{ display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
        .card-cat{
            font-family:'Space Mono',monospace;
            font-size:10px;
            letter-spacing:.06em;
            text-transform:uppercase;
            margin-bottom:3px;
        }
        .card-name{
            font-family:'Fraunces',serif;
            font-weight:600;
            font-size:15.5px;
            color:var(--navy-dk);
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
            background:#eee2c9;
            color:#8a6416;
        }
        .card-address{ font-size:12.5px; color:#5b6674; margin-top:4px; }
        .card-quartier{ font-size:11.5px; color:#93a0ae; margin-top:2px; }
        .card-phone{ margin-top:8px; font-family:'Space Mono',monospace; font-size:12px; color:var(--navy-dk); }
        .empty-state{ padding:32px 16px; text-align:center; color:#5b6674; font-size:13px; }
        #urg-map{ height:100%; width:100%; }
        .leaflet-popup-content-wrapper{ border-radius:10px; font-family:'Inter',sans-serif; }
        .popup-cat{ font-family:'Space Mono',monospace; font-size:10px; text-transform:uppercase; letter-spacing:.05em; }
        .popup-title{ font-family:'Fraunces',serif; font-weight:600; font-size:15px; color:var(--navy-dk); margin:2px 0; }
        .popup-address{ font-size:12px; color:#5b6674; }

        /* Éléments du bottom-sheet mobile : masqués par défaut sur desktop */
        .sheet-handle{ display:none; }
        .sheet-backdrop{ display:none; }

        /* ==========================================================
           MOBILE — la sidebar devient une "bottom sheet" rétractable
           au-dessus de la carte plein écran, au lieu d'un split 50/50.
           ========================================================== */
        @media (max-width:760px){
            #urg-app{
                display:block;
                height:calc(100vh - var(--header-height, 77px));
                min-height:0;
            }

            #urg-map{
                position:absolute;
                inset:0;
                height:100%;
                width:100%;
            }

            #urg-sidebar{
                position:fixed;
                left:0;
                right:0;
                bottom:0;
                z-index:500;
                height:82vh;
                max-height:82vh;
                border-right:none;
                border-radius:18px 18px 0 0;
                box-shadow:0 -10px 30px rgba(28,35,30,0.18);
                transform:translateY(calc(100% - 62px));
                transition:transform .32s cubic-bezier(.32,.72,0,1);
            }

            #urg-sidebar.expanded{
                transform:translateY(0);
            }

            .sheet-handle{
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                gap:6px;
                height:62px;
                flex-shrink:0;
                cursor:pointer;
                background:var(--paper);
                border-radius:18px 18px 0 0;
            }

            .sheet-handle-bar{
                width:40px;
                height:4px;
                border-radius:4px;
                background:var(--line);
            }

            .sheet-handle-text{
                font-family:'Space Mono',monospace;
                font-size:11.5px;
                letter-spacing:.03em;
                color:#5b6674;
                text-align:center;
                padding:0 16px;
            }

            .sheet-backdrop{
                display:block;
                position:fixed;
                inset:0;
                background:rgba(28,35,30,0.35);
                opacity:0;
                pointer-events:none;
                transition:opacity .3s ease;
                z-index:480;
            }

            .sheet-backdrop.show{
                opacity:1;
                pointer-events:auto;
            }

            #urg-sidebar header{
                padding:2px 18px 12px 18px;
            }

            #urg-sidebar .eyebrow,
            #urg-sidebar h1,
            #urg-count{
                display:none;
            }

            .pills{
                flex-wrap:nowrap;
                overflow-x:auto;
                -webkit-overflow-scrolling:touch;
                scrollbar-width:none;
                margin-top:10px;
                padding-bottom:2px;
            }
            .pills::-webkit-scrollbar{ display:none; }
            .pill{ flex:0 0 auto; }

            #urg-list{
                padding:8px 12px calc(20px + var(--safe-area-inset-bottom, 0px)) 12px;
            }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php' ?>

<div id="urg-app">
    <div id="urg-sidebar">
        <div class="sheet-handle" id="sheetHandle">
            <span class="sheet-handle-bar"></span>
            <span class="sheet-handle-text" id="sheetHandleText">Toucher pour voir la liste</span>
        </div>
        <header>
            <div class="eyebrow">Antsiranana · Diego Suarez</div>
            <h1>Carte des urgences</h1>
            <div class="search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="#5b6674" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="urg-search" placeholder="Rechercher une pharmacie, un hôpital, la police, les pompiers…" autocomplete="off">
            </div>
            <div class="pills" id="urg-pills">
                <?php foreach ($categories as $key => $label): ?>
                    <div class="pill<?= $key === 'tous' ? ' active' : '' ?>" data-cat="<?= htmlspecialchars($key) ?>">
                        <?php if ($key !== 'tous'): ?><span class="swatch" style="background:var(--<?= htmlspecialchars($key) ?>)"></span><?php endif; ?>
                        <?= htmlspecialchars($label) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="urg-count"></div>
        </header>
        <div id="urg-list"></div>
    </div>
    <div class="sheet-backdrop" id="sheetBackdrop"></div>
    <div id="urg-map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const allServices = <?= $servicesJson ?: '[]' ?>;

const CATEGORY_LABELS = { pharmacie: 'Pharmacie', hopital: 'Hôpital', police: 'Police', pompier: 'Pompiers' };
const CATEGORY_COLORS = { pharmacie: '#e8b944', hopital: '#c0392b', police: '#1e40af', pompier: '#d9631e' };
const CATEGORY_GLYPH  = { pharmacie: '+', hopital: '✚', police: '★', pompier: '▲' };

const map = L.map('urg-map', { zoomControl: true }).setView([-12.284, 49.293], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

function pinIcon(cat){
    const color = CATEGORY_COLORS[cat] || '#1e40af';
    const glyph = CATEGORY_GLYPH[cat] || '•';
    const svg = `<svg width="30" height="38" viewBox="0 0 30 38" xmlns="http://www.w3.org/2000/svg">
        <path d="M15 0C6.7 0 0 6.7 0 15c0 10.5 15 23 15 23s15-12.5 15-23C30 6.7 23.3 0 15 0z" fill="${color}"/>
        <circle cx="15" cy="15" r="7.5" fill="#ffffff"/>
        <text x="15" y="19.5" font-size="11" text-anchor="middle" fill="${color}" font-family="Arial" font-weight="bold">${glyph}</text>
    </svg>`;
    return L.divIcon({ html: svg, className: '', iconSize: [30,38], iconAnchor: [15,38], popupAnchor: [0,-34] });
}

const markers = {};
allServices.filter(s => s.lat !== null && s.lng !== null).forEach(s => {
    const marker = L.marker([s.lat, s.lng], { icon: pinIcon(s.categorie) });
    marker.bindPopup(`
        <div class="popup-cat" style="color:${CATEGORY_COLORS[s.categorie]}">${CATEGORY_LABELS[s.categorie] || s.categorie}</div>
        <div class="popup-title">${s.name}</div>
        <div class="popup-address">${s.address}${s.quartier ? ' · ' + s.quartier : ''}</div>
    `);
    marker.on('click', () => selectService(s.id, false));
    markers[s.id] = marker;
});

let activeCategory = 'tous';
let searchTerm = '';
let activeId = null;

/* ---------- Bottom-sheet mobile ---------- */
const sidebarEl = document.getElementById('urg-sidebar');
const sheetHandle = document.getElementById('sheetHandle');
const sheetHandleText = document.getElementById('sheetHandleText');
const sheetBackdrop = document.getElementById('sheetBackdrop');

function isMobile(){
    return window.matchMedia('(max-width:760px)').matches;
}

function setSheetExpanded(expanded){
    sidebarEl.classList.toggle('expanded', expanded);
    sheetBackdrop.classList.toggle('show', expanded);
    if (expanded && sheetHandleText){
        sheetHandleText.textContent = 'Toucher pour voir la carte';
    } else {
        updateHandleLabel();
    }
}

function updateHandleLabel(){
    if (!sheetHandleText || sidebarEl.classList.contains('expanded')) return;
    const visibleCount = allServices.filter(matchesFilters).length;
    sheetHandleText.textContent = visibleCount + ' service' + (visibleCount > 1 ? 's' : '') + ' à proximité · Toucher pour la liste';
}

if (sheetHandle){
    sheetHandle.addEventListener('click', () => {
        setSheetExpanded(!sidebarEl.classList.contains('expanded'));
    });
}
if (sheetBackdrop){
    sheetBackdrop.addEventListener('click', () => setSheetExpanded(false));
}

function matchesFilters(s){
    const catOk = activeCategory === 'tous' || s.categorie === activeCategory;
    const term = searchTerm.trim().toLowerCase();
    const searchOk = !term
        || s.name.toLowerCase().includes(term)
        || s.address.toLowerCase().includes(term)
        || (s.quartier && s.quartier.toLowerCase().includes(term));
    return catOk && searchOk;
}

function render(){
    Object.keys(markers).forEach(id => map.removeLayer(markers[id]));
    const visible = allServices.filter(matchesFilters);
    visible.forEach(s => { if (markers[s.id]) markers[s.id].addTo(map); });

    const listEl = document.getElementById('urg-list');
    listEl.innerHTML = '';
    if (!visible.length){
        listEl.innerHTML = `<div class="empty-state">Aucun résultat pour cette recherche.</div>`;
    } else {
        visible.forEach(s => {
            const hasPosition = s.lat !== null && s.lng !== null;
            const card = document.createElement('div');
            card.className = 'card' + (hasPosition ? '' : ' no-position');
            card.id = 'card-' + s.id;
            card.style.borderLeftColor = CATEGORY_COLORS[s.categorie];
            card.innerHTML = `
                <div class="card-top">
                    <div>
                        <div class="card-cat" style="color:${CATEGORY_COLORS[s.categorie]}">${CATEGORY_LABELS[s.categorie] || s.categorie}</div>
                        <div class="card-name">${s.name}</div>
                    </div>
                    ${!hasPosition ? '<span class="badge">Position à préciser</span>' : ''}
                </div>
                <div class="card-address">${s.address}</div>
                ${s.quartier ? `<div class="card-quartier">Quartier : ${s.quartier}</div>` : ''}
                ${s.phone ? `<div class="card-phone">${s.phone}</div>` : ''}
            `;
            if (hasPosition){
                card.addEventListener('click', () => selectService(s.id, true));
            }
            listEl.appendChild(card);
        });
    }

    document.getElementById('urg-count').textContent =
        visible.length + ' résultat' + (visible.length > 1 ? 's' : '');

    updateHandleLabel();
}

function selectService(id, flyMap){
    if (activeId) document.getElementById('card-' + activeId)?.classList.remove('active');
    activeId = id;
    const cardEl = document.getElementById('card-' + id);
    cardEl?.classList.add('active');
    cardEl?.scrollIntoView({ behavior:'smooth', block:'nearest' });

    const s = allServices.find(x => String(x.id) === String(id));
    if (!s || s.lat === null || s.lng === null) return;
    if (flyMap) map.flyTo([s.lat, s.lng], 16, { duration: 0.8 });
    markers[id]?.openPopup();

    // Sur mobile, quand on choisit un service depuis la liste, on referme
    // la sheet pour laisser voir la carte et le pin sélectionné.
    if (flyMap && isMobile()) setSheetExpanded(false);
}

document.getElementById('urg-search').addEventListener('input', (e) => {
    searchTerm = e.target.value;
    render();
});

document.querySelectorAll('#urg-pills .pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('#urg-pills .pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        activeCategory = pill.dataset.cat;
        render();
    });
});

render();
</script>
</body>
</html>