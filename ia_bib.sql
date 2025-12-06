-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 04 déc. 2025 à 16:09
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
-- Base de données : `ia_bib`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `ID_Admin` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Date_inscription_admin` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`ID_Admin`, `Nom`, `Prenom`, `Email`, `Mot_de_passe`, `Date_inscription_admin`) VALUES
(1, 'Karim', 'Yassine', 'yassine.admin1@example.com', 'admin123', '2025-12-04 16:02:57'),
(2, 'Nadia', 'Sofia', 'sofia.admin2@example.com', 'admin456', '2025-12-04 16:02:57'),
(3, 'Rachid', 'Omar', 'omar.admin3@example.com', 'admin789', '2025-12-04 16:02:57'),
(4, 'Imane', 'Aya', 'aya.admin4@example.com', 'admin000', '2025-12-04 16:02:57');

-- --------------------------------------------------------

--
-- Structure de la table `auteur`
--

CREATE TABLE `auteur` (
  `ID_Auteur` int(11) NOT NULL,
  `Nom_Auteur` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `auteur`
--

INSERT INTO `auteur` (`ID_Auteur`, `Nom_Auteur`) VALUES
(1, 'J.K. Rowling'),
(2, 'George Orwell'),
(3, 'Victor Hugo'),
(4, 'Albert Einstein'),
(5, 'Isaac Newton'),
(6, 'Platon'),
(7, 'Aristote'),
(8, 'Leonardo da Vinci'),
(9, 'Stephen King'),
(10, 'Agatha Christie'),
(11, 'Tolkien J.R.R.'),
(12, 'Homer'),
(13, 'Mark Twain'),
(14, 'Charles Dickens'),
(15, 'F. Scott Fitzgerald'),
(16, 'Gabriel Garcia Marquez'),
(17, 'Paulo Coelho'),
(18, 'Ernest Hemingway'),
(19, 'Dan Brown'),
(20, 'Simone de Beauvoir');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `ID_Categorie` int(11) NOT NULL,
  `Libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`ID_Categorie`, `Libelle`) VALUES
(1, 'Informatique'),
(2, 'Science'),
(3, 'Littérature'),
(4, 'Histoire'),
(5, 'Mathématiques'),
(6, 'Philosophie'),
(7, 'Arts'),
(8, 'Technologie'),
(9, 'sportif');

-- --------------------------------------------------------

--
-- Structure de la table `emprunt`
--

