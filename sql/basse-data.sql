-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 04 juil. 2026 à 19:05
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `urgences_antsiranana`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id_article` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `id_auteur` int(11) NOT NULL,
  `date_publication` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories_service`
--

CREATE TABLE `categories_service` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories_service`
--

INSERT INTO `categories_service` (`id_categorie`, `nom_categorie`) VALUES
(3, 'Force de l\'ordre'),
(4, 'Hôpital'),
(1, 'Pharmacie'),
(2, 'Pompier');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id_log` int(11) NOT NULL,
  `type_log` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `date_log` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pharmacies`
--

CREATE TABLE `pharmacies` (
  `id_pharmacie` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `nom_pharmacien` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pharmacies`
--

INSERT INTO `pharmacies` (`id_pharmacie`, `id_service`, `nom_pharmacien`) VALUES
(1, 1, NULL),
(2, 2, NULL),
(3, 3, NULL),
(4, 4, NULL),
(5, 5, NULL),
(6, 6, NULL),
(7, 7, NULL),
(8, 8, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `quartiers`
--

CREATE TABLE `quartiers` (
  `id_quartier` int(11) NOT NULL,
  `nom_quartier` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quartiers`
--

INSERT INTO `quartiers` (`id_quartier`, `nom_quartier`) VALUES
(8, 'Ambalavola'),
(2, 'Avenir'),
(14, 'Bazar Kely'),
(12, 'Cité Ouvrière'),
(6, 'Grand Pavois'),
(4, 'Lazaret Nord'),
(5, 'Lazaret Sud'),
(11, 'Mahatsara'),
(15, 'Manongalaza'),
(10, 'Morafeno'),
(1, 'Place Kabary'),
(3, 'SCAMA'),
(9, 'Soafeno'),
(7, 'Tanambao V'),
(13, 'Tsaramandroso');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `nom_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `nom_role`) VALUES
(3, 'Administrateur'),
(2, 'Redacteur'),
(1, 'Visiteur');

-- --------------------------------------------------------

--
-- Structure de la table `services_urgence`
--

CREATE TABLE `services_urgence` (
  `id_service` int(11) NOT NULL,
  `nom_service` varchar(255) NOT NULL,
  `numero_telephone` varchar(50) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `id_quartier` int(11) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `description_specifique` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `services_urgence`
--

INSERT INTO `services_urgence` (`id_service`, `nom_service`, `numero_telephone`, `adresse`, `id_quartier`, `id_categorie`, `description_specifique`) VALUES
(1, 'Pharmacie Scama', '032 03 250 23', 'a Cote Bar Cristal Scama', 3, 1, 'Appelée aussi Issa dans le calendrier'),
(2, 'Pharmacie Mahasoa', '000 00 000 00', 'Rue la pirotechnique Grand Pavois', 6, 1, NULL),
(3, 'Pharmacie Mora', '032 78 826 04', 'Place Kabary', 1, 1, NULL),
(4, 'Pharmacie Esperance', '032 44 116 80', 'Place Kabary', 1, 1, NULL),
(5, 'Pharmacie Henintsoa', '000 00 000 00', 'Rue Justin Bezara', 7, 1, NULL),
(6, 'Pharmacie Avenir', '000 00 000 00', '32 Rue Lafayette', 2, 1, NULL),
(7, 'Pharmacie Olga', '000 00 000 00', 'Quartier Lazaret', 4, 1, NULL),
(8, 'Pharmacie Mahavy', '000 00 000 00', 'Quartier Tanambao', 7, 1, NULL),

(9, 'Pompier Antsiranana', '032 63 505 56', 'Quartier Lazaret', 4, 2, NULL),
(10, 'Ambulance Homi', '032 84 794 64', 'Hôpital Militaire', 4, 4, NULL),
(11, 'Ambulance Hopitale BE', '032 40 794 15', 'Centre Ville', 1, 4, NULL),
(12, 'Ambulance Policlinique', '034 49 110 11', 'Centre Ville', 1, 4, NULL),
(13, 'Ambulance CU/DS', '032 62 360 53', 'Centre Ville', 1, 4, NULL),
(14, 'FIP', '034 05 998 60', 'Place Kabary', 1, 3, NULL),
(15, 'Police Manogalaza', '034 05 440 66', 'Quartier Manongalaza', 15, 3, NULL),
(16, 'Police Centrale', '034 05 507 14', 'Centre Ville', 1, 3, NULL),
(17, 'Police Tanabao 5', '034 05 440 66', 'Tanambao V', 7, 3, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tours_garde`
--

CREATE TABLE `tours_garde` (
  `id_tour_garde` int(11) NOT NULL,
  `id_pharmacie` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `est_exceptionnel` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tours_garde`
--

INSERT INTO `tours_garde` (`id_tour_garde`, `id_pharmacie`, `date_debut`, `date_fin`, `est_exceptionnel`, `notes`) VALUES
(1, 5, '2026-01-03', '2026-01-10', 0, NULL),
(2, 6, '2026-01-10', '2026-01-17', 0, NULL),
(3, 3, '2026-01-17', '2026-01-24', 0, NULL),
(4, 2, '2026-01-24', '2026-01-31', 0, NULL),
(5, 8, '2026-01-31', '2026-02-07', 0, NULL),
(6, 1, '2026-02-07', '2026-02-14', 0, NULL),
(7, 4, '2026-02-14', '2026-02-21', 0, NULL),
(8, 7, '2026-02-21', '2026-02-28', 0, NULL),
(9, 5, '2026-02-28', '2026-03-07', 0, NULL),
(10, 6, '2026-03-07', '2026-03-14', 0, NULL),
(11, 3, '2026-03-14', '2026-03-21', 0, NULL),
(12, 2, '2026-03-21', '2026-03-28', 0, NULL),
(13, 8, '2026-03-28', '2026-04-04', 0, NULL),
(14, 1, '2026-04-04', '2026-04-11', 0, NULL),
(15, 4, '2026-04-11', '2026-04-18', 0, NULL),
(16, 7, '2026-04-18', '2026-04-25', 0, NULL),
(17, 5, '2026-04-25', '2026-05-02', 0, NULL),
(18, 6, '2026-05-02', '2026-05-09', 0, NULL),
(19, 3, '2026-05-09', '2026-05-16', 0, NULL),
(20, 2, '2026-05-16', '2026-05-23', 0, NULL),
(21, 8, '2026-05-23', '2026-05-30', 0, NULL),
(22, 1, '2026-05-30', '2026-06-06', 0, NULL),
(23, 4, '2026-06-06', '2026-06-13', 0, NULL),
(24, 7, '2026-06-13', '2026-06-20', 0, NULL),
(25, 5, '2026-06-20', '2026-06-27', 0, NULL),
(26, 6, '2026-06-27', '2026-07-04', 0, NULL),
(27, 3, '2026-07-04', '2026-07-11', 0, NULL),
(28, 2, '2026-07-11', '2026-07-18', 0, NULL),
(29, 8, '2026-07-18', '2026-07-25', 0, NULL),
(30, 1, '2026-07-25', '2026-08-01', 0, NULL),
(31, 4, '2026-08-01', '2026-08-08', 0, NULL),
(32, 7, '2026-08-08', '2026-08-15', 0, NULL),
(33, 5, '2026-08-15', '2026-08-22', 0, NULL),
(34, 6, '2026-08-22', '2026-08-29', 0, NULL),
(35, 3, '2026-08-29', '2026-09-05', 0, NULL),
(36, 2, '2026-09-05', '2026-09-12', 0, NULL),
(37, 8, '2026-09-12', '2026-09-19', 0, NULL),
(38, 1, '2026-09-19', '2026-09-26', 0, NULL),
(39, 4, '2026-09-26', '2026-10-03', 0, NULL),
(40, 7, '2026-10-03', '2026-10-10', 0, NULL),
(41, 5, '2026-10-10', '2026-10-17', 0, NULL),
(42, 6, '2026-10-17', '2026-10-24', 0, NULL),
(43, 3, '2026-10-24', '2026-10-31', 0, NULL),
(44, 2, '2026-10-31', '2026-11-07', 0, NULL),
(45, 8, '2026-11-07', '2026-11-14', 0, NULL),
(46, 1, '2026-11-14', '2026-11-21', 0, NULL),
(47, 4, '2026-11-21', '2026-11-28', 0, NULL),
(48, 7, '2026-11-28', '2026-12-05', 0, NULL),
(49, 5, '2026-12-05', '2026-12-12', 0, NULL),
(50, 6, '2026-12-12', '2026-12-19', 0, NULL),
(51, 3, '2026-12-19', '2026-12-26', 0, NULL),
(52, 2, '2026-12-26', '2027-01-02', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `id_quartier` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `date_naissance`, `id_quartier`, `email`, `mot_de_passe`, `id_role`, `date_creation`) VALUES
(1, 'Hamzah', 'Bouchirany', '2005-07-12', 5, 'bouchiranymisizarahamzah@gmail.com', 'a0afeb5333afa658aa48f0822cc124d35f5224d6775121728c780ead3a58b5e4', 3, '2026-07-04 09:31:39'),
(4, 'Haz\'mah', 'Nasser', '2000-02-15', 2, 'nasser@gmail.com', '15e2b0d3c33891ebb0f1ef609ec419420c20e320ce94c65fbc8c3312448eb225', 1, '2026-07-04 09:53:21');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `id_auteur` (`id_auteur`);

--
-- Index pour la table `categories_service`
--
ALTER TABLE `categories_service`
  ADD PRIMARY KEY (`id_categorie`),
  ADD UNIQUE KEY `nom_categorie` (`nom_categorie`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD PRIMARY KEY (`id_pharmacie`),
  ADD UNIQUE KEY `id_service` (`id_service`);

--
-- Index pour la table `quartiers`
--
ALTER TABLE `quartiers`
  ADD PRIMARY KEY (`id_quartier`),
  ADD UNIQUE KEY `nom_quartier` (`nom_quartier`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `nom_role` (`nom_role`);

--
-- Index pour la table `services_urgence`
--
ALTER TABLE `services_urgence`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `id_quartier` (`id_quartier`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `tours_garde`
--
ALTER TABLE `tours_garde`
  ADD PRIMARY KEY (`id_tour_garde`),
  ADD KEY `id_pharmacie` (`id_pharmacie`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`),
  ADD KEY `id_quartier` (`id_quartier`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id_article` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories_service`
--
ALTER TABLE `categories_service`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pharmacies`
--
ALTER TABLE `pharmacies`
  MODIFY `id_pharmacie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `quartiers`
--
ALTER TABLE `quartiers`
  MODIFY `id_quartier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `services_urgence`
--
ALTER TABLE `services_urgence`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `tours_garde`
--
ALTER TABLE `tours_garde`
  MODIFY `id_tour_garde` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD CONSTRAINT `pharmacies_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `services_urgence` (`id_service`);

--
-- Contraintes pour la table `services_urgence`
--
ALTER TABLE `services_urgence`
  ADD CONSTRAINT `services_urgence_ibfk_1` FOREIGN KEY (`id_quartier`) REFERENCES `quartiers` (`id_quartier`),
  ADD CONSTRAINT `services_urgence_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categories_service` (`id_categorie`);

--
-- Contraintes pour la table `tours_garde`
--
ALTER TABLE `tours_garde`
  ADD CONSTRAINT `tours_garde_ibfk_1` FOREIGN KEY (`id_pharmacie`) REFERENCES `pharmacies` (`id_pharmacie`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`),
  ADD CONSTRAINT `utilisateurs_ibfk_2` FOREIGN KEY (`id_quartier`) REFERENCES `quartiers` (`id_quartier`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
