<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Urgences Antsiranana">

    <title>Inscription - Urgences Antsiranana</title>
    <link rel="stylesheet" href="asset/icon/fontAwesome/all.min.css">
    <link rel="stylesheet" href="asset/css/registre.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <div class="container">
            <a href="index.php" class="logo-placeholder">URGENCES</a>
            <nav class="nav-links">
                <a href="index.php">← Retour à l'accueil</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="auth-wrapper">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1><i class="fa-solid fa-book-bookmark" style="color: rgb(116, 192, 252);"></i> Inscription</h1>
                        <p>Créez votre compte Urgences Antsiranana</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="success-message">
                            <i class="fa-solid fa-ranking-star" style="color: rgb(99, 230, 190);"></i> Inscription réussie ! Redirection vers la connexion...
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <?php foreach ($errors as $error): ?>
                            <div class="error-message">
                                <i class="fa-solid fa-triangle-exclamation" style="color: rgb(230, 99, 99);"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST" action="index.php?action=register">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input
                                    type="text"
                                    id="nom"
                                    name="nom"
                                    placeholder="Votre nom"
                                    value="<?php echo htmlspecialchars($formData['nom']); ?>"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input
                                    type="text"
                                    id="prenom"
                                    name="prenom"
                                    placeholder="Votre prénom"
                                    value="<?php echo htmlspecialchars($formData['prenom']); ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="votre@email.com"
                                value="<?php echo htmlspecialchars($formData['email']); ?>"
                                required
                                autocomplete="email"
                            >
                        </div>

                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input
                                type="date"
                                id="date_naissance"
                                name="date_naissance"
                                max="<?php echo date('Y-m-d'); ?>"
                                value="<?php echo htmlspecialchars($formData['date_naissance']); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="id_quartier">Quartier</label>
                            <select id="id_quartier" name="id_quartier" required>
                                <option value="">-- Sélectionnez votre quartier --</option>
                                <?php foreach ($quartiers as $quartier): ?>
                                    <option value="<?php echo $quartier['id_quartier']; ?>"
                                        <?php echo ($formData['id_quartier'] == $quartier['id_quartier']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($quartier['nom_quartier']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="password-wrapper">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Au moins 6 caractères"
                                    required
                                    autocomplete="new-password"
                                    onchange="checkPasswordStrength()"
                                    oninput="checkPasswordStrength()"
                                >
                                <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                            </div>
                            <div id="password-strength" class="password-strength"></div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmer le mot de passe</label>
                            <div class="password-wrapper">
                                <input
                                    type="password"
                                    id="password_confirm"
                                    name="password_confirm"
                                    placeholder="Confirmez votre mot de passe"
                                    required
                                    autocomplete="new-password"
                                >
                                <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('password_confirm', this)"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </form>

                    <div class="divider">Vous avez déjà un compte ?</div>

                    <a href="index.php?action=login" class="btn btn-secondary">Se connecter</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Bottom Navigation Bar (Mobile) -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-house"></i></div>
            <div>Accueil</div>
        </a>
        <a href="index.php?action=login" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-door-open"></i></div>
            <div>Connexion</div>
        </a>
        <a href="index.php?action=register" class="nav-item active">
            <div class="nav-item-icon"><i class="fa-solid fa-user-plus"></i></div>
            <div>Inscription</div>
        </a>
    </nav>

    <footer>
        <div class="container">
            <p>&copy; 2026 Urgences Antsiranana</p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem;">Pour votre sécurité</p>
        </div>
    </footer>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.className = isHidden
                ? 'fa-solid fa-ban toggle-password'
                : 'fa-solid fa-eye toggle-password';
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('password-strength');

            if (password.length === 0) {
                strengthDiv.className = 'password-strength';
                return;
            }

            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            if (strength < 2) {
                strengthDiv.textContent = '🟥 Mot de passe faible';
                strengthDiv.className = 'password-strength weak';
            } else if (strength < 4) {
                strengthDiv.textContent = '🟨 Mot de passe moyen';
                strengthDiv.className = 'password-strength medium';
            } else {
                strengthDiv.textContent = '🟩 Mot de passe fort';
                strengthDiv.className = 'password-strength strong';
            }
        }
    </script>
</body>
</html>
