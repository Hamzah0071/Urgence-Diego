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
    header("Location: ./client/home.php");
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
                    header("Location: ./client/home.php");
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
    
    <title>Connexion - Urgences Antsiranana</title>
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">
    <link rel="stylesheet" href="./asset/css/login.css">

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