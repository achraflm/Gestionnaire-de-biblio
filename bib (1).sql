-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 29 nov. 2025 à 21:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bib`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `ID_Admin` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Prenom` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Date_Inscription` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`ID_Admin`, `Nom`, `Prenom`, `Email`, `Mot_de_passe`, `Date_Inscription`) VALUES
(1, 'Martin', 'Alexandre', 'alexandre.martin@bib.fr', '3b612c75a7b5048a435fb6ec81e52ff92d6d795a8b5a9c17070f6a63c97a53b2', '2024-01-01');

-- --------------------------------------------------------

--
-- Structure de la table `categorie_livre`
--

CREATE TABLE `categorie_livre` (
  `ID_Categorie` int(11) NOT NULL,
  `Libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie_livre`
--

INSERT INTO `categorie_livre` (`ID_Categorie`, `Libelle`) VALUES
(1, 'Littérature Classique'),
(2, 'Développement Web'),
(3, 'Psychologie'),
(4, ''),
(5, 'dsadda');

-- --------------------------------------------------------

--
-- Structure de la table `emprunter`
--

CREATE TABLE `emprunter` (
  `ID_Emprunt` int(11) NOT NULL,
  `ID_Etudiant` int(11) NOT NULL,
  `ID_Exemplaire` int(11) NOT NULL,
  `Date_Emprunt` date NOT NULL DEFAULT curdate(),
  `Date_Retour` date DEFAULT NULL,
  `Etat_Retour` enum('Bon état','Mauvais état') DEFAULT NULL,
  `Date_Retour_Prevue` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `emprunter`
--

INSERT INTO `emprunter` (`ID_Emprunt`, `ID_Etudiant`, `ID_Exemplaire`, `Date_Emprunt`, `Date_Retour`, `Etat_Retour`, `Date_Retour_Prevue`) VALUES
(1, 1, 47, '2025-11-29', '2025-11-29', NULL, NULL),
(2, 1, 15, '2025-11-29', NULL, NULL, NULL),
(3, 1, 16, '2025-11-29', NULL, NULL, NULL),
(4, 1, 27, '2025-11-29', NULL, NULL, NULL),
(5, 1, 31, '2025-11-29', NULL, NULL, '2025-12-14');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `ID_Etudiant` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Prenom` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Date_Inscription` date NOT NULL DEFAULT curdate(),
  `Statut` enum('Actif','Bloqué') NOT NULL DEFAULT 'Actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`ID_Etudiant`, `Nom`, `Prenom`, `Email`, `Mot_de_passe`, `Date_Inscription`, `Statut`) VALUES
(1, 'Dubois', 'Sophie', 'sophie.dubois@etudiant.fr', '123456', '2024-09-02', 'Actif'),
(2, 'Lefevre', 'Marc', 'marc.lefevre@etudiant.fr', '123456', '2024-09-03', 'Actif'),
(3, 'Garcia', 'Camille', 'camille.garcia@etudiant.fr', '123456', '2024-09-04', 'Actif'),
(4, 'Petit', 'Thomas', 'thomas.petit@etudiant.fr', '123456', '2024-09-05', 'Actif'),
(5, 'Bernard', 'Marie', 'marie.bernard@etudiant.fr', '123456', '2024-09-06', 'Actif'),
(6, 'Roux', 'Lucas', 'lucas.roux@etudiant.fr', '123456', '2024-09-07', 'Actif'),
(7, 'Moreau', 'Clara', 'clara.moreau@etudiant.fr', '123456', '2024-09-08', 'Actif'),
(8, 'Fournier', 'Hugo', 'hugo.fournier@etudiant.fr', '123456', '2024-09-09', 'Actif'),
(9, 'Girard', 'Emma', 'emma.girard@etudiant.fr', '123456', '2024-09-10', 'Actif'),
(10, 'Lambert', 'Antoine', 'antoine.lambert@etudiant.fr', '123456', '2024-09-01', 'Actif'),
(11, 'Vidal', 'Chloé', 'chloe.vidal@etudiant.fr', '123456', '2024-09-02', 'Actif'),
(12, 'Duval', 'Yanis', 'yanis.duval@etudiant.fr', '123456', '2024-09-03', 'Actif'),
(13, 'Perrin', 'Laura', 'laura.perrin@etudiant.fr', '123456', '2024-09-04', 'Actif'),
(14, 'Sanchez', 'Nicolas', 'nicolas.sanchez@etudiant.fr', '123456', '2024-09-05', 'Actif'),
(15, 'Leroy', 'Inès', 'ines.leroy@etudiant.fr', '123456', '2024-09-06', 'Actif'),
(16, 'Meyer', 'Jules', 'jules.meyer@etudiant.fr', '123456', '2024-09-07', 'Actif'),
(17, 'Schmitt', 'Léa', 'lea.schmitt@etudiant.fr', '123456', '2024-09-08', 'Actif'),
(18, 'Barbier', 'Paul', 'paul.barbier@etudiant.fr', '123456', '2024-09-09', 'Actif'),
(19, 'David', 'Manon', 'manon.david@etudiant.fr', '123456', '2024-09-10', 'Actif'),
(20, 'Robert', 'Hugo', 'hugo.robert@etudiant.fr', '123456', '2024-09-01', 'Actif');

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE `evaluation` (
  `ID_Evaluation` int(11) NOT NULL,
  `ID_Etudiant` int(11) NOT NULL,
  `ISBN` varchar(20) NOT NULL,
  `Note` int(11) NOT NULL CHECK (`Note` between 1 and 5),
  `Commentaire` text DEFAULT NULL,
  `Date_Evaluation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exemplaire`
--

CREATE TABLE `exemplaire` (
  `ID_Exemplaire` int(11) NOT NULL,
  `ISBN` varchar(20) NOT NULL,
  `Etat` enum('Bon état','Mauvais état') NOT NULL DEFAULT 'Bon état'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exemplaire`
