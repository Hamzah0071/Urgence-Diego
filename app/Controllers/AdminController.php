<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Service.php';
require_once __DIR__ . '/../Models/Article.php';
require_once __DIR__ . '/../Models/Quartier.php';
require_once __DIR__ . '/../Models/Role.php';

/**
 * AdminController
 * -------------------------------------------------------------
 * Regroupe toutes les pages de l'espace admin :
 *  - dashboard()       -> app/Views/admin/dashboard.php
 *  - articles()        -> app/Views/admin/admin-article.php
 *  - services()        -> app/Views/admin/admin-service.php
 *  - utilisateurs()     -> app/Views/admin/admin-utilisateur.php
 *  - importArticles()  -> app/Views/admin/import-article.php
 * -------------------------------------------------------------
 */
class AdminController
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo; // instance PDO créée dans config/database.php, incluse par public/index.php

        $this->pdo = $pdo;

        // Vérifie la session + le droit d'accès admin.
        // Fonction attendue dans app/includes/session.php (ex admin_session.php).
        verifierAccesAdmin();
    }

    /* =============================================================
     * TABLEAU DE BORD
     * ============================================================= */
    public function dashboard(): void
    {
        $serviceModel = new Service($this->pdo);
        $today = date('Y-m-d');

        $pharmaciesGarde = $serviceModel->getPharmaciesDeGarde($today);

        $data = [
            'num_pharmacies_garde' => count($pharmaciesGarde),
            'pharmacies_garde'     => $pharmaciesGarde,
            'num_services_urgence' => $serviceModel->countServicesActifs(),
            'recent_updates'       => $serviceModel->getMisesAJourRecentes($today, 5),
        ];

        $this->render('dashboard', $data);
    }

    /* =============================================================
     * SOURCES D'ARTICLES (RSS / réseaux sociaux)
     * ============================================================= */
    public function articles(): void
    {
        $articleModel = new Article($this->pdo);
        $articleModel->ensureTable();

        $erreur = '';
        $succes = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'ajouter':
                    [$erreur, $succes] = $this->traiterAjoutSource($articleModel);
                    break;
                case 'modifier':
                    if (isset($_POST['id_source'])) {
                        [$erreur, $succes] = $this->traiterModificationSource($articleModel);
                    }
                    break;
                case 'supprimer':
                    if (isset($_POST['id_source'])) {
                        $articleModel->supprimerSource((int)$_POST['id_source']);
                        $succes = "Source supprimée.";
                    }
                    break;
                case 'toggle':
                    if (isset($_POST['id_source'])) {
                        $articleModel->toggleSource((int)$_POST['id_source']);
                        $succes = "Statut mis à jour.";
                    }
                    break;
            }
        }

        $data = [
            'erreur'  => $erreur,
            'succes'  => $succes,
            'sources' => $articleModel->getSources(),
        ];

        $this->render('admin-article', $data);
    }

    private function traiterAjoutSource(Article $articleModel): array
    {
        $nom = trim($_POST['nom_source'] ?? '');
        $url = trim($_POST['url_flux'] ?? '');

        if ($nom === '' || $url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return ["Merci de renseigner un nom et une URL de flux valide.", ''];
        }

        $articleModel->ajouterSource($nom, $url);
        return ['', "Source ajoutée avec succès."];
    }

    private function traiterModificationSource(Article $articleModel): array
    {
        $nom = trim($_POST['nom_source'] ?? '');
        $url = trim($_POST['url_flux'] ?? '');

        if ($nom === '' || $url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return ["Merci de renseigner un nom et une URL valide pour la modification.", ''];
        }

        $articleModel->modifierSource((int)$_POST['id_source'], $nom, $url);
        return ['', "Source modifiée avec succès."];
    }

    /* =============================================================
     * SERVICES D'URGENCE (pharmacies, pompiers, police, hôpitaux)
     * ============================================================= */
    public function services(): void
    {
        $serviceModel  = new Service($this->pdo);
        $quartierModel = new Quartier($this->pdo);

        $erreur = '';
        $succes = '';

        $types = $serviceModel->getTypes();
        if (empty($types)) {
            die("Aucun type de service configuré dans la table type_service.");
        }

        $idTypeActif = isset($_GET['type']) ? (int)$_GET['type'] : (int)$types[0]['id_type'];
        if (!$this->existeDansListe($types, 'id_type', $idTypeActif)) {
            $idTypeActif = (int)$types[0]['id_type'];
        }

        $quartiers = $quartierModel->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'ajouter':
                    [$erreur, $succes, $idTypeActif] = $this->traiterAjoutService($serviceModel, $idTypeActif);
                    break;
                case 'modifier':
                    if (isset($_POST['id_service'])) {
                        [$erreur, $succes] = $this->traiterModificationService($serviceModel);
                    }
                    break;
                case 'supprimer':
                    if (isset($_POST['id_service'])) {
                        $serviceModel->supprimer((int)$_POST['id_service']);
                        $succes = "Service supprimé.";
                    }
                    break;
                case 'toggle':
                    if (isset($_POST['id_service'])) {
                        $serviceModel->toggle((int)$_POST['id_service']);
                        $succes = "Statut mis à jour.";
                    }
                    break;
            }
        }

        $data = [
            'erreur'       => $erreur,
            'succes'       => $succes,
            'types'        => $types,
            'idTypeActif'  => $idTypeActif,
            'quartiers'    => $quartiers,
            'services'     => $serviceModel->getServicesByType($idTypeActif),
            'iconesType'   => [
                'Pharmacie'        => 'fa-pills',
                'Pompier'          => 'fa-fire-extinguisher',
                "Force de l'ordre" => 'fa-user-shield',
                'Hôpital'          => 'fa-hospital',
            ],
            'classesType'  => [
                'Pharmacie'        => 'onglet-pharmacie',
                'Pompier'          => 'onglet-pompier',
                "Force de l'ordre" => 'onglet-police',
                'Hôpital'          => 'onglet-hopital',
            ],
        ];

        $this->render('admin-service', $data);
    }

    private function traiterAjoutService(Service $serviceModel, int $idTypeActif): array
    {
        $libelle     = trim($_POST['libelle'] ?? '');
        $telephone   = trim($_POST['telephone'] ?? '');
        $adresse     = trim($_POST['adresse'] ?? '');
        $idQuartier  = (int)($_POST['id_quartier'] ?? 0);
        $idType      = (int)($_POST['id_type'] ?? $idTypeActif);
        $description = trim($_POST['description'] ?? '');

        if ($libelle === '' || $telephone === '' || $adresse === '' || $idQuartier === 0) {
            return ["Merci de renseigner le nom, le téléphone, l'adresse et le quartier.", '', $idTypeActif];
        }

        $serviceModel->ajouter([
            'libelle'     => $libelle,
            'telephone'   => $telephone,
            'adresse'     => $adresse,
            'id_quartier' => $idQuartier,
            'id_type'     => $idType,
            'description' => $description !== '' ? $description : null,
        ]);

        // On reste sur le bon onglet après l'ajout.
        return ['', "Service ajouté avec succès.", $idType];
    }

    private function traiterModificationService(Service $serviceModel): array
    {
        $libelle     = trim($_POST['libelle'] ?? '');
        $telephone   = trim($_POST['telephone'] ?? '');
        $adresse     = trim($_POST['adresse'] ?? '');
        $idQuartier  = (int)($_POST['id_quartier'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($libelle === '' || $telephone === '' || $adresse === '' || $idQuartier === 0) {
            return ["Merci de renseigner le nom, le téléphone, l'adresse et le quartier pour la modification.", ''];
        }

        $serviceModel->modifier([
            'libelle'     => $libelle,
            'telephone'   => $telephone,
            'adresse'     => $adresse,
            'id_quartier' => $idQuartier,
            'description' => $description !== '' ? $description : null,
            'id'          => (int)$_POST['id_service'],
        ]);

        return ['', "Service modifié avec succès."];
    }

    /* =============================================================
     * UTILISATEURS
     * ============================================================= */
    public function utilisateurs(): void
    {
        $userModel     = new User($this->pdo);
        $roleModel     = new Role($this->pdo);
        $quartierModel = new Quartier($this->pdo);

        $erreur = '';
        $succes = '';

        $roles = $roleModel->all();
        if (empty($roles)) {
            die("Aucun rôle configuré dans la table role.");
        }

        $idRoleActif = isset($_GET['role']) ? (int)$_GET['role'] : (int)$roles[0]['id_role'];
        if (!$this->existeDansListe($roles, 'id_role', $idRoleActif)) {
            $idRoleActif = (int)$roles[0]['id_role'];
        }

        $quartiers = $quartierModel->all();
        $idUtilisateurConnecte = $_SESSION['id_utilisateur'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'ajouter':
                    [$erreur, $succes, $idRoleActif] = $this->traiterAjoutUtilisateur($userModel, $idRoleActif);
                    break;
                case 'modifier':
                    if (isset($_POST['id_utilisateur'])) {
                        [$erreur, $succes] = $this->traiterModificationUtilisateur($userModel);
                    }
                    break;
                case 'supprimer':
                    if (isset($_POST['id_utilisateur'])) {
                        [$erreur, $succes] = $this->traiterSuppressionUtilisateur($userModel, $idUtilisateurConnecte);
                    }
                    break;
                case 'toggle':
                    if (isset($_POST['id_utilisateur'])) {
                        [$erreur, $succes] = $this->traiterToggleUtilisateur($userModel, $idUtilisateurConnecte);
                    }
                    break;
                case 'changer_role':
                    if (isset($_POST['id_utilisateur'], $_POST['nouveau_role'])) {
                        $userModel->changerRole((int)$_POST['id_utilisateur'], (int)$_POST['nouveau_role']);
                        $succes = "Rôle mis à jour.";
                    }
                    break;
            }
        }

        $data = [
            'erreur'                => $erreur,
            'succes'                => $succes,
            'roles'                 => $roles,
            'idRoleActif'           => $idRoleActif,
            'quartiers'             => $quartiers,
            'utilisateurs'          => $userModel->getByRole($idRoleActif),
            'idUtilisateurConnecte' => $idUtilisateurConnecte,
            'iconesRole'            => [
                'Administrateur' => 'fa-user-shield',
                'Redacteur'      => 'fa-pen-nib',
                'Visiteur'       => 'fa-user',
            ],
            'classesRole'           => [
                'Administrateur' => 'onglet-admin',
                'Redacteur'      => 'onglet-redacteur',
                'Visiteur'       => 'onglet-visiteur',
            ],
        ];

        $this->render('admin-utilisateur', $data);
    }

    private function traiterAjoutUtilisateur(User $userModel, int $idRoleActif): array
    {
        $nom           = trim($_POST['nom'] ?? '');
        $prenom        = trim($_POST['prenom'] ?? '');
        $dateNaissance = trim($_POST['date_naissance'] ?? '');
        $idQuartier    = (int)($_POST['id_quartier'] ?? 0);
        $email         = trim($_POST['email'] ?? '');
        $motDePasse    = trim($_POST['mot_de_passe'] ?? '');
        $idRole        = (int)($_POST['id_role'] ?? $idRoleActif);

        if ($nom === '' || $prenom === '' || $email === '' || $motDePasse === '') {
            return ["Merci de renseigner le nom, le prénom, l'email et le mot de passe.", '', $idRoleActif];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["L'adresse email n'est pas valide.", '', $idRoleActif];
        }
        if ($userModel->emailExiste($email)) {
            return ["Cet email est déjà utilisé par un autre utilisateur.", '', $idRoleActif];
        }

        $userModel->ajouter([
            'nom'            => $nom,
            'prenom'         => $prenom,
            'date_naissance' => $dateNaissance !== '' ? $dateNaissance : null,
            'id_quartier'    => $idQuartier > 0 ? $idQuartier : null,
            'email'          => $email,
            'mot_de_passe'   => hash('sha256', $motDePasse),
            'id_role'        => $idRole,
        ]);

        // On reste sur le bon onglet après l'ajout.
        return ['', "Utilisateur ajouté avec succès.", $idRole];
    }

    private function traiterModificationUtilisateur(User $userModel): array
    {
        $idUtilisateur = (int)$_POST['id_utilisateur'];
        $nom           = trim($_POST['nom'] ?? '');
        $prenom        = trim($_POST['prenom'] ?? '');
        $dateNaissance = trim($_POST['date_naissance'] ?? '');
        $idQuartier    = (int)($_POST['id_quartier'] ?? 0);
        $email         = trim($_POST['email'] ?? '');
        $motDePasse    = trim($_POST['mot_de_passe'] ?? '');

        if ($nom === '' || $prenom === '' || $email === '') {
            return ["Merci de renseigner le nom, le prénom et l'email pour la modification.", ''];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["L'adresse email n'est pas valide.", ''];
        }
        if ($userModel->emailExiste($email, $idUtilisateur)) {
            return ["Cet email est déjà utilisé par un autre utilisateur.", ''];
        }

        $userModel->modifier([
            'id'             => $idUtilisateur,
            'nom'            => $nom,
            'prenom'         => $prenom,
            'date_naissance' => $dateNaissance !== '' ? $dateNaissance : null,
            'id_quartier'    => $idQuartier > 0 ? $idQuartier : null,
            'email'          => $email,
            'mot_de_passe'   => $motDePasse !== '' ? hash('sha256', $motDePasse) : null,
        ]);

        return ['', "Utilisateur modifié avec succès."];
    }

    private function traiterSuppressionUtilisateur(User $userModel, ?int $idUtilisateurConnecte): array
    {
        $idUtilisateur = (int)$_POST['id_utilisateur'];

        if ($idUtilisateurConnecte !== null && $idUtilisateur === $idUtilisateurConnecte) {
            return ["Vous ne pouvez pas supprimer votre propre compte.", ''];
        }

        $userModel->supprimer($idUtilisateur);
        return ['', "Utilisateur supprimé."];
    }

    private function traiterToggleUtilisateur(User $userModel, ?int $idUtilisateurConnecte): array
    {
        $idUtilisateur = (int)$_POST['id_utilisateur'];

        if ($idUtilisateurConnecte !== null && $idUtilisateur === $idUtilisateurConnecte) {
            return ["Vous ne pouvez pas désactiver votre propre compte.", ''];
        }

        $userModel->toggle($idUtilisateur);
        return ['', "Statut mis à jour."];
    }

    /* =============================================================
     * IMPORT & VALIDATION DES ARTICLES
     * ============================================================= */
    public function importArticles(): void
    {
        $articleModel = new Article($this->pdo);

        // Actions de validation/refus -> redirection immédiate (comme à l'origine)
        if (isset($_GET['valider'])) {
            $articleModel->validerArticle((int)$_GET['valider']);
            header('Location: index.php?action=admin-import-articles&vue=validation&succes=valide');
            exit;
        }
        if (isset($_GET['refuser'])) {
            $articleModel->refuserArticle((int)$_GET['refuser']);
            header('Location: index.php?action=admin-import-articles&vue=validation&succes=refuse');
            exit;
        }

        $vue = $_GET['vue'] ?? 'import';

        $sources        = [];
        $totalNouveaux  = 0;
        $totalIgnores   = 0;
        $rapport        = [];

        if ($vue === 'import') {
            $sources = $articleModel->getSourcesActives();

            foreach ($sources as $source) {
                $xmlBrut = Article::telechargerFlux($source['url_flux']);

                if ($xmlBrut === false || $xmlBrut === '') {
                    $rapport[] = ['type' => 'erreur', 'source' => $source['nom_source'], 'message' => "Impossible de télécharger le flux."];
                    continue;
                }

                $articlesFlux = Article::parserFlux($xmlBrut);

                if (empty($articlesFlux)) {
                    $rapport[] = ['type' => 'avertissement', 'source' => $source['nom_source'], 'message' => "Flux téléchargé mais aucun article détecté (format non reconnu ?)."];
                    continue;
                }

                $nouveauxPourCetteSource = 0;

                foreach ($articlesFlux as $art) {
                    if ($art['titre'] === '' || $art['lien'] === '') {
                        continue;
                    }

                    if ($articleModel->insererArticle($art, $source['id_source'])) {
                        $nouveauxPourCetteSource++;
                        $totalNouveaux++;
                    } else {
                        $totalIgnores++;
                    }
                }

                $rapport[] = [
                    'type'       => 'succes',
                    'source'     => $source['nom_source'],
                    'message'    => "$nouveauxPourCetteSource nouvel(aux) article(s) ajouté(s).",
                    'nb_nouveaux' => $nouveauxPourCetteSource,
                ];
            }
        }

        $messageValidation = '';
        if (isset($_GET['succes'])) {
            $messages = [
                'valide' => "Article validé : il est maintenant visible sur le site public.",
                'refuse' => "Article refusé : il reste invisible du public.",
            ];
            $messageValidation = $messages[$_GET['succes']] ?? '';
        }

        $data = [
            'vue'                => $vue,
            'sources'            => $sources,
            'totalNouveaux'      => $totalNouveaux,
            'totalIgnores'       => $totalIgnores,
            'rapport'            => $rapport,
            'articlesAValider'   => $articleModel->getArticlesAValider(),
            'messageValidation'  => $messageValidation,
        ];

        // Cette vue a son propre <html>/<head> : pas de header/footer admin autour.
        $this->render('import-article', $data, false);
    }

    /* =============================================================
     * Utilitaires internes
     * ============================================================= */

    /** Vérifie qu'un id est bien présent dans une liste de lignes (ex: types, roles). */
    private function existeDansListe(array $liste, string $cle, int $valeur): bool
    {
        foreach ($liste as $ligne) {
            if ((int)$ligne[$cle] === $valeur) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extrait les données puis inclut la vue demandée, entourée
     * (par défaut) du header/footer admin.
     */
    private function render(string $vue, array $data = [], bool $avecLayout = true): void
    {
        extract($data);

        if ($avecLayout) {
            require __DIR__ . '/../Views/includes/header-admin.php';
        }

        require __DIR__ . '/../Views/admin/' . $vue . '.php';

        if ($avecLayout) {
            require __DIR__ . '/../Views/includes/footer-admin.php';
        }
    }
}
