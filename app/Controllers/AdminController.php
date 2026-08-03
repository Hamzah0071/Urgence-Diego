<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use App\Models\Quartier;
use App\Models\Role;
use PDO;
use SimpleXMLElement;
use Exception;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Service.php';
require_once __DIR__ . '/../Models/Article.php';
require_once __DIR__ . '/../Models/Quartier.php';
require_once __DIR__ . '/../Models/Role.php';

/**
 * AdminController
 * -------------------------------------------------------------
 * Gestion complète de l'espace administration
 * -------------------------------------------------------------
 */
class AdminController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['user_role'] ?? '') !== 'Administrateur') {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public function dashboard(): void
    {
        $stats = [
            'articles_total'   => $this->pdo->query("SELECT COUNT(*) FROM article")->fetchColumn(),
            'articles_attente' => $this->pdo->query("SELECT COUNT(*) FROM article WHERE statut = 'brouillon'")->fetchColumn(),
            'services_total'   => $this->pdo->query("SELECT COUNT(*) FROM service")->fetchColumn(),
            'users_total'      => $this->pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn(),
        ];

        $articlesAttente = $this->pdo->query("
            SELECT a.*, u.nom, u.prenom 
            FROM article a 
            LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
            WHERE a.statut = 'brouillon' 
            ORDER BY a.date_publication DESC 
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('admin/dashboard', [
            'stats' => $stats,
            'articlesAttente' => $articlesAttente
        ]);
    }

    /**
     * Page "Gestion des Sources d'Articles" (admin-article.php).
     * Affiche : formulaire d'ajout de source, liste des sources,
     * ET les articles rédacteurs en attente de validation (directement ici).
     */
    public function articles(): void
    {
        $erreur = '';
        $succes = '';
        $messageValidation = '';

        // --- Valider / Refuser un article rédacteur, directement depuis cette page ---
        if (isset($_GET['valider'])) {
            $id = (int)$_GET['valider'];
            $this->pdo->prepare("UPDATE article SET statut = 'publie' WHERE id_article = ?")->execute([$id]);
            $messageValidation = "L'article a été validé et est maintenant public.";
        } elseif (isset($_GET['refuser'])) {
            $id = (int)$_GET['refuser'];
            $this->pdo->prepare("UPDATE article SET statut = 'archive' WHERE id_article = ?")->execute([$id]);
            $messageValidation = "L'article a été refusé (archivé).";
        }

        // --- Gestion des sources (formulaire POST) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'ajouter') {
                $nom = trim($_POST['nom_source'] ?? '');
                $url = trim($_POST['url_flux'] ?? '');
                if ($nom && $url) {
                    $stmt = $this->pdo->prepare("INSERT INTO sources_articles (nom_source, url_flux, actif) VALUES (?, ?, 1)");
                    $stmt->execute([$nom, $url]);
                    $succes = "Source ajoutée avec succès.";
                } else {
                    $erreur = "Le nom et l'URL du flux sont obligatoires.";
                }
            } elseif ($action === 'toggle') {
                $id = (int)($_POST['id_source'] ?? 0);
                $stmt = $this->pdo->prepare("UPDATE sources_articles SET actif = 1 - actif WHERE id_source = ?");
                $stmt->execute([$id]);
                $succes = "Statut mis à jour.";
            } elseif ($action === 'supprimer') {
                $id = (int)($_POST['id_source'] ?? 0);
                $stmt = $this->pdo->prepare("DELETE FROM sources_articles WHERE id_source = ?");
                $stmt->execute([$id]);
                $succes = "Source supprimée.";
            }
        }

        $sources = $this->pdo->query("SELECT * FROM sources_articles ORDER BY nom_source")->fetchAll(PDO::FETCH_ASSOC);

        // --- Articles rédacteurs en attente de validation, affichés directement ici ---
        $articlesAValider = $this->pdo->query("
            SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom 
            FROM article a 
            LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
            WHERE a.statut = 'brouillon' 
            ORDER BY a.date_publication DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('admin/admin-article', [
            'sources' => $sources,
            'articlesAValider' => $articlesAValider,
            'erreur' => $erreur,
            'succes' => $succes,
            'messageValidation' => $messageValidation
        ]);
    }

