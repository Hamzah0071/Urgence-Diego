<title>Sources d'articles</title>

<main class="admin-content">
    <div class="admin-container">
        <div class="admin-content">
            <header>
                <h1>Gestion des Sources d'Articles</h1>
                <p>Ajouter, modifier ou supprimer les chaînes TV / réseaux sociaux utilisés pour alimenter les actualités du site.</p>
                <a href="index.php?action=admin-import-articles" class="btn-primary" style="text-decoration:none; display:inline-block; background: #1e40af; color: #fff; padding: 10px 15px; border-radius: 5px;">
                    <i class="fa-solid fa-arrows-rotate"></i> Actualiser les articles maintenant
                </a>
            </header>

            <?php 
            if (isset($_GET['import_done'])): 
                $res = $_SESSION['import_result'] ?? ['count' => 0, 'errors' => []];
                unset($_SESSION['import_result']);
            ?>
                <div class="alert success" style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    Importation terminée : <strong><?= $res['count'] ?></strong> nouveaux articles ajoutés.
                    <?php if (!empty($res['errors'])): ?>
                        <ul style="margin-top: 10px; font-size: 0.85rem;">
                            <?php foreach ($res['errors'] as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($erreur): ?><div class="alert erreur" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 20px 0;"><?php echo htmlspecialchars($erreur); ?></div><?php endif; ?>
            <?php if ($succes): ?><div class="alert success" style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 5px; margin: 20px 0;"><?php echo htmlspecialchars($succes); ?></div><?php endif; ?>

            <!-- Formulaire d'ajout -->
            <section class="add-pharmacy-section" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <h2 style="margin-bottom: 20px;">Ajouter une nouvelle source</h2>
                <form method="post" action="index.php?action=admin-articles" id="form-reseau_social">
                    <input type="hidden" name="action" value="ajouter">

                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nom de la chaîne</label>
                            <input type="text" name="nom_source" placeholder="Ex : TVM - Télévision Malagasy" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 5px;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">URL du flux RSS</label>
                            <input type="url" name="url_flux" placeholder="https://rss.app/feeds/xxxxx.xml" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 5px;">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top: 20px; background: #1e40af; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Ajouter la chaîne</button>
                </form>
            </section>

            <!-- Liste des sources -->
            <section class="pharmacy-list-section" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <h2>Sources actuelles</h2>

                <?php if (empty($sources)): ?>
                    <p class="empty-state" style="padding: 20px; text-align: center; color: #64748b;">Aucune source enregistrée pour le moment.</p>
                <?php else: ?>
                    <div class="table-scroll" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 12px;">Nom</th>
                                    <th style="padding: 12px;">URL du flux</th>
                                    <th style="padding: 12px;">Statut</th>
                                    <th style="padding: 12px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sources as $s): ?>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($s['nom_source']); ?></td>
                                    <td style="padding: 12px; font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($s['url_flux']); ?></td>
                                    <td style="padding: 12px;">
                                        <span style="color: <?= $s['actif'] ? '#166534' : '#991b1b' ?>; font-weight: 600;">
                                            <?= $s['actif'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px; display: flex; gap: 5px;">
                                        <form method="post" action="index.php?action=admin-articles">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id_source" value="<?php echo (int)$s['id_source']; ?>">
                                            <button type="submit" style="padding: 5px 10px; border-radius: 5px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;"><?php echo $s['actif'] ? 'Désactiver' : 'Activer'; ?></button>
                                        </form>

                                        <form method="post" action="index.php?action=admin-articles" onsubmit="return confirm('Supprimer cette source ?');">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id_source" value="<?php echo (int)$s['id_source']; ?>">
                                            <button type="submit" style="padding: 5px 10px; border-radius: 5px; border: none; background: #fee2e2; color: #991b1b; cursor: pointer;">Supprimer</button>
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