--

INSERT INTO `exemplaire` (`ID_Exemplaire`, `ISBN`, `Etat`) VALUES
(1, '978-207036066', 'Bon état'),
(2, '978-207036066', 'Bon état'),
(3, '978-207037243', 'Bon état'),
(4, '978-207037243', 'Bon état'),
(5, '978-207037515', 'Bon état'),
(6, '978-207037515', 'Bon état'),
(7, '978-207037677', 'Bon état'),
(8, '978-207037677', 'Bon état'),
(9, '978-207037746', 'Bon état'),
(10, '978-207037746', 'Bon état'),
(11, '978-207044005', 'Bon état'),
(12, '978-207044005', 'Bon état'),
(13, '978-207044577', 'Bon état'),
(14, '978-207044577', 'Bon état'),
(15, '978-207044675', 'Bon état'),
(16, '978-207044675', 'Bon état'),
(17, '978-225300974', 'Bon état'),
(18, '978-225300974', 'Bon état'),
(19, '978-225301064', 'Bon état'),
(20, '978-225301064', 'Bon état'),
(21, '978-013443654', 'Bon état'),
(22, '978-013443654', 'Bon état'),
(23, '978-144933189', 'Bon état'),
(24, '978-144933189', 'Bon état'),
(25, '978-149190447', 'Bon état'),
(26, '978-149190447', 'Bon état'),
(27, '978-149191079', 'Bon état'),
(28, '978-149191079', 'Bon état'),
(29, '978-149193792', 'Bon état'),
(30, '978-149193792', 'Bon état'),
(31, '978-149195446', 'Bon état'),
(32, '978-149195446', 'Bon état'),
(33, '978-149204393', 'Bon état'),
(34, '978-149204393', 'Bon état'),
(35, '978-149206880', 'Bon état'),
(36, '978-149206880', 'Bon état'),
(37, '978-149207032', 'Bon état'),
(38, '978-149207032', 'Bon état'),
(39, '978-161729757', 'Bon état'),
(40, '978-161729757', 'Bon état'),
(41, '978-207032549', 'Bon état'),
(42, '978-207032549', 'Bon état'),
(43, '978-207040439', 'Bon état'),
(44, '978-207040439', 'Bon état'),
(45, '978-225300643', 'Bon état'),
(46, '978-225300643', 'Bon état'),
(47, '978-225309714', 'Bon état'),
(48, '978-225309714', 'Bon état'),
(49, '978-270001026', 'Bon état'),
(50, '978-270001026', 'Bon état'),
(51, '978-273811802', 'Bon état'),
(52, '978-273811802', 'Bon état'),
(53, '978-273812869', 'Bon état'),
(54, '978-273812869', 'Bon état'),
(55, '978-274670984', 'Bon état'),
(56, '978-274670984', 'Bon état'),
(57, '978-274991196', 'Bon état'),
(58, '978-274991196', 'Bon état'),
(59, '978-284567220', 'Bon état'),
(60, '978-284567220', 'Bon état');

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `ISBN` varchar(20) NOT NULL,
  `Titre` varchar(255) NOT NULL,
  `Auteur` varchar(150) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `Fichier_PDF` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`ISBN`, `Titre`, `Auteur`, `id_categorie`, `Fichier_PDF`) VALUES
