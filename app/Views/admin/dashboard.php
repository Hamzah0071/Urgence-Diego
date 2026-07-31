<main class="admin-content">
    <div class="admin-header" style="margin-bottom: 30px;">
        <h1>Tableau de bord</h1>
        <p>Gestion de la plateforme Urgences Antsiranana.</p>
    </div>

    <!-- Grille de statistiques -->
    <div class="admin-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">Articles total</h3>
            <p class="stat-number" style="font-size: 1.8rem; font-weight: 700; color: #1e40af;"><?= $stats['articles_total'] ?></p>
        </div>
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">En attente</h3>
            <p class="stat-number" style="font-size: 1.8rem; font-weight: 700; color: #ea580c;"><?= $stats['articles_attente'] ?></p>
        </div>
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">Services</h3>
            <p class="stat-number" style="font-size: 1.8rem; font-weight: 700; color: #16a34a;"><?= $stats['services_total'] ?></p>
        </div>
        <div class="stat-card" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">Utilisateurs</h3>
            <p class="stat-number" style="font-size: 1.8rem; font-weight: 700; color: #0284c7;"><?= $stats['users_total'] ?></p>
        </div>
    </div>

    <!-- Section articles en attente -->
    <section class="recent-updates" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Articles en attente de validation</h2>
            <a href="index.php?action=admin-articles" style="color: #1e40af; text-decoration: none; font-size: 0.9rem;">Voir tout →</a>
        </div>

        <?php if (empty($articlesAttente)): ?>
            <p style="text-align: center; padding: 20px; color: #64748b;">Aucun article en attente.</p>
        <?php else: ?>
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px;">Titre</th>
                        <th style="padding: 12px;">Auteur</th>
                        <th style="padding: 12px;">Date</th>
                        <th style="padding: 12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articlesAttente as $art): ?>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 12px;"><?= htmlspecialchars($art['titre']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($art['prenom'] . ' ' . $art['nom']) ?></td>
                            <td style="padding: 12px;"><?= date('d/m/Y', strtotime($art['date_publication'])) ?></td>
                            <td style="padding: 12px;">
                                <a href="index.php?action=admin-articles&valider=<?= $art['id_article'] ?>" 
                                   style="background: #16a34a; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;"
                                   onclick="return confirm('Valider cet article ?')">
                                   Valider
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