CREATE TABLE `emprunt` (
  `ID_Emprunt` int(11) NOT NULL,
  `ID_Etudiant` int(11) DEFAULT NULL,
  `ID_Exemplaire` int(11) DEFAULT NULL,
  `Date_Emprunt` datetime DEFAULT current_timestamp(),
  `Date_Retour` datetime DEFAULT NULL,
  `Etat_Retour` enum('bon','mauvais','non rendu') DEFAULT 'non rendu',
  `Date_Retour_Prevu` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE `evaluation` (
  `ID_Evaluation` int(11) NOT NULL,
  `ID_Etudiant` int(11) DEFAULT NULL,
  `ISBN` varchar(30) DEFAULT NULL,
  `Note` int(11) DEFAULT NULL CHECK (`Note` >= 0 and `Note` <= 5),
  `Commentaire` text DEFAULT NULL,
  `Date_Evaluation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exemplaire`
--

CREATE TABLE `exemplaire` (
  `ID_Exemplaire` int(11) NOT NULL,
  `ISBN` varchar(30) DEFAULT NULL,
  `Etat` enum('bon etat','mauvais etat') DEFAULT 'bon etat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `ISBN` varchar(30) NOT NULL,
  `Titre` varchar(200) NOT NULL,
  `ID_Categorie` int(11) DEFAULT NULL,
  `Book_Cover` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `ID_Auteur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`ISBN`, `Titre`, `ID_Categorie`, `Book_Cover`, `Description`, `ID_Auteur`) VALUES
('ISBN0001', 'Programmation en Python', 1, NULL, 'Apprentissage du langage Python.', 1),
('ISBN0002', 'Java pour débutants', 1, NULL, 'Introduction au langage Java.', 2),
('ISBN0003', 'Algorithmique Avancée', 5, NULL, 'Techniques avancées en algorithmique.', 3),
('ISBN0004', 'Physique Moderne', 2, NULL, 'Concepts de la physique contemporaine.', 4),
('ISBN0005', 'Calcul Différentiel', 5, NULL, 'Cours complet sur le calcul différentiel.', 5),
('ISBN0006', 'Philosophie Antique', 6, NULL, 'Étude des philosophes antiques.', 6),
('ISBN0007', 'Éthique et Morale', 6, NULL, 'Réflexion sur l’éthique moderne.', 7),
('ISBN0008', 'Léonard de Vinci: Biographie', 7, NULL, 'Vie et œuvre de Léonard de Vinci.', 8),
('ISBN0009', 'Roman d’Horreur', 3, NULL, 'Histoire effrayante et captivante.', 9),
('ISBN0010', 'Enquête et Mystère', 3, NULL, 'Roman policier captivant.', 10),
('ISBN0011', 'Le Seigneur des Anneaux', 3, NULL, 'Épopée fantastique.', 11),
('ISBN0012', 'L’Odyssée', 3, NULL, 'Poème épique grec.', 12),
('ISBN0013', 'Les Aventures de Tom Sawyer', 3, NULL, 'Roman classique américain.', 13),
('ISBN0014', 'Les Misérables', 3, NULL, 'Roman historique français.', 14),
('ISBN0015', 'Gatsby le Magnifique', 3, NULL, 'Roman américain des années 20.', 15),
('ISBN0016', 'Cent Ans de Solitude', 3, NULL, 'Roman sud-américain.', 16),
('ISBN0017', 'L’Alchimiste', 3, NULL, 'Roman philosophique.', 17),
('ISBN0018', 'Le Vieil Homme et la Mer', 3, NULL, 'Roman court et puissant.', 18),
('ISBN0019', 'Da Vinci Code', 3, NULL, 'Thriller captivant.', 19),
('ISBN0020', 'Le Deuxième Sexe', 6, NULL, 'Essai féministe.', 20),
('ISBN0021', 'Introduction à C++', 1, NULL, 'Apprentissage du C++.', 1),
('ISBN0022', 'Bases de Données', 1, NULL, 'Cours sur les bases de données.', 2),
('ISBN0023', 'JavaScript Moderne', 1, NULL, 'Programmation web moderne.', 3),
('ISBN0024', 'Machine Learning', 1, NULL, 'Introduction au ML.', 4),
('ISBN0025', 'Intelligence Artificielle', 1, NULL, 'Concepts fondamentaux de l’IA.', 5),
('ISBN0026', 'Physique Quantique', 2, NULL, 'Introduction à la physique quantique.', 4),
('ISBN0027', 'Relativité Restreinte', 2, NULL, 'Théorie de la relativité.', 4),
('ISBN0028', 'Histoire de France', 4, NULL, 'Histoire complète de France.', 3),
('ISBN0029', 'Histoire du Monde', 4, NULL, 'Histoire mondiale.', 3),
('ISBN0030', 'Art et Créativité', 7, NULL, 'Introduction à l’art.', 8),
('ISBN0031', 'Philosophie Contemporaine', 6, NULL, 'Analyse philosophique moderne.', 6),
('ISBN0032', 'Mathématiques Appliquées', 5, NULL, 'Mathématiques dans le monde réel.', 5),
('ISBN0033', 'Poésie Française', 3, NULL, 'Recueil de poèmes.', 14),
('ISBN0034', 'Roman Historique', 3, NULL, 'Récit historique captivant.', 14),
('ISBN0035', 'Éthique et Technologie', 6, NULL, 'Réflexion sur l’éthique.', 7),
('ISBN0036', 'Biographie d’Einstein', 2, NULL, 'Vie et travaux d’Einstein.', 4),
('ISBN0037', 'Écriture Créative', 3, NULL, 'Guide pour écrire.', 9),
('ISBN0038', 'Science et Société', 2, NULL, 'Impact de la science.', 4),
('ISBN0039', 'Mathématiques Pures', 5, NULL, 'Cours approfondi.', 5),
('ISBN0040', 'Littérature Américaine', 3, NULL, 'Romans américains célèbres.', 13),
('ISBN0041', 'Technologie Moderne', 8, NULL, 'Les dernières technologies.', 20),
('ISBN0042', 'Informatique Avancée', 1, NULL, 'Cours avancé en informatique.', 1),
('ISBN0043', 'Histoire Antique', 4, NULL, 'Civilisations anciennes.', 12),
('ISBN0044', 'Arts Visuels', 7, NULL, 'Apprentissage des arts.', 8),
('ISBN0045', 'Roman Policier', 3, NULL, 'Histoire policière.', 10),
('ISBN0046', 'Science-fiction', 3, NULL, 'Romans de science-fiction.', 11),
('ISBN0047', 'Mathématiques Discrètes', 5, NULL, 'Théorie des graphes et combinatoire.', 5),
('ISBN0048', 'Informatique Théorique', 1, NULL, 'Algorithmes et complexité.', 3),
('ISBN0049', 'Philosophie Moderne', 6, NULL, 'Concepts philosophiques contemporains.', 6),
('ISBN0050', 'Littérature Contemporaine', 3, NULL, 'Romans récents.', 17),
('ISBN0051', 'Programmation Web', 1, NULL, 'HTML, CSS, JS.', 1),
('ISBN0052', 'Bases de Données Avancées', 1, NULL, 'SQL avancé.', 2),
('ISBN0053', 'Java Avancé', 1, NULL, 'Cours complet.', 2),
('ISBN0054', 'Python Avancé', 1, NULL, 'Cours avancé.', 1),
('ISBN0055', 'Art Contemporain', 7, NULL, 'Arts récents.', 8),
('ISBN0056', 'Histoire Européenne', 4, NULL, 'Histoire de l’Europe.', 3),
('ISBN0057', 'Littérature Française', 3, NULL, 'Romans français.', 14),
('ISBN0058', 'Mathématiques Financières', 5, NULL, 'Applications en finance.', 5),
('ISBN0059', 'Physique Classique', 2, NULL, 'Cours de physique.', 4),
('ISBN0060', 'Éthique et Société', 6, NULL, 'Réflexions sociétales.', 7),
('ISBN0061', 'Programmation Mobile', 1, NULL, 'Développement mobile.', 1),
('ISBN0062', 'IA et Machine Learning', 1, NULL, 'Intelligence artificielle.', 4),
('ISBN0063', 'Roman Historique Contemporain', 3, NULL, 'Récit historique.', 14),
('ISBN0064', 'Arts Modernes', 7, NULL, 'Exploration artistique.', 8),
('ISBN0065', 'Science et Technologie', 2, NULL, 'Découvertes scientifiques.', 4),
('ISBN0066', 'Mathématiques Avancées', 5, NULL, 'Cours avancé.', 5),
('ISBN0067', 'Littérature Américaine Moderne', 3, NULL, 'Romans récents.', 13),
('ISBN0068', 'Philosophie et Société', 6, NULL, 'Essai philosophique.', 6),
('ISBN0069', 'Poésie Contemporaine', 3, NULL, 'Recueil de poèmes récents.', 14),
('ISBN0070', 'Physique Expérimentale', 2, NULL, 'Cours et expériences.', 4),
('ISBN0071', 'Histoire du XXème siècle', 4, NULL, 'Événements historiques.', 3),
('ISBN0072', 'Technologie et Innovation', 8, NULL, 'Innovation et tech.', 20),
('ISBN0073', 'Programmation Fonctionnelle', 1, NULL, 'Langages fonctionnels.', 3),
('ISBN0074', 'Informatique Théorique Avancée', 1, NULL, 'Algorithmes et complexité.', 3),
('ISBN0075', 'Roman Policier Moderne', 3, NULL, 'Histoire policière.', 10),
('ISBN0076', 'Science et Société Contemporaine', 2, NULL, 'Science et société.', 4),
('ISBN0077', 'Mathématiques Discrètes Avancées', 5, NULL, 'Graphes et combinatoire.', 5),
('ISBN0078', 'Philosophie Contemporaine Avancée', 6, NULL, 'Concepts modernes.', 6),
('ISBN0079', 'Arts Visuels Contemporains', 7, NULL, 'Arts récents.', 8),
('ISBN0080', 'Technologie du Futur', 8, NULL, 'Technologies futures.', 20);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `ID_Message` int(11) NOT NULL,
  `ID_Etudiant` int(11) DEFAULT NULL,
  `Sens` enum('etudiant-vers-admin','admin-vers-etudiant') NOT NULL,
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
  `ID_Etudiant` int(11) DEFAULT NULL,
  `Statut` enum('en attente','acceptee','refusee','expirée') DEFAULT 'en attente',
  `ISBN` varchar(30) DEFAULT NULL,
  `Date_Reservation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `student`
--

CREATE TABLE `student` (
  `ID_Etudiant` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Date_inscription` datetime DEFAULT current_timestamp(),
  `Statut` enum('actif','restrict') DEFAULT 'actif',
  `Score` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student`
--

INSERT INTO `student` (`ID_Etudiant`, `Nom`, `Prenom`, `Email`, `Mot_de_passe`, `Date_inscription`, `Statut`, `Score`) VALUES
(1, 'El Amrani', 'Youssef', 'youssef1@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(2, 'Benzekri', 'Salma', 'salma2@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(3, 'Hajji', 'Omar', 'omar3@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(4, 'El Fassi', 'Aya', 'aya4@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(5, 'Bakir', 'Anas', 'anas5@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(6, 'Mansouri', 'Nada', 'nada6@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(7, 'Chraibi', 'Imane', 'imane7@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(8, 'Alaoui', 'Khalid', 'khalid8@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(9, 'Rami', 'Hamza', 'hamza9@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(10, 'El Idrissi', 'Sanae', 'sanae10@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(11, 'Tazi', 'Yassin', 'yassin11@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(12, 'Berrada', 'Hiba', 'hiba12@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(13, 'Jabri', 'Mohamed', 'mohamed13@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(14, 'Ziani', 'Rania', 'rania14@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(15, 'Ouazzani', 'Hicham', 'hicham15@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(16, 'Naciri', 'Sofia', 'sofia16@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(17, 'Kabbaj', 'Tarik', 'tarik17@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(18, 'Slaoui', 'Douaa', 'douaa18@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(19, 'Sabir', 'Nassim', 'nassim19@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(20, 'Benali', 'Rachid', 'rachid20@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(21, 'El Malki', 'Hanae', 'hanae21@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(22, 'Ghazi', 'Adil', 'adil22@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(23, 'Moutawakil', 'Sara', 'sara23@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(24, 'Hariri', 'Younes', 'younes24@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(25, 'Othmani', 'Zahra', 'zahra25@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(26, 'Fadili', 'Samir', 'samir26@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(27, 'Boulahcen', 'Meryem', 'meryem27@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(28, 'Joundi', 'Malak', 'malak28@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(29, 'Lamhadi', 'Walid', 'walid29@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(30, 'Kharbouch', 'Farah', 'farah30@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(31, 'Essalhi', 'Yahya', 'yahya31@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(32, 'Bourkia', 'Asmae', 'asmae32@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(33, 'Kaidi', 'Saad', 'saad33@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(34, 'El Khatib', 'Rita', 'rita34@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(35, 'Badaoui', 'Ismail', 'ismail35@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(36, 'Fakir', 'Nour', 'nour36@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(37, 'Touimi', 'Ilham', 'ilham37@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(38, 'El Ayoubi', 'Said', 'said38@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(39, 'Karimi', 'Othmane', 'othmane39@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(40, 'Sekkat', 'Wissal', 'wissal40@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(41, 'Barakat', 'Yassir', 'yassir41@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(42, 'Miri', 'Nadia', 'nadia42@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(43, 'Chakiri', 'Aziz', 'aziz43@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(44, 'Lazrak', 'Noura', 'noura44@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(45, 'Idrissi', 'Taha', 'taha45@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(46, 'Bennis', 'Ikram', 'ikram46@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(47, 'Mernissi', 'Sabah', 'sabah47@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(48, 'Ouahabi', 'Imrane', 'imrane48@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(49, 'Chennaoui', 'Lina', 'lina49@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100),
(50, 'Said', 'Ayoub', 'ayoub50@example.com', 'pass123', '2025-12-04 16:01:54', 'actif', 100);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID_Admin`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Index pour la table `auteur`
--
ALTER TABLE `auteur`
  ADD PRIMARY KEY (`ID_Auteur`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`ID_Categorie`);

--
-- Index pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD PRIMARY KEY (`ID_Emprunt`),
  ADD KEY `fk_emp_etudiant` (`ID_Etudiant`),
  ADD KEY `fk_emp_exemplaire` (`ID_Exemplaire`);

--
-- Index pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`ID_Evaluation`),
  ADD KEY `fk_eval_etudiant` (`ID_Etudiant`),
  ADD KEY `fk_eval_livre` (`ISBN`);

--
-- Index pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD PRIMARY KEY (`ID_Exemplaire`),
  ADD KEY `fk_exemplaire_livre` (`ISBN`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`ISBN`),
  ADD KEY `fk_livre_categorie` (`ID_Categorie`),
  ADD KEY `fk_livre_auteur` (`ID_Auteur`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`ID_Message`),
  ADD KEY `fk_msg_etudiant` (`ID_Etudiant`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ID_Reservation`),
  ADD KEY `fk_res_etudiant` (`ID_Etudiant`),
  ADD KEY `fk_res_livre` (`ISBN`);

--
-- Index pour la table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`ID_Etudiant`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID_Admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `auteur`
--
ALTER TABLE `auteur`
  MODIFY `ID_Auteur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `ID_Categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `emprunt`
--
ALTER TABLE `emprunt`
  MODIFY `ID_Emprunt` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `ID_Message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ID_Reservation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `student`
--
ALTER TABLE `student`
  MODIFY `ID_Etudiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD CONSTRAINT `fk_emp_etudiant` FOREIGN KEY (`ID_Etudiant`) REFERENCES `student` (`ID_Etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_emp_exemplaire` FOREIGN KEY (`ID_Exemplaire`) REFERENCES `exemplaire` (`ID_Exemplaire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `fk_eval_etudiant` FOREIGN KEY (`ID_Etudiant`) REFERENCES `student` (`ID_Etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eval_livre` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD CONSTRAINT `fk_exemplaire_livre` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`) ON DELETE CASCADE;

--
-- Contraintes pour la table `livre`
--
ALTER TABLE `livre`
  ADD CONSTRAINT `fk_livre_auteur` FOREIGN KEY (`ID_Auteur`) REFERENCES `auteur` (`ID_Auteur`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_livre_categorie` FOREIGN KEY (`ID_Categorie`) REFERENCES `categorie` (`ID_Categorie`) ON DELETE SET NULL;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_msg_etudiant` FOREIGN KEY (`ID_Etudiant`) REFERENCES `student` (`ID_Etudiant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_res_etudiant` FOREIGN KEY (`ID_Etudiant`) REFERENCES `student` (`ID_Etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_livre` FOREIGN KEY (`ISBN`) REFERENCES `livre` (`ISBN`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
