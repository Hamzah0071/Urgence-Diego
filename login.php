<?php
/**
 * Page de connexion - Urgences Antsiranana
 * Version simplifiée sans Google OAuth
 */

require_once 'db_connect.php';

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
                FROM utilisateurs u
                LEFT JOIN roles r ON u.id_role = r.id_role
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Urgences Antsiranana</title>
    <link rel="stylesheet" href="./asset/icon/fontAwesome/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=JetBrains+Mono:wght@700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --blue-deep: #1e40af;
            --red-emergency: #dc2626;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --transition: all 0.3s ease;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-light) 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
        }

        .auth-card {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem 2rem;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            font-family: 'Poppins', sans-serif;
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
            background: #1e3a8a;
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
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fa-solid fa-user-plus"></i> Connexion</h1>
                <p>Accédez à votre compte Urgences Antsiranana</p>
            </div>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="error-message">
                        <i class="fa-solid fa-triangle-exclamation" style="color: rgb(230, 99, 99);"></i> <?php echo htmlspecialchars($error); ?>
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
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Votre mot de passe" 
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>

            <div class="divider">Nouveau sur la plateforme ?</div>

            <a href="register.php" class="btn btn-secondary">Créer un compte</a>

            <div class="auth-footer">
                <p><a href="index.php">← Retour à l'accueil</a></p>
            </div>
        </div>
    </div>
</body>
</html>