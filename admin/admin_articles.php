<?php
/**
 * admin_articles.php
 * -------------------------------------------------------------
 * Gestion des sources d'articles / actualités pour le site.
 * Deux types de sources :
 *   - "rss"          : flux RSS classique (URL directe)
 *   - "reseau_social" : chaîne TV malgache sans RSS natif. On ne
 *                       scrape pas Facebook directement : l'admin
 *                       colle ici une URL de flux déjà générée par
 *                       un outil externe (rss.app, RSS-Bridge
 *                       auto-hébergé, etc.), et elle est ensuite
 *                       traitée comme n'importe quel flux RSS.
 * -------------------------------------------------------------
 */

require_once '../includes/admin_session.php';

/* -------------------------------------------------------------
 * Table + mise à niveau des colonnes si la table existe déjà
 * avec un schéma plus ancien
 * ----------------------------------------------------------- */
$pdo->exec("
    CREATE TABLE IF NOT EXISTS sources_articles (
        id_source INT AUTO_INCREMENT PRIMARY KEY,
        nom_source VARCHAR(150) NOT NULL,
        type_source ENUM('rss','reseau_social') NOT NULL DEFAULT 'rss',
        url_flux VARCHAR(500) NOT NULL,
        actif TINYINT(1) NOT NULL DEFAULT 1,
        date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
// Ajoute les colonnes manquantes si la table existait déjà sous une forme plus simple
$pdo->exec("ALTER TABLE sources_articles ADD COLUMN IF NOT EXISTS type_source ENUM('rss','reseau_social') NOT NULL DEFAULT 'rss'");

/* -------------------------------------------------------------
 * Liste pré-configurée des chaînes TV malgaches sans RSS natif
 * ----------------------------------------------------------- */
$chaines_predefinies = [
    'TVM - Télévision Malagasy',
    'TV Plus Madagascar',
    'Real TV Madagascar',
    'RTA (Radio Télévision Analamanga)',
    'Viva TV Madagascar',
    'MBS (Madagascar Broadcasting System)',
    'Record TV Madagascar',
    'IBC Madagascar',
    "Dream'in TV",
    'Kolo TV',
    'MATV',
    'Autre (nom personnalisé)',
];

$erreur = '';
$succes = '';

/* -------------------------------------------------------------
 * Traitement des actions
 * ----------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // --- Ajout d'une source ---
    if ($action === 'ajouter') {
        $type = $_POST['type_source'] ?? 'rss';

        if ($type === 'rss') {
            $nom = trim($_POST['nom_source'] ?? '');
            $url = trim($_POST['url_flux'] ?? '');

            if ($nom === '' || $url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
                $erreur = "Merci de renseigner un nom et une URL de flux RSS valide.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO sources_articles (nom_source, type_source, url_flux)
                    VALUES (:nom, 'rss', :url)
                ");
                $stmt->execute(['nom' => $nom, 'url' => $url]);
                $succes = "Source RSS ajoutée avec succès.";
            }

        } elseif ($type === 'reseau_social') {
            $nom_choisi = trim($_POST['chaine_predefinie'] ?? '');
            $nom_perso  = trim($_POST['nom_source_perso'] ?? '');
            $url        = trim($_POST['url_flux'] ?? '');

            $nom_final = ($nom_choisi === 'Autre (nom personnalisé)') ? $nom_perso : $nom_choisi;

            if ($nom_final === '' || $url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
                $erreur = "Merci de choisir une chaîne et de coller une URL de flux valide.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO sources_articles (nom_source, type_source, url_flux)
                    VALUES (:nom, 'reseau_social', :url)
                ");
                $stmt->execute(['nom' => $nom_final, 'url' => $url]);
                $succes = "Source réseau social ajoutée avec succès.";
            }
        }
    }

    // --- Modification d'une source ---
    if ($action === 'modifier' && isset($_POST['id_source'])) {
        $nom = trim($_POST['nom_source'] ?? '');
        $url = trim($_POST['url_flux'] ?? '');

        if ($nom === '' || $url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            $erreur = "Merci de renseigner un nom et une URL valide pour la modification.";
        } else {
            $stmt = $pdo->prepare("UPDATE sources_articles SET nom_source = :nom, url_flux = :url WHERE id_source = :id");
            $stmt->execute(['nom' => $nom, 'url' => $url, 'id' => $_POST['id_source']]);
            $succes = "Source modifiée avec succès.";
        }
    }

    // --- Suppression d'une source ---
    if ($action === 'supprimer' && isset($_POST['id_source'])) {
        $stmt = $pdo->prepare("DELETE FROM sources_articles WHERE id_source = :id");
        $stmt->execute(['id' => $_POST['id_source']]);
        $succes = "Source supprimée.";
    }

    // --- Activation / désactivation d'une source ---
    if ($action === 'toggle' && isset($_POST['id_source'])) {
        $stmt = $pdo->prepare("UPDATE sources_articles SET actif = 1 - actif WHERE id_source = :id");
        $stmt->execute(['id' => $_POST['id_source']]);
        $succes = "Statut mis à jour.";
    }
}

/* -------------------------------------------------------------
 * Récupération de la liste des sources existantes
 * ----------------------------------------------------------- */
$sources = $pdo->query("SELECT * FROM sources_articles ORDER BY type_source, nom_source")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/admin_header.php';
?>
<title>Sources d'articles</title>

<main class="admin-content">
    <div class="admin-container">
        <div class="admin-content">
            <header>
                <h1>Gestion des Sources d'Articles</h1>
                <p>Ajouter, modifier ou supprimer des flux RSS et des chaînes TV utilisés pour alimenter les actualités du site.</p>
            </header>

            <?php if ($erreur): ?><div class="alert erreur"><?php echo htmlspecialchars($erreur); ?></div><?php endif; ?>
            <?php if ($succes): ?><div class="alert success"><?php echo htmlspecialchars($succes); ?></div><?php endif; ?>

            <!-- Formulaires d'ajout -->
            <section class="add-pharmacy-section">
                <div class="onglets">
                    <button type="button" class="onglet-btn actif" onclick="afficherOnglet('rss')">Flux RSS classique</button>
                    <button type="button" class="onglet-btn" onclick="afficherOnglet('reseau_social')">Chaîne TV / Réseaux sociaux</button>
                </div>

                <!-- Formulaire : source RSS classique -->
                <form method="post" class="bloc-form actif" id="form-rss">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="type_source" value="rss">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nom_source_rss">Nom de la source</label>
                            <input type="text" id="nom_source_rss" name="nom_source" placeholder="Ex : Midi Madagasikara" required>
                        </div>
                        <div class="form-group">
                            <label for="url_flux_rss">URL du flux RSS</label>
                            <input type="url" id="url_flux_rss" name="url_flux" placeholder="https://exemple.mg/feed" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary mt-30">Ajouter le flux RSS</button>
                </form>

                <!-- Formulaire : chaîne TV / réseau social -->
                <form method="post" class="bloc-form" id="form-reseau_social">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="type_source" value="reseau_social">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="select-chaine">Chaîne</label>
                            <select name="chaine_predefinie" id="select-chaine" onchange="toggleNomPerso()">
                                <?php foreach ($chaines_predefinies as $chaine): ?>
                                    <option value="<?php echo htmlspecialchars($chaine); ?>"><?php echo htmlspecialchars($chaine); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" id="bloc-nom-perso" style="display:none;">
                            <label for="nom_source_perso">Nom personnalisé</label>
                            <input type="text" id="nom_source_perso" name="nom_source_perso" placeholder="Nom de la chaîne">
                        </div>

                        <div class="form-group full-width">
                            <label for="url_flux_reseau">URL du flux (déjà générée)</label>
                            <input type="url" id="url_flux_reseau" name="url_flux" placeholder="https://rss.app/feeds/xxxxx.xml" required>
                            <p class="note">
                                Cette chaîne n'a pas de RSS natif : génère l'URL de son flux avec un outil externe
                                comme <strong>rss.app</strong> (rapide, sans hébergement) ou une instance
                                <strong>RSS-Bridge</strong> auto-hébergée, à partir de sa page Facebook publique.
                                Colle ici l'URL du flux déjà générée.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary mt-30">Ajouter la chaîne</button>
                </form>
            </section>

            <!-- Liste des sources -->
            <section class="pharmacy-list-section">
                <h2>Sources actuelles</h2>

                <?php if (empty($sources)): ?>
                    <p class="empty-state">Aucune source enregistrée pour le moment.</p>
                <?php else: ?>
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>URL du flux</th>
                                    <th>Statut</th>
                                    <th class="actions-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sources as $s): ?>
                                <tr>
                                    <td data-label="Nom"><?php echo htmlspecialchars($s['nom_source']); ?></td>
                                    <td data-label="Type">
                                        <?php if ($s['type_source'] === 'rss'): ?>
                                            <span class="badge badge-rss">RSS</span>
                                        <?php else: ?>
                                            <span class="badge badge-reseau">Réseau social</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="URL du flux" style="word-break:break-all;"><?php echo htmlspecialchars($s['url_flux']); ?></td>
                                    <td data-label="Statut">
                                        <?php if ($s['actif']): ?>
                                            <span class="etat-actif">Actif</span>
                                        <?php else: ?>
                                            <span class="etat-inactif">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell" data-label="Actions">
                                        <button
                                            type="button"
                                            class="edit-btn"
                                            data-id_source="<?php echo htmlspecialchars($s['id_source']); ?>"
                                            data-nom_source="<?php echo htmlspecialchars($s['nom_source']); ?>"
                                            data-url_flux="<?php echo htmlspecialchars($s['url_flux']); ?>"
                                        >
                                            Modifier
                                        </button>

                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id_source" value="<?php echo (int)$s['id_source']; ?>">
                                            <button type="submit" class="btn-toggle"><?php echo $s['actif'] ? 'Désactiver' : 'Activer'; ?></button>
                                        </form>

                                        <form method="post" class="inline-form" onsubmit="return confirm('Supprimer cette source ?');">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id_source" value="<?php echo (int)$s['id_source']; ?>">
                                            <button type="submit" class="btn-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>

<!-- Modal de modification -->
<div id="editSourceModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier la source</h2>

        <form method="post">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" id="edit_id_source" name="id_source">

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_nom_source">Nom de la source</label>
                    <input type="text" id="edit_nom_source" name="nom_source" required>
                </div>
                <div class="form-group">
                    <label for="edit_url_flux">URL du flux</label>
                    <input type="url" id="edit_url_flux" name="url_flux" required>
                </div>
            </div>

            <button type="submit" class="btn-primary mt-30">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<style>
    :root {
        --primary: #2c7a7b;
        --primary-dark: #1f5b5c;
        --danger: #e74c3c;
        --danger-dark: #c0392b;
        --success-bg: #d4edda;
        --success-text: #155724;
        --success-border: #c3e6cb;
        --erreur-bg: #fdecea;
        --erreur-text: #a12b2b;
        --border-color: #dcdfe3;
        --text-main: #2d3436;
        --text-muted: #6b7280;
        --radius: 8px;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-lg: 0 10px 30px rgba(0,0,0,0.2);
    }

    .admin-content { width: 100%; max-width: 1100px; margin: 0 auto; padding: 20px; box-sizing: border-box; color: var(--text-main); }
    .admin-container, .add-pharmacy-section, .pharmacy-list-section { box-sizing: border-box; }
    .admin-content header h1 { margin: 0 0 5px; font-size: 1.8rem; }
    .admin-content header p { margin: 0 0 25px; color: var(--text-muted); }

    .alert { padding: 14px 18px; margin-bottom: 20px; border-radius: var(--radius); font-weight: 600; border: 1px solid transparent; }
    .alert.success { background-color: var(--success-bg); color: var(--success-text); border-color: var(--success-border); }
    .alert.erreur { background-color: var(--erreur-bg); color: var(--erreur-text); border-color: #f5c6cb; }

    .add-pharmacy-section, .pharmacy-list-section {
        background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius);
        box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px;
    }
    .pharmacy-list-section h2 { margin-top: 0; margin-bottom: 20px; font-size: 1.3rem; border-bottom: 2px solid var(--primary); padding-bottom: 10px; }
    .empty-state { color: var(--text-muted); font-style: italic; }

    .onglets { display: flex; gap: 10px; margin-bottom: 20px; }
    .onglet-btn { padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; background: #f3f4f6; font-weight: 600; color: var(--text-main); }
    .onglet-btn.actif { background: var(--primary); color: #fff; border-color: var(--primary); }
    .bloc-form { display: none; }
    .bloc-form.actif { display: block; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: var(--text-main); }
    .form-group input, .form-group select, .form-group textarea {
        padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; width: 100%;
        font-size: 0.95rem; font-family: inherit; transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(44, 122, 123, 0.15);
    }
    .full-width { grid-column: 1 / -1; }
    .note { font-size: 13px; color: var(--text-muted); margin-top: 6px; }

    .btn-primary {
        display: inline-block; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer;
        font-weight: 600; font-size: 0.9rem; background-color: var(--primary); color: #fff;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-primary:hover { background-color: var(--primary-dark); }
    .btn-primary:active { transform: scale(0.97); }

    .btn-danger {
        border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
        background-color: var(--danger); color: white; transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-danger:hover { background-color: var(--danger-dark); }
    .btn-danger:active { transform: scale(0.97); }

    .btn-toggle {
        border: 1px solid var(--border-color); background: #f3f4f6; color: var(--text-main);
        padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .btn-toggle:hover { background-color: #e5e7eb; }
    .btn-toggle:active { transform: scale(0.97); }

    .edit-btn {
        background-color: var(--primary); color: #fff; border: none; padding: 8px 14px; border-radius: 6px;
        cursor: pointer; font-weight: 600; font-size: 0.85rem; margin-right: 6px;
        transition: background-color 0.15s ease, transform 0.05s ease;
    }
    .edit-btn:hover { background-color: var(--primary-dark); }
    .edit-btn:active { transform: scale(0.97); }

    .inline-form { display: inline-block; }
    .mt-30 { margin-top: 30px; }

    .badge { padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; color: #fff; }
    .badge-rss { background: var(--primary); }
    .badge-reseau { background: #8e44ad; }
    .etat-actif { color: #1a7a2e; font-weight: 700; }
    .etat-inactif { color: #a12b2b; font-weight: 700; }

    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid var(--border-color); border-radius: var(--radius); }
    .table-scroll table { min-width: 760px; width: 100%; border-collapse: collapse; }
    .table-scroll th, .table-scroll td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; vertical-align: top; }
    .table-scroll th { background-color: #f7f8f9; font-weight: 700; color: var(--text-main); text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.03em; }
    .table-scroll tbody tr:hover { background-color: #fafbfc; }
    .table-scroll tbody tr:last-child td { border-bottom: none; }
    .actions-cell { white-space: nowrap; }

    @media (max-width: 900px) {
        .admin-content { padding: 16px; }
        .add-pharmacy-section, .pharmacy-list-section { padding: 20px; }
        .form-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
    }
    @media (max-width: 700px) {
        .form-grid { grid-template-columns: 1fr; }
        .admin-content header h1 { font-size: 1.5rem; }
        .onglets { flex-direction: column; }
    }
    @media (max-width: 600px) {
        .admin-content { padding: 12px; }
        .add-pharmacy-section, .pharmacy-list-section { padding: 16px; margin-bottom: 20px; }
        .btn-primary { width: 100%; }

        .table-scroll { overflow-x: visible; border: none; border-radius: 0; }
        .table-scroll table { min-width: 0; width: 100%; }
        .table-scroll thead { display: none; }
        .table-scroll tbody, .table-scroll tr, .table-scroll td { display: block; width: 100%; }
        .table-scroll tr {
            background: #fff; border: 1px solid var(--border-color); border-radius: var(--radius);
            margin-bottom: 14px; padding: 10px 14px; box-shadow: var(--shadow);
        }
        .table-scroll td {
            border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between;
            align-items: flex-start; gap: 12px; text-align: right;
        }
        .table-scroll td:last-child { border-bottom: none; }
        .table-scroll td::before {
            content: attr(data-label); font-weight: 700; font-size: 0.78rem; text-transform: uppercase;
            letter-spacing: 0.03em; color: var(--text-muted); text-align: left; flex-shrink: 0;
        }
        .actions-cell { flex-direction: column; align-items: stretch !important; gap: 8px !important; }
        .actions-cell::before { margin-bottom: 4px; }
        .edit-btn, .btn-toggle, .inline-form, .inline-form .btn-danger { width: 100%; }
        .inline-form { display: block; }
    }

    .modal {
        display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
        overflow: auto; background-color: rgba(0,0,0,0.45);
    }
    .modal-content {
        background-color: #fefefe; margin: 6% auto; padding: 30px; border: none; width: 90%; max-width: 700px;
        max-height: 85vh; overflow-y: auto; border-radius: 10px; position: relative; box-shadow: var(--shadow-lg);
        box-sizing: border-box;
    }
    @media (max-width: 600px) {
        .modal-content { width: 100%; max-width: 100%; height: 100%; max-height: 100%; margin: 0; border-radius: 0; padding: 20px; }
        .modal-content .form-grid { grid-template-columns: 1fr; }
    }
    .modal-content h2 { margin-top: 0; border-bottom: 2px solid var(--primary); padding-bottom: 10px; }
    .close-button {
        color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px;
        line-height: 1; cursor: pointer;
    }
    .close-button:hover, .close-button:focus { color: var(--danger); text-decoration: none; }
</style>

<script>
function afficherOnglet(type) {
    document.querySelectorAll('.onglet-btn').forEach(b => b.classList.remove('actif'));
    document.querySelectorAll('.bloc-form').forEach(f => f.classList.remove('actif'));
    document.getElementById('form-' + type).classList.add('actif');
    event.currentTarget.classList.add('actif');
}

function toggleNomPerso() {
    const select = document.getElementById('select-chaine');
    const bloc = document.getElementById('bloc-nom-perso');
    bloc.style.display = (select.value === 'Autre (nom personnalisé)') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById("editSourceModal");
    var closeBtn = document.querySelector('.close-button');

    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('edit_id_source').value = this.dataset.id_source;
            document.getElementById('edit_nom_source').value = this.dataset.nom_source;
            document.getElementById('edit_url_flux').value = this.dataset.url_flux;
            modal.style.display = "block";
        });
    });

    closeBtn.onclick = function () { modal.style.display = "none"; };
    window.onclick = function (event) {
        if (event.target === modal) { modal.style.display = "none"; }
    };
});
</script>

<?php include '../includes/admin_footer.php'; ?>