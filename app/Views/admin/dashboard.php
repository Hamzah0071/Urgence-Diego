<link rel="stylesheet" href="/assets/css/admin/admin-dashboard.css">

<main class="admin-content">

    <h1>Tableau de bord</h1>

    <div class="admin-stats">
        <div class="stat-card">
            <h3>Pharmacies de garde aujourd'hui</h3>
            <p class="stat-number"><?= htmlspecialchars($num_pharmacies_garde) ?></p>
            <?php if (!empty($pharmacies_garde)): ?>
                <ul class="stat-detail-list">
                    <?php foreach ($pharmacies_garde as $nom_pharmacie): ?>
                        <li><?= htmlspecialchars($nom_pharmacie) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="stat-empty">Aucune pharmacie de garde aujourd'hui.</p>
            <?php endif; ?>
        </div>
        <div class="stat-card">
            <h3>Services d'urgence actifs</h3>
            <p class="stat-number"><?= htmlspecialchars($num_services_urgence) ?></p>
        </div>
    </div>

    <section class="recent-updates">
        <h2>Mises à jour récentes</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nom</th>
                    <th>Action</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_updates)): ?>
                    <tr><td colspan="5">Aucune mise à jour récente.</td></tr>
                <?php else: ?>
                    <?php foreach ($recent_updates as $update): ?>
                        <tr>
                            <td><?= htmlspecialchars($update['type']) ?></td>
                            <td><?= htmlspecialchars($update['nom']) ?></td>
                            <td><?= htmlspecialchars($update['action']) ?></td>
                            <td><?= htmlspecialchars($update['date']) ?></td>
                            <td><span class="badge badge-<?= strtolower($update['statut']) === 'actif' ? 'active' : 'done' ?>"><?= htmlspecialchars($update['statut']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</main>
