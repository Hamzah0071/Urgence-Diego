<title>Services d'urgence</title>

<main class="admin-content">
    <div class="admin-container">
        <div class="admin-content">
            <header style="margin-bottom: 30px;">
                <h1>Gestion des Services d'Urgence</h1>
                <p>Ajouter, modifier ou supprimer les pharmacies, pompiers, forces de l'ordre et hôpitaux.</p>
            </header>

            <?php if ($erreur): ?><div class="alert erreur" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin-bottom: 20px;"><?php echo htmlspecialchars($erreur); ?></div><?php endif; ?>
            <?php if ($succes): ?><div class="alert success" style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 5px; margin-bottom: 20px;"><?php echo htmlspecialchars($succes); ?></div><?php endif; ?>

            <!-- Onglets par type de service -->
            <div class="onglets" style="display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap;">
                <?php foreach ($types as $t):
                    $icone = $iconesType[$t['nom_type']] ?? 'fa-building';
                    $actif = ((int)$t['id_type'] === $idTypeActif) ? 'background: #1e40af; color: #fff;' : 'background: #fff; color: #64748b;';
                ?>
                    <a href="index.php?action=admin-services&type=<?php echo (int)$t['id_type']; ?>" 
                       style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; transition: all 0.2s; <?= $actif ?>">
                        <i class="fas <?php echo $icone; ?>"></i> <?php echo htmlspecialchars($t['nom_type']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Formulaire d'ajout -->
            <section class="add-pharmacy-section" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 35px; border: 1px solid #e2e8f0;">
                <h2 style="margin-bottom: 20px; font-size: 1.2rem; color: #1e293b;">
                    <i class="fas fa-plus-circle" style="color: #1e40af;"></i> 
                    Ajouter un nouveau service
                </h2>

                <form method="post" action="index.php?action=admin-services&type=<?= $idTypeActif ?>">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="id_type" value="<?php echo $idTypeActif; ?>">

                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Nom du service</label>
                            <input type="text" name="libelle" placeholder="Ex : Pharmacie Mora" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Téléphone</label>
                            <input type="text" name="telephone" placeholder="Ex : 032 78 826 04" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Adresse complète</label>
                            <input type="text" name="adresse" placeholder="Rue, Point de repère..." required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Quartier / Fokontany</label>
                            <select name="id_quartier" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">-- Choisir un quartier --</option>
                                <?php foreach ($quartiers as $q): ?>
                                    <option value="<?php echo (int)$q['id_quartier']; ?>"><?php echo htmlspecialchars($q['nom_quartier']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Description courte (optionnel)</label>
                            <input type="text" name="description" placeholder="Ex : Près de la banque..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                    </div>

                    <button type="submit" style="margin-top: 25px; background: #1e40af; color: #fff; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                        Enregistrer le service
                    </button>
                </form>
            </section>

            <!-- Liste des services -->
            <section class="pharmacy-list-section" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <h2 style="margin-bottom: 20px; font-size: 1.2rem; color: #1e293b;">Liste des services enregistrés</h2>

                <?php if (empty($services)): ?>
                    <p style="text-align: center; padding: 30px; color: #64748b; font-style: italic;">Aucun service enregistré pour ce type.</p>
                <?php else: ?>
                    <div class="table-scroll" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 15px;">Nom</th>
                                    <th style="padding: 15px;">Téléphone</th>
                                    <th style="padding: 15px;">Quartier</th>
                                    <th style="padding: 15px;">Statut</th>
                                    <th style="padding: 15px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $s): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                                    <td style="padding: 15px; font-weight: 500; color: #1e293b;"><?php echo htmlspecialchars($s['libelle']); ?></td>
                                    <td style="padding: 15px; color: #1e40af; font-family: monospace;"><?php echo htmlspecialchars($s['telephone']); ?></td>
                                    <td style="padding: 15px; color: #64748b;"><?php echo htmlspecialchars($s['nom_quartier'] ?? '—'); ?></td>
                                    <td style="padding: 15px;">
                                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; <?= $s['actif'] ? 'background: #dcfce7; color: #166534;' : 'background: #fee2e2; color: #991b1b;' ?>">
                                            <?= $s['actif'] ? 'ACTIF' : 'INACTIF' ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px; display: flex; gap: 8px; flex-wrap: wrap;">
                                        <button type="button"
                                            class="btn-modifier-service"
                                            data-id="<?= (int)$s['id_service'] ?>"
                                            data-libelle="<?= htmlspecialchars($s['libelle'], ENT_QUOTES) ?>"
                                            data-telephone="<?= htmlspecialchars($s['telephone'], ENT_QUOTES) ?>"
                                            data-adresse="<?= htmlspecialchars($s['adresse'], ENT_QUOTES) ?>"
                                            data-quartier="<?= (int)$s['id_quartier'] ?>"
                                            data-description="<?= htmlspecialchars($s['description'] ?? '', ENT_QUOTES) ?>"
                                            style="padding: 6px 12px; border-radius: 6px; border: 1px solid #1e40af; background: #eff6ff; color: #1e40af; cursor: pointer; font-size: 0.8rem;">
                                            <i class="fas fa-pen"></i> Modifier
                                        </button>

                                        <form method="post" action="index.php?action=admin-services&type=<?= $idTypeActif ?>" style="display:inline;">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id_service" value="<?php echo (int)$s['id_service']; ?>">
                                            <button type="submit" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; font-size: 0.8rem;"><?php echo $s['actif'] ? 'Désactiver' : 'Activer'; ?></button>
                                        </form>

                                        <form method="post" action="index.php?action=admin-services&type=<?= $idTypeActif ?>" onsubmit="return confirm('Supprimer ce service ?');" style="display:inline;">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id_service" value="<?php echo (int)$s['id_service']; ?>">
                                            <button type="submit" style="padding: 6px 12px; border-radius: 6px; border: none; background: #fee2e2; color: #991b1b; cursor: pointer; font-size: 0.8rem;">Supprimer</button>
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

<!-- ============================================================
     MODAL DE MODIFICATION D'UN SERVICE
     ============================================================ -->
<div id="modal-modifier-service" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:1000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:14px; max-width:520px; width:100%; padding:30px; box-shadow:0 20px 50px rgba(0,0,0,0.25); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="font-size:1.2rem; color:#1e293b; margin:0;">
                <i class="fas fa-pen" style="color:#1e40af;"></i> Modifier le service
            </h2>
            <button type="button" id="btn-fermer-modal" style="background:none; border:none; font-size:1.3rem; color:#64748b; cursor:pointer; line-height:1;">&times;</button>
        </div>

        <form method="post" action="index.php?action=admin-services&type=<?= $idTypeActif ?>">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id_service" id="modif-id-service" value="">

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Nom du service</label>
                <input type="text" name="libelle" id="modif-libelle" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Téléphone</label>
                <input type="text" name="telephone" id="modif-telephone" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Adresse complète</label>
                <input type="text" name="adresse" id="modif-adresse" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Quartier / Fokontany</label>
                <select name="id_quartier" id="modif-quartier" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="">-- Choisir un quartier --</option>
                    <?php foreach ($quartiers as $q): ?>
                        <option value="<?php echo (int)$q['id_quartier']; ?>"><?php echo htmlspecialchars($q['nom_quartier']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; color: #475569;">Description courte (optionnel)</label>
                <input type="text" name="description" id="modif-description" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" id="btn-annuler-modal" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color:#475569; font-weight: 600; cursor: pointer;">
                    Annuler
                </button>
                <button type="submit" style="padding: 10px 20px; border-radius: 8px; border: none; background: #1e40af; color: #fff; font-weight: 600; cursor: pointer;">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modal-modifier-service');
    const boutonsModifier = document.querySelectorAll('.btn-modifier-service');
    const btnFermer = document.getElementById('btn-fermer-modal');
    const btnAnnuler = document.getElementById('btn-annuler-modal');

    function ouvrirModal(data) {
        document.getElementById('modif-id-service').value = data.id;
        document.getElementById('modif-libelle').value = data.libelle;
        document.getElementById('modif-telephone').value = data.telephone;
        document.getElementById('modif-adresse').value = data.adresse;
        document.getElementById('modif-quartier').value = data.quartier;
        document.getElementById('modif-description').value = data.description;
        modal.style.display = 'flex';
    }

    function fermerModal() {
        modal.style.display = 'none';
    }

    boutonsModifier.forEach(function (btn) {
        btn.addEventListener('click', function () {
            ouvrirModal({
                id: btn.dataset.id,
                libelle: btn.dataset.libelle,
                telephone: btn.dataset.telephone,
                adresse: btn.dataset.adresse,
                quartier: btn.dataset.quartier,
                description: btn.dataset.description
            });
        });
    });

    btnFermer.addEventListener('click', fermerModal);
    btnAnnuler.addEventListener('click', fermerModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) fermerModal();
    });
})();
</script>