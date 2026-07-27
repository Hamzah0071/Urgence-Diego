<?php

namespace App\Models;

use PDO;
use PDOException;

class Service
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Une seule pharmacie de garde (la plus récente), pour la landing page
     * et le bandeau de home.php.
     */
    public function getPharmacieDeGardeDuJour(): ?array
    {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("
            SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, s.adresse, q.nom_quartier
            FROM garde g
            JOIN service s ON g.id_service = s.id_service
            JOIN type_service ts ON s.id_type = ts.id_type
            JOIN quartier q ON s.id_quartier = q.id_quartier
            WHERE :today BETWEEN g.date_debut AND g.date_fin
              AND ts.nom_type = 'Pharmacie'
              AND s.actif = 1
            ORDER BY g.date_debut DESC
            LIMIT 1
        ");
        $stmt->execute(['today' => $today]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    /** Toutes les pharmacies de garde aujourd'hui (peut y en avoir plusieurs par quartier). */
    public function getPharmaciesDeGardeAujourdhui(): array
    {
        try {
            $sql = "SELECT s.libelle, s.telephone, s.adresse
                    FROM garde g
                    INNER JOIN service s ON g.id_service = s.id_service
                    WHERE s.id_type = 1
                      AND s.actif = 1
                      AND CURDATE() BETWEEN g.date_debut AND g.date_fin
                    ORDER BY s.libelle";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /** Poste fixe (pas de rotation de garde), affiché sur la landing page. */
    public function getPoliceCentrale(): ?array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, s.adresse, q.nom_quartier
                FROM service s
                JOIN quartier q ON s.id_quartier = q.id_quartier
                WHERE s.libelle = 'Police Centrale'
                LIMIT 1
            ");
            $stmt->execute();
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultat ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Numéros d'urgence classés par catégorie, pour les 3 cartes de home.php.
     * @return array{pompier: array, police: array, ambulance: array}
     */
    public function getServicesUrgence(): array
    {
        $resultat = ['pompier' => [], 'police' => [], 'ambulance' => []];
        try {
            $sql = "SELECT s.libelle, s.telephone, t.nom_type
                    FROM service s
                    INNER JOIN type_service t ON s.id_type = t.id_type
                    WHERE s.actif = 1
                      AND s.id_type IN (2, 3, 4)
                    ORDER BY t.nom_type, s.libelle";
            $lignes = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($lignes as $s) {
                switch ($s['nom_type']) {
                    case 'Pompier':
                        $resultat['pompier'][] = $s;
                        break;
                    case "Force de l'ordre":
                        $resultat['police'][] = $s;
                        break;
                    case 'Hôpital':
                        $resultat['ambulance'][] = $s;
                        break;
                }
            }
        } catch (PDOException $e) {
            return ['pompier' => [], 'police' => [], 'ambulance' => []];
        }

        return $resultat;
    }

    /** Liste des types de service (Pharmacie, Pompier, Force de l'ordre, Hôpital...). */
    public function getTypesService(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT id_type, nom_type FROM type_service ORDER BY nom_type');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Recherche filtrée pour service-urgence.php (quartier, type, texte libre).
     */
    public function rechercher(?int $idQuartier = null, ?int $idType = null, ?string $recherche = null): array
    {
        $sql = "
            SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, ts.nom_type AS nom_categorie, q.nom_quartier
            FROM service s
            JOIN type_service ts ON s.id_type = ts.id_type
            JOIN quartier q ON s.id_quartier = q.id_quartier
            WHERE s.actif = 1
        ";
        $params = [];

        if ($idQuartier) {
            $sql .= ' AND s.id_quartier = :id_quartier';
            $params['id_quartier'] = $idQuartier;
        }
        if ($idType) {
            $sql .= ' AND s.id_type = :id_type';
            $params['id_type'] = $idType;
        }
        if ($recherche) {
            $sql .= ' AND (s.libelle LIKE :recherche OR s.telephone LIKE :recherche)';
            $params['recherche'] = '%' . $recherche . '%';
        }

        $sql .= ' ORDER BY ts.nom_type, s.libelle';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Une pharmacie de garde "enrichie" (nom_service/nom_quartier/numero_telephone), pour service-urgence.php. */
    public function getPharmacieGardeAvecQuartier(): ?array
    {
        try {
            $sql = "SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, q.nom_quartier
                    FROM garde g
                    JOIN service s ON g.id_service = s.id_service
                    JOIN quartier q ON s.id_quartier = q.id_quartier
                    WHERE s.id_type = 1
                      AND s.actif = 1
                      AND CURDATE() BETWEEN g.date_debut AND g.date_fin
                    ORDER BY g.date_debut DESC
                    LIMIT 1";
            $resultat = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            return $resultat ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /** Tous les services actifs avec coordonnées, pour la carte Leaflet (urgence-carte.php). */
    public function getServicesPourCarte(): array
    {
        try {
            $sql = "SELECT
                        s.id_service,
                        s.libelle,
                        s.adresse,
                        s.telephone,
                        s.description,
                        COALESCE(s.latitude, q.latitude)   AS latitude,
                        COALESCE(s.longitude, q.longitude) AS longitude,
                        ts.nom_type,
                        q.nom_quartier
                    FROM service s
                    JOIN type_service ts ON s.id_type = ts.id_type
                    LEFT JOIN quartier q ON s.id_quartier = q.id_quartier
                    WHERE s.actif = 1
                    ORDER BY ts.nom_type, s.libelle";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur chargement service : ' . $e->getMessage());
            return [];
        }
    }
}
