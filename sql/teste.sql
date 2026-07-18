-- ============================================================
-- Base de données : urgences_antsiranana
-- Création complète du schéma (sans migration de données)
-- Version alignée sur l'état actuel de la base (18/07/2026)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ------------------------------------------------------------
-- roles
-- ------------------------------------------------------------
CREATE TABLE `role` (
  `id_role` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_role` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `nom_role` (`nom_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `role` (`nom_role`) VALUES
  ('Administrateur'),
  ('Redacteur'),
  ('Visiteur');

-- ------------------------------------------------------------
-- quartiers
-- ------------------------------------------------------------
CREATE TABLE `quartier` (
  `id_quartier` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_quartier` VARCHAR(100) NOT NULL,
  `latitude` DECIMAL(10,7) DEFAULT NULL,
  `longitude` DECIMAL(10,7) DEFAULT NULL,
  PRIMARY KEY (`id_quartier`),
  UNIQUE KEY `nom_quartier` (`nom_quartier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `quartier` (`nom_quartier`) VALUES
  ('Place Kabary'), ('Avenir'), ('SCAMA'), ('Lazaret Nord'), ('Lazaret Sud'),
  ('Grand Pavois'), ('Tanambao V'), ('Ambalavola'), ('Soafeno'), ('Morafeno'),
  ('Mahatsara'), ('Cité Ouvrière'), ('Tsaramandroso'), ('Bazar Kely'),
  ('Manongalaza'), ('Tanambao Nord'), ('Tanambao Sud');

-- ------------------------------------------------------------
-- utilisateurs
-- ------------------------------------------------------------
CREATE TABLE `utilisateur` (
  `id_utilisateur` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `date_naissance` DATE DEFAULT NULL,
  `id_quartier` INT(11) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `id_role` INT(11) NOT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `date_creation` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`),
  KEY `id_role` (`id_role`),
  KEY `id_quartier` (`id_quartier`),
  CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`),
  CONSTRAINT `utilisateur_ibfk_2` FOREIGN KEY (`id_quartier`) REFERENCES `quartier` (`id_quartier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- types de service (pharmacie, force de l'ordre, pompier, hopital...)
-- ------------------------------------------------------------
CREATE TABLE `type_service` (
  `id_type` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_type` VARCHAR(100) NOT NULL,
  `icone` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id_type`),
  UNIQUE KEY `nom_type` (`nom_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `type_service` (`nom_type`) VALUES
  ('Pharmacie'), ('Pompier'), ('Force de l\'ordre'), ('Hôpital');

-- ------------------------------------------------------------
-- services
-- ------------------------------------------------------------
CREATE TABLE `service` (
  `id_service` INT(11) NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(50) NOT NULL,
  `adresse` VARCHAR(255) NOT NULL,
  `latitude` DECIMAL(10,7) DEFAULT NULL,
  `longitude` DECIMAL(10,7) DEFAULT NULL,
  `id_quartier` INT(11) NOT NULL,
  `id_type` INT(11) NOT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `description` TEXT DEFAULT NULL,
  `date_creation` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `date_modification` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_service`),
  KEY `id_quartier` (`id_quartier`),
  KEY `id_type` (`id_type`),
  CONSTRAINT `service_ibfk_1` FOREIGN KEY (`id_quartier`) REFERENCES `quartier` (`id_quartier`),
  CONSTRAINT `service_ibfk_2` FOREIGN KEY (`id_type`) REFERENCES `type_service` (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- types de véhicule (ambulance, livraison, fourgon...)
-- ------------------------------------------------------------
CREATE TABLE `type_vehicule` (
  `id_type_vehicule` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_type` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_type_vehicule`),
  UNIQUE KEY `nom_type` (`nom_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `type_vehicule` (`nom_type`) VALUES
  ('Ambulance'), ('Véhicule de livraison'), ('Fourgon'), ('Camion');

-- ------------------------------------------------------------
-- véhicules rattachés à un service (0, 1 ou plusieurs par service)
-- ------------------------------------------------------------
CREATE TABLE `vehicule` (
  `id_vehicule` INT(11) NOT NULL AUTO_INCREMENT,
  `id_service` INT(11) NOT NULL,
  `id_type_vehicule` INT(11) NOT NULL,
  `nom` VARCHAR(255) DEFAULT NULL,
  `telephone` VARCHAR(50) DEFAULT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_vehicule`),
  KEY `id_service` (`id_service`),
  KEY `id_type_vehicule` (`id_type_vehicule`),
  CONSTRAINT `vehicule_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`) ON DELETE CASCADE,
  CONSTRAINT `vehicule_ibfk_2` FOREIGN KEY (`id_type_vehicule`) REFERENCES `type_vehicule` (`id_type_vehicule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- gardes (rotation, valable pour n'importe quel type de service)
-- ------------------------------------------------------------
CREATE TABLE `garde` (
  `id_garde` INT(11) NOT NULL AUTO_INCREMENT,
  `id_service` INT(11) NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `est_exceptionnel` TINYINT(1) DEFAULT 0,
  `notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_garde`),
  KEY `id_service` (`id_service`),
  CONSTRAINT `garde_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- sources d'articles (flux RSS, réseaux sociaux...)
-- ------------------------------------------------------------
CREATE TABLE `sources_articles` (
  `id_source` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_source` VARCHAR(150) NOT NULL,
  `type_source` ENUM('rss','reseau_social') NOT NULL DEFAULT 'rss',
  `url_flux` VARCHAR(500) NOT NULL,
  `identifiant_page` VARCHAR(150) DEFAULT NULL,
  `url_instance_bridge` VARCHAR(255) DEFAULT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `date_ajout` DATETIME DEFAULT current_timestamp(),
  PRIMARY KEY (`id_source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- articles
-- ------------------------------------------------------------
CREATE TABLE `article` (
  `id_article` INT(11) NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) NOT NULL,
  `contenu` TEXT NOT NULL,
  `lien_source` VARCHAR(500) DEFAULT NULL,
  `id_auteur` INT(11) DEFAULT NULL,
  `id_source` INT(11) DEFAULT NULL,
  `statut` ENUM('brouillon','publie','archive') NOT NULL DEFAULT 'brouillon',
  `date_publication` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `derniere_modification` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_article`),
  UNIQUE KEY `lien_source_unique` (`lien_source`),
  KEY `id_auteur` (`id_auteur`),
  KEY `id_source` (`id_source`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateur` (`id_utilisateur`),
  CONSTRAINT `article_ibfk_2` FOREIGN KEY (`id_source`) REFERENCES `sources_articles` (`id_source`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- logs
-- ------------------------------------------------------------
CREATE TABLE `log` (
  `id_log` INT(11) NOT NULL AUTO_INCREMENT,
  `type_log` VARCHAR(50) NOT NULL,
  `message` TEXT NOT NULL,
  `id_utilisateur` INT(11) DEFAULT NULL,
  `date_log` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `log_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;