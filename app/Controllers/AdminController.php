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

    public function articles(): void
    {
        $erreur = '';
        $succes = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'ajouter') {
                $nom = trim($_POST['nom_source'] ?? '');
                $url = trim($_POST['url_flux'] ?? '');
                if ($nom && $url) {
                    $stmt = $this->pdo->prepare("INSERT INTO sources_articles (nom_source, url_flux, actif) VALUES (?, ?, 1)");
                    $stmt->execute([$nom, $url]);
                    $succes = "Source ajoutée avec succès.";
                }
            } elseif ($action === 'toggle') {
                $id = (int)$_POST['id_source'];
                $this->pdo->query("UPDATE sources_articles SET actif = 1 - actif WHERE id_source = $id");
                $succes = "Statut mis à jour.";
            } elseif ($action === 'supprimer') {
                $id = (int)$_POST['id_source'];
                $this->pdo->query("DELETE FROM sources_articles WHERE id_source = $id");
                $succes = "Source supprimée.";
            }
        }

        $sources = $this->pdo->query("SELECT * FROM sources_articles ORDER BY nom_source")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('admin/admin-article', [
            'sources' => $sources,
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }

    /**
     * Importation et Validation des articles
     */
    public function importArticles(): void
    {
        $messageValidation = '';
        $vue = 'validation';

        // --- TRAITEMENT VALIDATION/REFUS ---
        if (isset($_GET['valider'])) {
            $id = (int)$_GET['valider'];
            $this->pdo->prepare("UPDATE article SET statut = 'publie' WHERE id_article = ?")->execute([$id]);
            $messageValidation = "L'article a été validé et est maintenant public.";
        } elseif (isset($_GET['refuser'])) {
            $id = (int)$_GET['refuser'];
            $this->pdo->prepare("UPDATE article SET statut = 'archive' WHERE id_article = ?")->execute([$id]);
            $messageValidation = "L'article a été refusé (archivé).";
        }

        // --- TRAITEMENT IMPORT RSS (si on vient de cliquer sur le bouton) ---
        $rapport = [];
        $totalNouveaux = 0;
        $totalIgnores = 0;
        $sources = [];

        if (!isset($_GET['valider']) && !isset($_GET['refuser'])) {
            $vue = 'import';
            $sources = $this->pdo->query("SELECT * FROM sources_articles WHERE actif = 1")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sources as $source) {
                try {
                    $rss = @simplexml_load_file($source['url_flux']);
                    if (!$rss) throw new Exception("Flux inaccessible.");

                    $countSource = 0;
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
                            $countSource++;
                        } else {
                            $totalIgnores++;
                        }
                    }
                    $rapport[] = ['source' => $source['nom_source'], 'type' => 'succes', 'message' => "$countSource nouveaux articles importés."];
                } catch (Exception $e) {
                    $rapport[] = ['source' => $source['nom_source'], 'type' => 'erreur', 'message' => $e->getMessage()];
                }
            }
        }

        // Articles rédacteurs en attente
        $articlesAValider = $this->pdo->query("
            SELECT a.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom 
            FROM article a 
            LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
            WHERE a.statut = 'brouillon' 
            ORDER BY a.date_publication DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('admin/import-article', [
            'vue' => $vue,
            'rapport' => $rapport,
            'totalNouveaux' => $totalNouveaux,
            'totalIgnores' => $totalIgnores,
            'sources' => $sources,
            'articlesAValider' => $articlesAValider,
            'messageValidation' => $messageValidation
        ]);
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
                $id = (int)$_POST['id_service'];
                $this->pdo->query("UPDATE service SET actif = 1 - actif WHERE id_service = $id");
                $succes = "Statut mis à jour.";

            } elseif ($action === 'supprimer') {
                $id = (int)$_POST['id_service'];
                $this->pdo->query("DELETE FROM service WHERE id_service = $id");
                $succes = "Service supprimé.";
            }
        }

        $services = $idTypeActif ? $this->pdo->query("
            SELECT s.*, q.nom_quartier 
            FROM service s 
            LEFT JOIN quartier q ON s.id_quartier = q.id_quartier 
            WHERE s.id_type = $idTypeActif 
            ORDER BY s.libelle
        ")->fetchAll(PDO::FETCH_ASSOC) : [];

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

    private function render(string $vue, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/includes/header-admin.php';
        require __DIR__ . '/../Views/' . $vue . '.php';
        require __DIR__ . '/../Views/includes/footer-admin.php';
    }
}