('978-013443654', 'The Mythical Man-Month', 'Frederick Brooks Jr.', 2, NULL),
('978-144933189', 'Eloquent JavaScript, 3rd Edition', 'Marijn Haverbeke', 2, NULL),
('978-149190447', 'Python pour le Web et le Machine Learning', 'Andreas Mueller', 2, NULL),
('978-149191079', 'Apprendre le SQL', 'Alan Beaulieu', 2, NULL),
('978-149193792', 'Head First Design Patterns', 'Eric Freeman', 2, NULL),
('978-149195446', 'React.js Essentials', 'Artemij Fedosejev', 2, NULL),
('978-149204393', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 2, NULL),
('978-149206880', 'HTML5 & CSS3: Maîtriser les standards du Web', 'Christophe Aubry', 2, NULL),
('978-149207032', 'NoSQL Distilled', 'Pramod Sadalage', 2, NULL),
('978-161729757', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 2, NULL),
('978-207032549', 'Les Six Piliers de l\'estime de soi', 'Nathaniel Branden', 3, NULL),
('978-207036066', 'Madame Bovary', 'Gustave Flaubert', 1, NULL),
('978-207037243', 'Germinal', 'Émile Zola', 1, NULL),
('978-207037515', 'Vingt mille lieues sous les mers', 'Jules Verne', 1, NULL),
('978-207037677', 'L\'Étranger', 'Albert Camus', 1, NULL),
('978-207037746', 'Les Misérables', 'Victor Hugo', 1, NULL),
('978-207040439', 'Manuel de survie pour hypersensible', 'Saverio Tomasella', 3, NULL),
('978-207044005', 'Candide ou l\'Optimisme', 'Voltaire', 1, NULL),
('978-207044577', 'Les Fleurs du Mal', 'Charles Baudelaire', 1, NULL),
('978-207044675', 'À la recherche du temps perdu : Du côté de chez Swann', 'Marcel Proust', 1, NULL),
('978-225300643', 'L\'Interprétation des rêves', 'Sigmund Freud', 3, NULL),
('978-225300974', 'Le Père Goriot', 'Honoré de Balzac', 1, NULL),
('978-225301064', 'Voyage au bout de la nuit', 'Louis-Ferdinand Céline', 1, NULL),
('978-225309714', 'Introduction à la Psychologie Cognitive', 'Jean Decety', 3, NULL),
('978-270001026', 'Petit traité de manipulation à l\'usage des honnêtes gens', 'Robert-Vincent Joule', 3, NULL),
('978-273811802', 'Le Pouvoir de l\'instant présent', 'Eckhart Tolle', 3, NULL),
('978-273812869', 'L\'Homme qui prenait sa femme pour un chapeau', 'Oliver Sacks', 3, NULL),
('978-274670984', 'La Psychologie pour les Nuls', 'Adam Cash', 3, NULL),
('978-274991196', 'Dépasser la dépendance affective', 'Geneviève Krebs', 3, NULL),
('978-284567220', 'Cessez d\'être gentil, soyez vrai !', 'Thomas d\'Ansembourg', 3, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `ID_Message` int(11) NOT NULL,
  `ID_Etudiant` int(11) NOT NULL,
  `Sens` enum('EtudiantVersAdmin','AdminVersEtudiant') NOT NULL,
  `Contenu` text NOT NULL,
  `Date_Envoi` datetime DEFAULT current_timestamp(),
  `Lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `ID_Reservation` int(11) NOT NULL,
  `ID_Etudiant` int(11) NOT NULL,
  `ISBN` varchar(20) NOT NULL,
  `Date_Reservation` datetime NOT NULL DEFAULT current_timestamp(),
  `Statut` enum('En attente','Confirmée','Annulée') NOT NULL DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`ID_Reservation`, `ID_Etudiant`, `ISBN`, `Date_Reservation`, `Statut`) VALUES
(1, 1, '978-207044675', '2025-11-29 20:27:58', 'En attente'),
(2, 1, '978-207044675', '2025-11-29 20:54:50', 'En attente'),
(3, 1, '978-207044675', '2025-11-29 20:54:54', 'En attente'),
(4, 1, '978-207044675', '2025-11-29 20:55:12', 'En attente'),
(5, 1, '978-207044675', '2025-11-29 20:55:45', 'En attente');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`ID_Admin`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Index pour la table `categorie_livre`
--
ALTER TABLE `categorie_livre`
  ADD PRIMARY KEY (`ID_Categorie`);

--
-- Index pour la table `emprunter`
--
ALTER TABLE `emprunter`
  ADD PRIMARY KEY (`ID_Emprunt`),
  ADD KEY `ID_Etudiant` (`ID_Etudiant`),
  ADD KEY `ID_Exemplaire` (`ID_Exemplaire`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`ID_Etudiant`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Index pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`ID_Evaluation`),
  ADD KEY `ID_Etudiant` (`ID_Etudiant`),
  ADD KEY `ISBN` (`ISBN`);

--
-- Index pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD PRIMARY KEY (`ID_Exemplaire`),
  ADD KEY `ISBN` (`ISBN`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`ISBN`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`ID_Message`),
  ADD KEY `ID_Etudiant` (`ID_Etudiant`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ID_Reservation`),
  ADD KEY `ID_Etudiant` (`ID_Etudiant`),
  ADD KEY `ISBN` (`ISBN`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `ID_Admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `categorie_livre`
--
ALTER TABLE `categorie_livre`
  MODIFY `ID_Categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `emprunter`
--
ALTER TABLE `emprunter`
  MODIFY `ID_Emprunt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `etudiant`
--
ALTER TABLE `etudiant`
  MODIFY `ID_Etudiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `ID_Evaluation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  MODIFY `ID_Exemplaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `ID_Message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ID_Reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `emprunter`
--
ALTER TABLE `emprunter`
  ADD CONSTRAINT `emprunter_ibfk_1` FOREIGN KEY (`ID_Etudiant`) REFERENCES `etudiant` (`ID_Etudiant`),
  ADD CONSTRAINT `emprunter_ibfk_2` FOREIGN KEY (`ID_Exemplaire`) REFERENCES `exemplaire` (`ID_Exemplaire`);

--
-- Contraintes pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`ID_Etudiant`) REFERENCES `etudiant` (`ID_Etudiant`),
  ADD CONSTRAINT `evaluation_ibfk_2` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`);

--
-- Contraintes pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD CONSTRAINT `exemplaire_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`);

--
-- Contraintes pour la table `livre`
--
ALTER TABLE `livre`
  ADD CONSTRAINT `livre_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie_livre` (`ID_Categorie`);

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`ID_Etudiant`) REFERENCES `etudiant` (`ID_Etudiant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`ID_Etudiant`) REFERENCES `etudiant` (`ID_Etudiant`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
