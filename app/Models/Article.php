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
     * Récupère les derniers articles publiés avec infos source + auteur
     * (RSS et Rédacteurs), pour les pages ayant besoin de ces jointures.
     */
    public function getArticlesRecents(int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, s.nom_source, u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM article a
                LEFT JOIN sources_articles s ON a.id_source = s.id_source
                LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
                WHERE a.statut = 'publie'
                ORDER BY a.date_publication DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /** Récupère un article par son ID (avec source + auteur). */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, s.nom_source, u.nom AS auteur_nom, u.prenom AS auteur_prenom
            FROM article a
            LEFT JOIN sources_articles s ON a.id_source = s.id_source
            LEFT JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
            WHERE a.id_article = :id
        ");
        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        return $article ?: null;
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

    /**
     * Extrait la première image du contenu HTML ou renvoie une image par défaut.
     */
    public static function extraireImage(?string $contenu): string
    {
        if (empty($contenu)) {
            return 'asset/img/default-news.jpg';
        }
        // Chercher une balise <img> dans le contenu
        if (preg_match('/<img.+src=["\']([^"\']+)["\']/', $contenu, $matches)) {
            return $matches[1];
        }
        // Image par défaut si aucune image trouvée
        return 'asset/img/default-news.jpg';
    }

    /**
     * Nettoie le contenu HTML pour l'aperçu (retire les images et les balises).
     */
    public static function nettoyerContenu(?string $contenu): string
    {
        if (empty($contenu)) {
            return "";
        }
        // Supprimer les balises images du texte pour l'extrait
        $texte = preg_replace('/<img[^>]+>/i', '', $contenu);
        return strip_tags($texte);
    }
}