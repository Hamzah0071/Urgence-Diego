<?php
/**
 * Page d'inscription - Urgences Antsiranana
 * Version simplifiée sans Google OAuth
 * UX harmonisée avec la landing page (index.php)
 */

require_once './includes/db_connect.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['id_utilisateur'])) {
    header("Location: home.php");
    exit();
}

// Variables pour le formulaire
$errors = [];
$success = false;
$form_data = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'date_naissance' => '',
    'id_quartier' => '',
];

// Récupérer les quartiers
$quartiers = getQuartiers($pdo);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $id_quartier = intval($_POST['id_quartier'] ?? 0);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Sauvegarder les données pour le formulaire
    $form_data = [
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'date_naissance' => $date_naissance,
        'id_quartier' => $id_quartier,
    ];

    // Validation
    if (empty($nom)) {
        $errors[] = "Le nom est requis.";
    }
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis.";
    }
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    if (!empty($date_naissance)) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_naissance);
        $today = new DateTime();
        if (!$date_obj || $date_obj > $today) {
            $errors[] = "La date de naissance ne peut pas être dans le futur.";
        }
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    if (empty($id_quartier)) {
        $errors[] = "Veuillez sélectionner un quartier.";
    }

    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }

    // Si pas d'erreurs, insérer l'utilisateur
    if (empty($errors)) {
        $password_hashed = hash('sha256', $password);
        $id_role = 1; // Rôle "Visiteur" par défaut
        // date_naissance peut être vide -> NULL en base
        $date_naissance_value = !empty($date_naissance) ? $date_naissance : null;

        try {
            $stmt = $pdo->prepare("
                INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, id_quartier, id_role, date_naissance)
                VALUES (:nom, :prenom, :email, :mot_de_passe, :id_quartier, :id_role, :date_naissance)
            ");
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mot_de_passe' => $password_hashed,
                'id_quartier' => $id_quartier,
                'id_role' => $id_role,
                'date_naissance' => $date_naissance_value,
            ]);

            $success = true;
            $form_data = ['nom' => '', 'prenom' => '', 'email' => '', 'date_naissance' => '', 'id_quartier' => ''];
            // Rediriger après 2 secondes
            header("refresh:2;url=login.php");
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription. Veuillez réessayer.";
            // en dev tu peux logguer $e->getMessage() dans un fichier de log
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Urgences Antsiranana">
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 192 192'><rect fill='%231e40af' width='192' height='192'/><text x='50%' y='50%' font-size='100' fill='white' text-anchor='middle' dy='.3em'>🚨</text></svg>">
    <title>Inscription - Urgences Antsiranana</title>
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --blue-deep: #1e40af;
            --blue-deep-dark: #1e3a8a;
            --red-emergency: #dc2626;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --transition: all 0.3s ease;
            --border-color: #e2e8f0;
            --safe-area-inset-bottom: env(safe-area-inset-bottom, 0);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header identique à la landing page */
        header {
            padding: 1rem 0;
            background: var(--bg-white);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-placeholder {
            width: 120px;
            height: 45px;
            background: linear-gradient(135deg, var(--blue-deep) 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: white;
            font-size: 0.7rem;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .logo-placeholder:active {
            transform: scale(0.95);
        }

        .nav-links {
            display: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--blue-deep);
        }

        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
        }

        .auth-card {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 2rem 1.75rem;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            font-size: 1.8rem;
            color: var(--blue-deep);
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        input, select {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--bg-white);
            color: var(--text-dark);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--blue-deep);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        input::placeholder {
            color: var(--text-muted);
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            border-left: 4px solid var(--red-emergency);
        }

        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            border-left: 4px solid #22c55e;
        }

        .btn {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--blue-deep);
            color: white;
            margin-bottom: 1rem;
        }

        .btn-primary:hover {
            background: var(--blue-deep-dark);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-dark);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .password-strength {
            display: none;
            padding: 0.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            font-weight: 600;
        }

        .password-strength.weak {
            background: #fee2e2;
            color: #991b1b;
            display: block;
        }

        .password-strength.medium {
            background: #fef3c7;
            color: #92400e;
            display: block;
        }

        .password-strength.strong {
            background: #dcfce7;
            color: #166534;
            display: block;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 3rem;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .toggle-password:hover {
            color: var(--blue-deep);
        }

        footer {
            padding: 2rem 0;
            text-align: center;
            color: var(--text-muted);
            border-top: 1px solid #e2e8f0;
            margin-bottom: calc(80px + var(--safe-area-inset-bottom));
            font-size: 0.85rem;
        }

        /* Bottom nav identique à la landing */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-white);
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
            z-index: 99;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
        }

        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 0;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.75rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .nav-item.active {
            color: var(--blue-deep);
        }

        .nav-item-icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        @media (min-width: 768px) {
            .nav-links {
                display: flex;
                gap: 1.5rem;
            }

            .bottom-nav {
                display: none;
            }

            footer {
                margin-bottom: 0;
            }
        }

        @media (max-width: 600px) {
            .auth-card {
                padding: 1.5rem;
            }

            .auth-header h1 {
                font-size: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
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

                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input 
                                    type="text" 
                                    id="nom" 
                                    name="nom" 
                                    placeholder="Votre nom" 
                                    value="<?php echo htmlspecialchars($form_data['nom']); ?>" 
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
                                    value="<?php echo htmlspecialchars($form_data['prenom']); ?>" 
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
                                value="<?php echo htmlspecialchars($form_data['email']); ?>" 
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
                                value="<?php echo htmlspecialchars($form_data['date_naissance']); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="id_quartier">Quartier</label>
                            <select id="id_quartier" name="id_quartier" required>
                                <option value="">-- Sélectionnez votre quartier --</option>
                                <?php foreach ($quartiers as $quartier): ?>
                                    <option value="<?php echo $quartier['id_quartier']; ?>" 
                                        <?php echo ($form_data['id_quartier'] == $quartier['id_quartier']) ? 'selected' : ''; ?>>
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

                    <a href="login.php" class="btn btn-secondary">Se connecter</a>
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
        <a href="login.php" class="nav-item">
            <div class="nav-item-icon"><i class="fa-solid fa-door-open"></i></div>
            <div>Connexion</div>
        </a>
        <a href="register.php" class="nav-item active">
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