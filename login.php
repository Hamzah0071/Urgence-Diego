<?php
/**
 * Page de connexion - Urgences Antsiranana
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

// Variables
$errors = [];
$email = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    if (empty($errors)) {
        // Hash du mot de passe saisi (même méthode que SHA2(..., 256) en SQL)
        $password_hashed = hash('sha256', $password);

        try {
            $stmt = $pdo->prepare("
                SELECT u.*, r.nom_role 
                FROM utilisateur u
                LEFT JOIN role r ON u.id_role = r.id_role
                WHERE u.email = :email
            ");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password_hashed === $user['mot_de_passe']) {
                // Créer la session
                $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_role'] = $user['nom_role'] ?? 'Utilisateur';
                $_SESSION['user_quartier'] = $user['id_quartier'];

                // Rediriger selon le rôle
                if ($_SESSION['user_role'] === 'Administrateur') {
                    header("Location: admin/admin_index.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                $errors[] = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur serveur, veuillez réessayer plus tard.";
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
    <title>Connexion - Urgences Antsiranana</title>
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

        /* Zone de connexion, occupant le reste de l'écran comme un vrai "écran" du site */
        .auth-wrapper {
            min-height: calc(100vh - 90px - 70px); /* header + bottom-nav */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
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

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        input {
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

        input:focus {
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

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--blue-deep);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .auth-footer a:hover {
            color: var(--red-emergency);
        }

        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-dark);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
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
                        <h1><i class="fa-solid fa-user-plus"></i> Connexion</h1>
                        <p>Accédez à votre compte Urgences Antsiranana</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <?php foreach ($errors as $error): ?>
                            <div class="error-message">
                                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="votre@email.com" 
                                value="<?php echo htmlspecialchars($email); ?>" 
                                required
                                autocomplete="email"
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="password-wrapper">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Votre mot de passe" 
                                    required
                                    autocomplete="current-password"
                                >
                                <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </form>

                    <div class="divider">Nouveau sur la plateforme ?</div>

                    <a href="register.php" class="btn btn-secondary">Créer un compte</a>
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
        <a href="login.php" class="nav-item active">
            <div class="nav-item-icon"><i class="fa-solid fa-door-open"></i></div>
            <div>Connexion</div>
        </a>
        <a href="register.php" class="nav-item">
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
    </script>
</body>
</html>