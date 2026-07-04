<?php
/**
 * Page d'inscription - Urgences Antsiranana
 * Version simplifiée sans Google OAuth
 */

require_once 'db_connect.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: acceuil.php");
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
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email");
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
                INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_quartier, id_role, date_naissance)
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Urgences Antsiranana</title>
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
            max-width: 500px;
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
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="Confirmez votre mot de passe" 
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>

            <div class="divider">Vous avez déjà un compte ?</div>

            <a href="login.php" class="btn btn-secondary">Se connecter</a>

            <div class="auth-footer">
                <p><a href="index.php">← Retour à l'accueil</a></p>
            </div>
        </div>
    </div>

    <script>
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
                strengthDiv.textContent = '<i class="fa-solid fa-circle" style="color: rgb(250, 0, 0);"></i> Mot de passe faible';
                strengthDiv.className = 'password-strength weak';
            } else if (strength < 4) {
                strengthDiv.textContent = '<i class="fa-solid fa-circle" style="color: rgb(255, 255, 0);"></i> Mot de passe moyen';
                strengthDiv.className = 'password-strength medium';
            } else {
                strengthDiv.textContent = '<i class="fa-solid fa-circle" style="color: rgb(0, 255, 0);"></i> Mot de passe fort';
                strengthDiv.className = 'password-strength strong';
            }
        }
    </script>
</body>
</html>