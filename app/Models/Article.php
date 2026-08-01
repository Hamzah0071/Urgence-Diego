<?php

namespace App\Models;

use PDO;
use PDOException;

class Article
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Tous les articles publiés, pour actualites.php. */
    public function getPublies(int $limite = 50): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                a.id_article, a.titre, a.contenu, a.lien_source, a.date_publication,
                u.nom AS auteur_nom, u.prenom AS auteur_prenom, s.nom_source
            FROM article a
            LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
            LEFT JOIN sources_articles s ON a.id_source = s.id_source
            WHERE a.statut = 'publie'
            ORDER BY a.date_publication DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Les N derniers articles publiés (résumé), pour home.php. */
    public function getDernieres(int $limite = 3): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id_article, titre, contenu, lien_source, date_publication
                FROM article
                WHERE statut = 'publie'
                ORDER BY date_publication DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Alias de getDernieres(), pour coller au nom attendu par HomeController::home().
     * Garder les deux noms évite de casser d'autres pages qui appelleraient déjà
     * getDernieres() ; à terme, choisis-en un seul et supprime l'autre.
     */
    public function getArticlesRecents(int $limite = 3): array
    {
        return $this->getDernieres($limite);
    }

    /**
     * Un article publié, pour la page de détail (HomeController::articleDetail()).
     * Retourne null si l'article n'existe pas ou n'est pas publié.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                a.id_article, a.titre, a.contenu, a.lien_source, a.date_publication,
                u.nom AS auteur_nom, u.prenom AS auteur_prenom, s.nom_source
            FROM article a
            LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
            LEFT JOIN sources_articles s ON a.id_source = s.id_source
            WHERE a.id_article = :id AND a.statut = 'publie'
        ");
        $stmt->execute(['id' => $id]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    /**
     * Quelques autres articles publiés (en excluant celui affiché), pour la
     * section "Autres actualités" en bas de la page de détail.
     */
    public function getAutres(int $idExclu, int $limite = 3): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                a.id_article, a.titre, a.contenu, a.lien_source, a.date_publication, s.nom_source
            FROM article a
            LEFT JOIN sources_articles s ON a.id_source = s.id_source
            WHERE a.statut = 'publie' AND a.id_article != :id_exclu
            ORDER BY a.date_publication DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':id_exclu', $idExclu, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Historique des publications d'un rédacteur, pour new-article.php. */
    public function getParAuteur(int $idAuteur): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM article WHERE id_auteur = :id_auteur ORDER BY date_publication DESC');
        $stmt->execute(['id_auteur' => $idAuteur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Un article du rédacteur, pour le formulaire d'édition. */
    public function trouverPourEdition(int $idArticle, int $idAuteur): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM article WHERE id_article = :id AND id_auteur = :id_auteur');
        $stmt->execute(['id' => $idArticle, 'id_auteur' => $idAuteur]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    /**
     * Publication manuelle par un rédacteur (id_source = NULL, statut = brouillon
     * en attente de validation par un administrateur).
     *
     * @throws PDOException si le lien Facebook (unique) est déjà utilisé
     */
    public function creer(string $titre, string $contenu, string $lienSource, int $idAuteur): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO article (titre, contenu, lien_source, id_auteur, id_source, statut)
            VALUES (:titre, :contenu, :lien_source, :id_auteur, NULL, 'brouillon')
        ");
        $stmt->execute([
            'titre'       => $titre,
            'contenu'     => $contenu,
            'lien_source' => $lienSource,
            'id_auteur'   => $idAuteur,
        ]);
    }

    /**
     * Modification par le rédacteur : si l'article était publié, il repasse
     * en brouillon (doit être revalidé par un administrateur).
     */
    public function modifier(int $idArticle, int $idAuteur, string $titre, string $contenu, string $lienSource): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE article
            SET titre = :titre, contenu = :contenu, lien_source = :lien_source,
                statut = IF(statut = 'publie', 'brouillon', statut)
            WHERE id_article = :id AND id_auteur = :id_auteur
        ");
        $stmt->execute([
            'titre'       => $titre,
            'contenu'     => $contenu,
            'lien_source' => $lienSource,
            'id'          => $idArticle,
            'id_auteur'   => $idAuteur,
        ]);
    }

    public function supprimer(int $idArticle, int $idAuteur): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM article WHERE id_article = :id AND id_auteur = :id_auteur');
        $stmt->execute(['id' => $idArticle, 'id_auteur' => $idAuteur]);
    }

    /* -----------------------------------------------------------
     * Helpers d'affichage statiques (utilisés par les Views/Controllers,
     * pas d'accès BDD ici)
     * --------------------------------------------------------- */

    /** Extrait la première image (URL src) trouvée dans le contenu HTML d'un article. */
    public static function extraireImage(string $contenu): ?string
    {
        if (preg_match('/<img[^>]+src="([^"]+)"/i', $contenu, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Nettoie le contenu HTML d'un article pour l'affichage en texte brut
     * (retire les balises, trim). Ne tronque pas : c'est à l'appelant de
     * découper avec mb_substr() selon la longueur voulue.
     */
    public static function nettoyerContenu(string $contenu): string
    {
        return trim(strip_tags($contenu));
    }
}