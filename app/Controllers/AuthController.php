<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Quartier;
use PDO;
use PDOException;
use DateTime;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Service.php';
require_once __DIR__ . '/../Models/Quartier.php';

/**
 * AuthController
 * -------------------------------------------------------------
 * Pages publiques (visiteur non connecté)
 * -------------------------------------------------------------
 */
class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* =============================================================
     * LANDING PAGE
     * ============================================================= */
    public function landing(): void
    {
        // Utilisateur déjà connecté -> pas de landing page pour lui.
        if (isset($_SESSION['id_utilisateur'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $serviceModel = new Service($this->pdo);

        $fonctionnalites = [
            [
                'icone' => 'fa-address-book',
                'titre' => 'Annuaire complet',
                'texte' => "Pharmacies, hôpitaux, police, pompiers : tous les contacts classés par quartier.",
            ],
            [
                'icone' => 'fa-map-location-dot',
                'titre' => 'Carte interactive',
                'texte' => "Repère en un coup d'œil le service d'urgence le plus proche de toi.",
            ],
            [
                'icone' => 'fa-newspaper',
                'titre' => 'Actualités locales',
                'texte' => "Les informations publiées par la commune et les médias d'Antsiranana.",
            ],
            [
                'icone' => 'fa-kit-medical',
                'titre' => 'Premiers secours',
                'texte' => "Des fiches simples pour savoir quoi faire en attendant les secours.",
            ],
        ];

        // Note : La vue landing est dans Views/client/ et non Views/auth/
        $this->render('client/landing', [
            'pharmacieGarde'   => $serviceModel->getPharmacieDeGardeDuJour(),
            'policeCentrale'   => $serviceModel->getPoliceCentrale(),
            'fonctionnalites'  => $fonctionnalites,
        ]);
    }

    /* =============================================================
     * CONNEXION
     * ============================================================= */
    public function login(): void
    {
        if (isset($_SESSION['id_utilisateur'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $userModel = new User($this->pdo);
        $erreurs = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['password'] ?? '';

            if ($email === '') {
                $erreurs[] = "L'email est requis.";
            }
            if ($motDePasse === '') {
                $erreurs[] = "Le mot de passe est requis.";
            }

            if (empty($erreurs)) {
                $motDePasseHash = hash('sha256', $motDePasse);

                try {
                    $user = $userModel->trouverParEmailAvecRole($email);

                    if ($user && $motDePasseHash === $user['mot_de_passe']) {
                        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
                        $_SESSION['user_email']     = $user['email'];
                        $_SESSION['user_nom']       = $user['nom'];
                        $_SESSION['user_prenom']    = $user['prenom'];
                        $_SESSION['user_role']      = $user['nom_role'] ?? 'Utilisateur';
                        $_SESSION['user_quartier']  = $user['id_quartier'];

                        if ($_SESSION['user_role'] === 'Administrateur') {
                            header('Location: index.php?action=admin-dashboard');
                        } else {
                            header('Location: index.php?action=home');
                        }
                        exit;
                    }

                    $erreurs[] = "Email ou mot de passe incorrect.";
                } catch (PDOException $e) {
                    $erreurs[] = "Erreur serveur, veuillez réessayer plus tard.";
                }
            }
        }

        $this->render('auth/login', [
            'errors' => $erreurs,
            'email'  => $email,
        ]);
    }

    /* =============================================================
     * INSCRIPTION
     * ============================================================= */
    public function register(): void
    {
        if (isset($_SESSION['id_utilisateur'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $userModel = new User($this->pdo);
        $quartierModel = new Quartier($this->pdo);

        $erreurs = [];
        $succes = false;
        $formData = [
            'nom' => '', 'prenom' => '', 'email' => '', 'date_naissance' => '', 'id_quartier' => '',
        ];

        $quartiers = $quartierModel->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $dateNaissance = trim($_POST['date_naissance'] ?? '');
            $idQuartier = (int)($_POST['id_quartier'] ?? 0);
            $motDePasse = $_POST['password'] ?? '';
            $motDePasseConfirm = $_POST['password_confirm'] ?? '';

            $formData = [
                'nom' => $nom, 'prenom' => $prenom, 'email' => $email,
                'date_naissance' => $dateNaissance, 'id_quartier' => $idQuartier,
            ];

            if ($nom === '') $erreurs[] = "Le nom est requis.";
            if ($prenom === '') $erreurs[] = "Le prénom est requis.";
            if ($email === '') {
                $erreurs[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = "L'email n'est pas valide.";
            }
            if ($dateNaissance !== '') {
                $dateObj = DateTime::createFromFormat('Y-m-d', $dateNaissance);
                $aujourdhui = new DateTime();
                if (!$dateObj || $dateObj > $aujourdhui) {
                    $erreurs[] = "La date de naissance ne peut pas être dans le futur.";
                }
            }
            if ($motDePasse === '') {
                $erreurs[] = "Le mot de passe est requis.";
            } elseif (strlen($motDePasse) < 6) {
                $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            if ($motDePasse !== $motDePasseConfirm) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            }
            if ($idQuartier === 0) {
                $erreurs[] = "Veuillez sélectionner un quartier.";
            }

            if (empty($erreurs) && $userModel->emailExiste($email)) {
                $erreurs[] = "Cet email est déjà utilisé.";
            }

            if (empty($erreurs)) {
                try {
                    $userModel->creerVisiteur([
                        'nom'            => $nom,
                        'prenom'         => $prenom,
                        'email'          => $email,
                        'mot_de_passe'   => hash('sha256', $motDePasse),
                        'id_quartier'    => $idQuartier,
                        'date_naissance' => $dateNaissance !== '' ? $dateNaissance : null,
                    ]);

                    $succes = true;
                    $formData = ['nom' => '', 'prenom' => '', 'email' => '', 'date_naissance' => '', 'id_quartier' => ''];
                    header('refresh:2;url=index.php?action=login');
                } catch (PDOException $e) {
                    $erreurs[] = "Erreur lors de l'inscription. Veuillez réessayer.";
                }
            }
        }

        $this->render('auth/register', [
            'errors'    => $erreurs,
            'success'   => $succes,
            'formData'  => $formData,
            'quartiers' => $quartiers,
        ]);
    }

    /* =============================================================
     * DÉCONNEXION
     * ============================================================= */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();

        header('Location: index.php?action=landing');
        exit;
    }

    /* =============================================================
     * Rendu de vue
     * ============================================================= */
    private function render(string $vue, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../app/Views/' . $vue . '.php';
    }
}