    /**
     * Déclenchée par le bouton "Actualiser les articles maintenant".
     * Lance l'import RSS immédiatement, stocke le résultat en session,
     * puis redirige automatiquement vers admin-articles (pas de vue séparée).
     */
    public function importArticles(): void
    {
        $sources = $this->pdo->query("SELECT * FROM sources_articles WHERE actif = 1")->fetchAll(PDO::FETCH_ASSOC);

        $totalNouveaux = 0;
        $erreurs = [];

        foreach ($sources as $source) {
            try {
                $rss = @simplexml_load_file($source['url_flux']);
                if (!$rss || !isset($rss->channel->item)) {
                    throw new Exception("Flux inaccessible : " . $source['nom_source']);
                }

                foreach ($rss->channel->item as $item) {
                    $link = (string)$item->link;
                    $check = $this->pdo->prepare("SELECT id_article FROM article WHERE lien_source = ?");
                    $check->execute([$link]);

                    if (!$check->fetch()) {
                        $stmt = $this->pdo->prepare("
                            INSERT INTO article (titre, contenu, lien_source, id_source, statut, date_publication) 
                            VALUES (?, ?, ?, ?, 'publie', NOW())
                        ");
                        $stmt->execute([
                            (string)$item->title,
                            (string)$item->description,
                            $link,
                            $source['id_source']
                        ]);
                        $totalNouveaux++;
                    }
                }
            } catch (Exception $e) {
                $erreurs[] = $source['nom_source'] . ' : ' . $e->getMessage();
            }
        }

        // Résultat transmis via session pour être affiché sur admin-articles
        $_SESSION['import_result'] = [
            'count'  => $totalNouveaux,
            'errors' => $erreurs
        ];

        header('Location: index.php?action=admin-articles&import_done=1');
        exit;
    }

    public function services(): void
    {
        $serviceModel = new Service($this->pdo);
        $quartierModel = new Quartier($this->pdo);

        $erreur = '';
        $succes = '';

        $types = $this->pdo->query("SELECT * FROM type_service ORDER BY nom_type")->fetchAll(PDO::FETCH_ASSOC);
        $idTypeActif = isset($_GET['type']) ? (int)$_GET['type'] : (int)($types[0]['id_type'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'ajouter') {
                $stmt = $this->pdo->prepare("INSERT INTO service (libelle, telephone, adresse, id_quartier, id_type, description, actif) VALUES (?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([
                    $_POST['libelle'], $_POST['telephone'], $_POST['adresse'],
                    $_POST['id_quartier'], $_POST['id_type'], $_POST['description']
                ]);
                $succes = "Service ajouté avec succès.";

            } elseif ($action === 'modifier') {
                $id = (int)($_POST['id_service'] ?? 0);
                $libelle = trim($_POST['libelle'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');
                $adresse = trim($_POST['adresse'] ?? '');
                $idQuartier = (int)($_POST['id_quartier'] ?? 0);
                $description = trim($_POST['description'] ?? '');

                if ($id > 0 && $libelle !== '' && $telephone !== '' && $adresse !== '' && $idQuartier > 0) {
                    $stmt = $this->pdo->prepare("
                        UPDATE service 
                        SET libelle = ?, telephone = ?, adresse = ?, id_quartier = ?, description = ?
                        WHERE id_service = ?
                    ");
                    $stmt->execute([$libelle, $telephone, $adresse, $idQuartier, $description ?: null, $id]);
                    $succes = "Service modifié avec succès.";
                } else {
                    $erreur = "Tous les champs obligatoires doivent être remplis (nom, téléphone, adresse, quartier).";
                }

            } elseif ($action === 'toggle') {
                $id = (int)($_POST['id_service'] ?? 0);
                $stmt = $this->pdo->prepare("UPDATE service SET actif = 1 - actif WHERE id_service = ?");
                $stmt->execute([$id]);
                $succes = "Statut mis à jour.";

            } elseif ($action === 'supprimer') {
                $id = (int)($_POST['id_service'] ?? 0);
                $stmt = $this->pdo->prepare("DELETE FROM service WHERE id_service = ?");
                $stmt->execute([$id]);
                $succes = "Service supprimé.";
            }
        }

        $services = [];
        if ($idTypeActif) {
            $stmt = $this->pdo->prepare("
                SELECT s.*, q.nom_quartier 
                FROM service s 
                LEFT JOIN quartier q ON s.id_quartier = q.id_quartier 
                WHERE s.id_type = ? 
                ORDER BY s.libelle
            ");
            $stmt->execute([$idTypeActif]);
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->render('admin/admin-service', [
            'types' => $types,
            'idTypeActif' => $idTypeActif,
            'quartiers' => $quartierModel->all(),
            'services' => $services,
            'erreur' => $erreur,
            'succes' => $succes,
            'iconesType' => [
                'Pharmacie' => 'fa-pills',
                'Pompier' => 'fa-fire-extinguisher',
                "Force de l'ordre" => 'fa-user-shield',
                'Hôpital' => 'fa-hospital'
            ],
            'classesType' => [
                'Pharmacie' => 'onglet-pharmacie',
                'Pompier' => 'onglet-pompier',
                "Force de l'ordre" => 'onglet-police',
                'Hôpital' => 'onglet-hopital'
            ]
        ]);
    }

    /**
     * Page "Gestion des Utilisateurs" (admin-utilisateur.php).
     */
    public function utilisateurs(): void
    {
        $quartierModel = new Quartier($this->pdo);

        $erreur = '';
        $succes = '';

        $roles = $this->pdo->query("SELECT * FROM role ORDER BY id_role")->fetchAll(PDO::FETCH_ASSOC);
        $idRoleActif = isset($_GET['role']) ? (int)$_GET['role'] : (int)($roles[0]['id_role'] ?? 0);

        $idUtilisateurConnecte = $_SESSION['id_utilisateur'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'ajouter') {
                $nom = trim($_POST['nom'] ?? '');
                $prenom = trim($_POST['prenom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $motDePasse = $_POST['mot_de_passe'] ?? '';
                $dateNaissance = $_POST['date_naissance'] ?: null;
                $idQuartier = $_POST['id_quartier'] ?: null;
                $idRole = (int)($_POST['id_role'] ?? $idRoleActif);

                if ($nom && $prenom && $email && $motDePasse) {
                    $check = $this->pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
                    $check->execute([$email]);

                    if ($check->fetch()) {
                        $erreur = "Un utilisateur avec cet email existe déjà.";
                    } else {
                        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
                        $stmt = $this->pdo->prepare("
                            INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, date_naissance, id_quartier, id_role, actif)
                            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                        ");
                        $stmt->execute([$nom, $prenom, $email, $hash, $dateNaissance, $idQuartier ?: null, $idRole]);
                        $succes = "Utilisateur ajouté avec succès.";
                    }
                } else {
                    $erreur = "Nom, prénom, email et mot de passe sont obligatoires.";
                }

            } elseif ($action === 'modifier') {
                $id = (int)($_POST['id_utilisateur'] ?? 0);
                $nom = trim($_POST['nom'] ?? '');
                $prenom = trim($_POST['prenom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $dateNaissance = $_POST['date_naissance'] ?: null;
                $idQuartier = $_POST['id_quartier'] ?: null;
                $motDePasse = $_POST['mot_de_passe'] ?? '';

                if ($id > 0 && $nom !== '' && $prenom !== '' && $email !== '') {
                    if ($motDePasse !== '') {
                        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
                        $stmt = $this->pdo->prepare("
                            UPDATE utilisateur
                            SET nom = ?, prenom = ?, email = ?, date_naissance = ?, id_quartier = ?, mot_de_passe = ?
                            WHERE id_utilisateur = ?
                        ");
                        $stmt->execute([$nom, $prenom, $email, $dateNaissance, $idQuartier ?: null, $hash, $id]);
                    } else {
                        $stmt = $this->pdo->prepare("
                            UPDATE utilisateur
                            SET nom = ?, prenom = ?, email = ?, date_naissance = ?, id_quartier = ?
                            WHERE id_utilisateur = ?
                        ");
                        $stmt->execute([$nom, $prenom, $email, $dateNaissance, $idQuartier ?: null, $id]);
                    }
                    $succes = "Utilisateur modifié avec succès.";
                } else {
                    $erreur = "Nom, prénom et email sont obligatoires.";
                }

            } elseif ($action === 'toggle') {
                $id = (int)($_POST['id_utilisateur'] ?? 0);
                if ($id === (int)$idUtilisateurConnecte) {
                    $erreur = "Vous ne pouvez pas désactiver votre propre compte.";
                } else {
                    $stmt = $this->pdo->prepare("UPDATE utilisateur SET actif = 1 - actif WHERE id_utilisateur = ?");
                    $stmt->execute([$id]);
                    $succes = "Statut mis à jour.";
                }

            } elseif ($action === 'supprimer') {
                $id = (int)($_POST['id_utilisateur'] ?? 0);
                if ($id === (int)$idUtilisateurConnecte) {
                    $erreur = "Vous ne pouvez pas supprimer votre propre compte.";
                } else {
                    $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
                    $stmt->execute([$id]);
                    $succes = "Utilisateur supprimé.";
                }

            } elseif ($action === 'changer_role') {
    $id = (int)($_POST['id_utilisateur'] ?? 0);
    $nouveauRole = (int)($_POST['nouveau_role'] ?? 0);

    // Retrouver l'id du rôle "Administrateur"
    $idRoleAdmin = 0;
    foreach ($roles as $r) {
        if ($r['nom_role'] === 'Administrateur') {
            $idRoleAdmin = (int)$r['id_role'];
            break;
        }
    }

    // Rôle actuel de l'utilisateur ciblé (avant modification)
    $stmtCheck = $this->pdo->prepare("SELECT id_role FROM utilisateur WHERE id_utilisateur = ?");
    $stmtCheck->execute([$id]);
    $roleActuel = (int)($stmtCheck->fetchColumn() ?: 0);

    if ($roleActuel === $idRoleAdmin && $nouveauRole !== $idRoleAdmin) {
        // Un administrateur ne peut jamais être rétrogradé en client/rédacteur
        $erreur = "Impossible de changer le rôle d'un administrateur en client ou rédacteur.";
    } elseif ($id > 0 && $nouveauRole > 0) {
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET id_role = ? WHERE id_utilisateur = ?");
        $stmt->execute([$nouveauRole, $id]);
        $succes = "Rôle mis à jour.";
    }
}
        }

        $stmt = $this->pdo->prepare("
            SELECT u.*, q.nom_quartier
            FROM utilisateur u
            LEFT JOIN quartier q ON u.id_quartier = q.id_quartier
            WHERE u.id_role = ?
            ORDER BY u.nom, u.prenom
        ");
        $stmt->execute([$idRoleActif]);
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('admin/admin-utilisateur', [
    'roles' => $roles,
    'idRoleActif' => $idRoleActif,
    'quartiers' => $quartierModel->all(),
    'utilisateurs' => $utilisateurs,
    'erreur' => $erreur,
    'succes' => $succes,
    'idUtilisateurConnecte' => $idUtilisateurConnecte,
    'rolesParId' => array_column($roles, 'nom_role', 'id_role'), // ← ajouté
    'iconesRole' => [
        'Administrateur' => 'fa-user-shield',
        'Rédacteur'      => 'fa-pen',
        'Visiteur'       => 'fa-user'
    ],
    'classesRole' => [
        'Administrateur' => 'onglet-admin',
        'Rédacteur'      => 'onglet-redacteur',
        'Visiteur'       => 'onglet-visiteur'
    ]
]);
    }

    private function render(string $vue, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/includes/header-admin.php';
        require __DIR__ . '/../Views/' . $vue . '.php';
        require __DIR__ . '/../Views/includes/footer-admin.php';
    }
}