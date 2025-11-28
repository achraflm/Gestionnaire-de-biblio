-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 nov. 2025 à 20:48
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

-- --------------------------------------------------------

--
-- Structure de la table `categorie_livre`
--

CREATE TABLE `categorie_livre` (
  `ID_Categorie` int(11) NOT NULL,
  `Libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `Etat_Retour` enum('Bon état','Mauvais état') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `ISBN` varchar(20) NOT NULL,
  `Titre` varchar(255) NOT NULL,
  `Auteur` varchar(150) NOT NULL,
  `id_categorie` int(11) NOT NULL
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
  MODIFY `ID_Admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categorie_livre`
--
ALTER TABLE `categorie_livre`
  MODIFY `ID_Categorie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emprunter`
--
ALTER TABLE `emprunter`
  MODIFY `ID_Emprunt` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etudiant`
--
ALTER TABLE `etudiant`
  MODIFY `ID_Etudiant` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `ID_Evaluation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  MODIFY `ID_Exemplaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ID_Reservation` int(11) NOT NULL AUTO_INCREMENT;

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
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`ID_Etudiant`) REFERENCES `etudiant` (`ID_Etudiant`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
