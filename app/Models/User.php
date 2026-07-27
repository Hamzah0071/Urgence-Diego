<?php

namespace App\Models;

use PDO;

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Utilisé par AuthController::login() : utilisateur + libellé de son rôle. */
    public function trouverParEmailAvecRole(string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, r.nom_role
            FROM utilisateur u
            LEFT JOIN role r ON u.id_role = r.id_role
            WHERE u.email = :email
        ");
        $stmt->execute(['email' => $email]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    /** Utilisé par app/includes/session.php (requireAuth) et le profil. */
    public function trouverParIdAvecRole(int $idUtilisateur): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.id_role, r.nom_role
            FROM utilisateur u
            JOIN role r ON u.id_role = r.id_role
            WHERE u.id_utilisateur = :id
        ");
        $stmt->execute(['id' => $idUtilisateur]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    /** Utilisé par HomeController::profil() : version enrichie (quartier + rôle). */
    public function getProfilComplet(int $idUtilisateur): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.nom, u.prenom, u.email, u.date_naissance, u.date_creation,
                   q.nom_quartier,
                   r.nom_role
            FROM utilisateur u
            LEFT JOIN quartier q ON u.id_quartier = q.id_quartier
            LEFT JOIN role r ON u.id_role = r.id_role
            WHERE u.id_utilisateur = :id
        ");
        $stmt->execute(['id' => $idUtilisateur]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetch();
    }

    /**
     * Crée un compte avec le rôle "Visiteur" (id_role = 1).
     * $donnees attend : nom, prenom, email, mot_de_passe (déjà hashé), id_quartier, date_naissance (ou null)
     */
    public function creerVisiteur(array $donnees): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, id_quartier, id_role, date_naissance)
            VALUES (:nom, :prenom, :email, :mot_de_passe, :id_quartier, :id_role, :date_naissance)
        ");
        $stmt->execute([
            'nom'            => $donnees['nom'],
            'prenom'         => $donnees['prenom'],
            'email'          => $donnees['email'],
            'mot_de_passe'   => $donnees['mot_de_passe'],
            'id_quartier'    => $donnees['id_quartier'],
            'id_role'        => 1,
            'date_naissance' => $donnees['date_naissance'],
        ]);
    }
}
