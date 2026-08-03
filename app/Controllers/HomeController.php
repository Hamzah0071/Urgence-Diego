<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Service;
use App\Models\Quartier;
use App\Models\User;
use PDO;
use PDOException;

require_once __DIR__ . '/../Models/Article.php';
require_once __DIR__ . '/../Models/Service.php';
require_once __DIR__ . '/../Models/Quartier.php';
require_once __DIR__ . '/../Models/User.php';

class HomeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /* =============================================================
     * ACCUEIL
     * ============================================================= */
    public function home(): void
    {
        $serviceModel = new Service($this->pdo);
        $articleModel = new Article($this->pdo);

        $pharmaciesGarde = $serviceModel->getPharmaciesDeGardeAujourdhui();
        $urgenceServices = $serviceModel->getServicesUrgence();

        // getPublies() (au lieu de getDernieres()) fait la jointure avec
        // sources_articles, donc renvoie aussi nom_source pour le badge
        // affiché sur les cartes (même badge que sur actualites.php).
        $dernieresActus = array_map(static function (array $a) {
            $texte = Article::nettoyerContenu($a['contenu']);
            return [
                'id'          => $a['id_article'],
                'titre'       => $a['titre'],
                'image'       => Article::extraireImage($a['contenu']),
                'extrait'     => mb_substr($texte, 0, 110) . (mb_strlen($texte) > 110 ? '…' : ''),
                'lien_source' => $a['lien_source'],
                'date'        => $a['date_publication'],
                'nom_source'  => $a['nom_source'] ?? null,
            ];
        }, $articleModel->getPublies(3));

        $this->render('home', [
            'pharmaciesGarde' => $pharmaciesGarde,
            'urgenceServices' => $urgenceServices,
            'dernieresActus'  => $dernieresActus,
        ]);
    }

    /* =============================================================
     * ACTUALITÉS (liste publique)
     * ============================================================= */
    public function actualites(): void
    {
        $articleModel = new Article($this->pdo);

        $this->render('actualites', [
            'articles' => $articleModel->getPublies(50),
        ]);
    }

    /* =============================================================
     * DÉTAIL D'UN ARTICLE
     * ============================================================= */
    public function articleDetail(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->pageIntrouvable();
            return;
        }

        $articleModel = new Article($this->pdo);
        $article = $articleModel->getById($id);

        if (!$article) {
            $this->pageIntrouvable();
            return;
        }

        $this->render('article-detail', [
            'article' => $article,
            'autresArticles' => $articleModel->getAutres($id, 3),
        ]);
    }

    /* =============================================================
     * ESPACE RÉDACTEUR : publier / modifier / supprimer ses articles
     * ============================================================= */
    public function newArticle(): void
    {
        // Contrôle d'accès : uniquement le rôle Redacteur (id_role = 2)
        if ((int)($_SESSION['id_role'] ?? 0) !== 2) {
            header('Location: index.php?action=login');
            exit;
        }

        $articleModel = new Article($this->pdo);
        $idUtilisateur = (int)$_SESSION['id_utilisateur'];

        $erreur = '';
        $succes = '';
        $onglet = $_GET['onglet'] ?? 'publier';
        $articleAModifier = null;

        // --- Publier un nouvel article ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'publier') {
            [$erreur] = $this->traiterPublicationArticle($articleModel, $idUtilisateur);
        }

        // --- Modifier un article existant ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
            [$erreur, $onglet, $articleAModifier] = $this->traiterModificationArticle($articleModel, $idUtilisateur, $onglet);
        }

        // --- Passer un article en mode édition ---
        if (isset($_GET['modifier'])) {
            $trouve = $articleModel->trouverPourEdition((int)$_GET['modifier'], $idUtilisateur);
            if ($trouve) {
                $articleAModifier = $trouve;
                $onglet = 'publier';
            }
        }

        // --- Supprimer un article ---
        if (isset($_GET['supprimer'])) {
            $articleModel->supprimer((int)$_GET['supprimer'], $idUtilisateur);
            header('Location: index.php?action=new-article&onglet=historique&succes=supprime');
            exit;
        }

        // --- Message de succès (après redirection) ---
        $messages = [
            'publie'   => "Article envoyé pour validation. Il sera visible sur le site dès qu'un administrateur l'aura validé.",
            'modifie'  => "Article modifié. Il repasse en attente de validation par un administrateur.",
            'supprime' => "Article supprimé.",
        ];
        $succes = isset($_GET['succes']) ? ($messages[$_GET['succes']] ?? '') : $succes;

        $mesArticles = $onglet === 'historique' ? $articleModel->getParAuteur($idUtilisateur) : [];

        $this->render('new-article', [
            'erreur'           => $erreur,
            'succes'           => $succes,
            'onglet'           => $onglet,
            'articleAModifier' => $articleAModifier,
            'mesArticles'      => $mesArticles,
        ]);
    }

    private function traiterPublicationArticle(Article $articleModel, int $idAuteur): array
    {
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $lienSource = trim($_POST['lien_source'] ?? '');

        if ($titre === '' || $contenu === '' || $lienSource === '') {
            return ["Tous les champs sont obligatoires (titre, description et lien Facebook)."];
        }
        if (!filter_var($lienSource, FILTER_VALIDATE_URL)) {
            return ["Le lien collé n'est pas une URL valide."];
        }

        try {
            $articleModel->creer($titre, $contenu, $lienSource, $idAuteur);
            header('Location: index.php?action=new-article&onglet=historique&succes=publie');
            exit;
        } catch (PDOException $e) {
            $erreur = ($e->getCode() == 23000)
                ? "Ce lien Facebook a déjà été utilisé pour un autre article."
                : "Erreur lors de l'enregistrement de l'article.";
            return [$erreur];
        }
    }

    private function traiterModificationArticle(Article $articleModel, int $idAuteur, string $onglet): array
    {
        $idArticle = (int)($_POST['id_article'] ?? 0);
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $lienSource = trim($_POST['lien_source'] ?? '');

        $donneesResaisies = ['id_article' => $idArticle, 'titre' => $titre, 'contenu' => $contenu, 'lien_source' => $lienSource];

        if ($titre === '' || $contenu === '' || $lienSource === '') {
            return ["Tous les champs sont obligatoires.", 'publier', $donneesResaisies];
        }
        if (!filter_var($lienSource, FILTER_VALIDATE_URL)) {
            return ["Le lien collé n'est pas une URL valide.", 'publier', $donneesResaisies];
        }

        $articleModel->modifier($idArticle, $idAuteur, $titre, $contenu, $lienSource);
        header('Location: index.php?action=new-article&onglet=historique&succes=modifie');
        exit;
    }

    /* =============================================================
     * DON
     * ============================================================= */
    public function don(): void
    {
        $this->render('don', []);
    }

    /* =============================================================
     * PROFIL (utilisateur connecté)
     * ============================================================= */
    public function profile(): void
    {
        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userModel = new User($this->pdo);
        $profil = $userModel->getProfilComplet((int)$_SESSION['id_utilisateur']);

        if (!$profil) {
            die("Profil introuvable.");
        }

        $this->render('profile', ['profil' => $profil]);
    }

    /* =============================================================
     * SERVICES D'URGENCE (liste filtrable)
     * ============================================================= */
    public function serviceUrgence(): void
{
    $serviceModel = new Service($this->pdo);
    $quartierModel = new Quartier($this->pdo);

    $idQuartier = $_GET['id_quartier'] ?? null;
    $idType = $_GET['id_type'] ?? null;
    $recherche = trim($_GET['q'] ?? '');

    $data = [
        'quartiers'      => $quartierModel->all(),
        'typesServices'  => $serviceModel->getTypesService(),
        'services'       => $serviceModel->rechercher($idQuartier ? (int)$idQuartier : null, $idType ? (int)$idType : null, $recherche ?: null),
        'pharmacieGarde' => $serviceModel->getPharmacieGardeAvecQuartier(),
        'pompiers'       => $serviceModel->getPompiers(), // ← toujours tous les pompiers, jamais filtré
        'idQuartier'     => $idQuartier,
        'idType'         => $idType,
        'recherche'      => $recherche,
    ];

    $this->render('service-urgence', $data);
}

    
    /* =============================================================
     * CARTE INTERACTIVE DES URGENCES
     * ============================================================= */
    public function urgenceCarte(): void
    {
        $serviceModel = new Service($this->pdo);

        $typeToCategorie = [
            'Pharmacie'        => 'pharmacie',
            'Pompier'          => 'pompier',
            "Force de l'ordre" => 'police',
            'Hôpital'          => 'hopital',
        ];

        $services = $serviceModel->getServicesPourCarte();

        $servicesForJs = array_map(static function (array $s) use ($typeToCategorie) {
            $categorie = $typeToCategorie[$s['nom_type']] ?? 'pharmacie';
            return [
                'id'          => (string)$s['id_service'],
                'categorie'   => $categorie,
                'name'        => $s['libelle'],
                'address'     => $s['adresse'],
                'quartier'    => $s['nom_quartier'],
                'lat'         => $s['latitude'] !== null ? (float)$s['latitude'] : null,
                'lng'         => $s['longitude'] !== null ? (float)$s['longitude'] : null,
                'phone'       => (!empty($s['telephone']) && $s['telephone'] !== '000 00 000 00') ? $s['telephone'] : null,
                'description' => $s['description'],
            ];
        }, $services);

        $categories = [
            'tous'      => 'Tous',
            'pharmacie' => 'Pharmacies',
            'hopital'   => 'Hôpitaux',
            'police'    => 'Police',
            'pompier'   => 'Pompiers',
        ];

        $this->render('urgence-carte', [
            'categories'    => $categories,
            'servicesJson'  => json_encode($servicesForJs, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /* =============================================================
     * Rendu de vue : inclut le header/footer client autour de la View
     * ============================================================= */
    private function render(string $vue, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/client/' . $vue . '.php';
    }

    /* =============================================================
     * Page 404 générique
     * ============================================================= */
    private function pageIntrouvable(): void
    {
        http_response_code(404);
        echo 'Page introuvable.';
    }
}