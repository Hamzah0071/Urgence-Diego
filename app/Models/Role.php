<?php

namespace App\Models;

use PDO;

class Role
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id_role, nom_role FROM role ORDER BY id_role');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId(int $idRole): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id_role, nom_role FROM role WHERE id_role = :id');
        $stmt->execute(['id' => $idRole]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }
}
