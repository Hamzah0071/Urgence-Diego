<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <title>Faire un don - Urgences Antsiranana</title>
    <link rel="stylesheet" href="public/asset/css/client/home.css">
    <link rel="stylesheet" href="public/asset/icon/fontAwesome/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <style>
        /* Complément propre à cette page : réutilise les variables de home.css
           (--navy, --navy-dk, --paper, --sage, --line, --radius). */
        .don-section {
            max-width: 900px;
            margin: 0 auto;
            padding: 50px 20px 70px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title h1 {
            font-family: 'Fraunces', serif;
            font-size: 2.1rem;
            color: var(--navy-dk);
            margin: 0 0 10px;
        }

        .page-title p {
            color: #64748b;
            font-size: 1.02rem;
            max-width: 480px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .don-info {
            background: var(--sage);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 28px 32px;
            margin-bottom: 40px;
        }

        .don-info h2 {
            font-family: 'Fraunces', serif;
            font-size: 1.3rem;
            color: var(--navy-dk);
            margin: 0 0 16px;
        }

        .don-info ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .don-info li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            color: var(--ink);
        }

        .don-info li::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #10b981;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .don-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .don-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 28px 24px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .don-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        }

        .don-card h3 {
            font-family: 'Fraunces', serif;
            font-size: 1.15rem;
            color: var(--navy-dk);
            margin: 0 0 12px;
        }

        .don-card p {
            font-family: 'Space Mono', monospace;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--ink);
            margin: 0 0 20px;
            letter-spacing: 0.02em;
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--sage);
            color: var(--navy-dk);
            border: 1px solid var(--line);
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: var(--navy-dk);
            color: #ffffff;
            border-color: var(--navy-dk);
        }

        .copy-btn.copied {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }

        @media (max-width: 600px) {
            .don-info ul {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>

<section class="don-section">

    <div class="page-title">
        <h1>Faire un don</h1>
        <p>
            Soutenez le développement et la maintenance de la plateforme
            Urgences Antsiranana.
        </p>
    </div>

    <div class="don-info">
        <h2>Pourquoi faire un don ?</h2>
        <ul>
            <li>Mettre à jour les numéros d'urgence</li>
            <li>Ajouter de nouveaux services</li>
            <li>Améliorer la plateforme</li>
            <li>Assurer son fonctionnement</li>
        </ul>
    </div>

    <div class="don-grid">

        <div class="don-card">
            <h3>Mvola</h3>
            <p>034 XX XX XX</p>
            <button class="copy-btn" data-numero="034 XX XX XX">
                <i class="fa-solid fa-copy"></i>
                Copier
            </button>
        </div>

        <div class="don-card">
            <h3>Orange Money</h3>
            <p>032 XX XX XX</p>
            <button class="copy-btn" data-numero="032 XX XX XX">
                <i class="fa-solid fa-copy"></i>
                Copier
            </button>
        </div>

        <div class="don-card">
            <h3>Airtel Money</h3>
            <p>033 XX XX XX</p>
            <button class="copy-btn" data-numero="033 XX XX XX">
                <i class="fa-solid fa-copy"></i>
                Copier
            </button>
        </div>

    </div>

</section>

<script>
    document.querySelectorAll('.copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const numero = btn.dataset.numero || '';
            const restaurerLibelle = () => {
                btn.innerHTML = '<i class="fa-solid fa-copy"></i> Copier';
                btn.classList.remove('copied');
            };

            const afficherSucces = () => {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Copié !';
                btn.classList.add('copied');
                setTimeout(restaurerLibelle, 1800);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(numero).then(afficherSucces).catch(() => {
                    alert('Impossible de copier automatiquement. Numéro : ' + numero);
                });
            } else {
                // Repli pour les navigateurs/contextes sans Clipboard API (ex: http non sécurisé)
                const zoneTemp = document.createElement('textarea');
                zoneTemp.value = numero;
                zoneTemp.style.position = 'fixed';
                zoneTemp.style.opacity = '0';
                document.body.appendChild(zoneTemp);
                zoneTemp.select();
                try {
                    document.execCommand('copy');
                    afficherSucces();
                } catch (e) {
                    alert('Impossible de copier automatiquement. Numéro : ' + numero);
                }
                document.body.removeChild(zoneTemp);
            }
        });
    });
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>