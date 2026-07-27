<?php

namespace App\Models;

use PDO;

class Quartier
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Liste de tous les quartiers, triés par nom. */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id_quartier, nom_quartier FROM quartier ORDER BY nom_quartier');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId(int $idQuartier): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id_quartier, nom_quartier FROM quartier WHERE id_quartier = :id');
        $stmt->execute(['id' => $idQuartier]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }
}
