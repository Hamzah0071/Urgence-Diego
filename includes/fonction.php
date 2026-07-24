<?php

/**
 * Fonctions utilitaires partagées (utilisent PDO, pas mysqli)
 */

// Retourne la liste de tous les quartiers
function getQuartiers($pdo) {
    $stmt = $pdo->query("SELECT id_quartier, nom_quartier FROM quartier ORDER BY nom_quartier");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retourne la pharmacie de garde en cours (basée sur la date du jour)
function getPharmacieGarde($pdo) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, q.nom_quartier
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
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

// Retourne les services d'urgence avec leur catégorie et leur quartier.
// Filtres optionnels : quartier, type de service, recherche texte (nom ou téléphone).
function getServices($pdo, $id_quartier = null, $id_type = null, $recherche = null) {
    $sql = "
        SELECT s.libelle AS nom_service, s.telephone AS numero_telephone, ts.nom_type AS nom_categorie, q.nom_quartier
        FROM service s
        JOIN type_service ts ON s.id_type = ts.id_type
        JOIN quartier q ON s.id_quartier = q.id_quartier
        WHERE s.actif = 1
    ";

    $params = [];

    if ($id_quartier) {
        $sql .= " AND s.id_quartier = :id_quartier";
        $params['id_quartier'] = $id_quartier;
    }

    if ($id_type) {
        $sql .= " AND s.id_type = :id_type";
        $params['id_type'] = $id_type;
    }

    if ($recherche) {
        $sql .= " AND (s.libelle LIKE :recherche OR s.telephone LIKE :recherche)";
        $params['recherche'] = '%' . $recherche . '%';
    }

    $sql .= " ORDER BY ts.nom_type, s.libelle";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}