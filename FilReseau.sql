-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 06 Avril 2017 à 14:28
-- Version du serveur :  5.7.17-0ubuntu0.16.04.1
-- Version de PHP :  7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `FilReseau`
--

-- --------------------------------------------------------

--
-- Structure de la table `assurePermanence`
--

CREATE TABLE `assurePermanence` (
  `expert_id` int(8) NOT NULL,
  `permanence_id` int(8) NOT NULL,
  `disponibilite` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande`
--

CREATE TABLE `demande` (
  `id` int(8) NOT NULL,
  `etat` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `estArchive` tinyint(1) DEFAULT NULL,
  `manager` int(8) NOT NULL,
  `theme` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(8) NOT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auteur` int(8) NOT NULL,
  `demande` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permanence`
--

CREATE TABLE `permanence` (
  `id` int(8) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(8) NOT NULL,
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse` text COLLATE utf8_unicode_ci NOT NULL,
  `expert` int(8) NOT NULL,
  `datePost` date DEFAULT NULL,
  `estPublie` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role_user`
--

CREATE TABLE `role_user` (
  `id` int(1) NOT NULL,
  `libelle` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `site`
--

CREATE TABLE `site` (
  `id` int(8) NOT NULL,
  `libelle` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

CREATE TABLE `theme` (
  `id` int(4) NOT NULL,
  `libelle` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `unite`
--

CREATE TABLE `unite` (
  `id` int(8) NOT NULL,
  `libelle` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(8) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` int(1) DEFAULT NULL,
  `site` int(8) DEFAULT NULL,
  `unite` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `assurePermanence`
--
ALTER TABLE `assurePermanence`
  ADD PRIMARY KEY (`expert_id`,`permanence_id`),
  ADD KEY `FK_PERMANENCE_EXPERT` (`permanence_id`),
  ADD KEY `expert_id` (`expert_id`);

--
-- Index pour la table `demande`
--
ALTER TABLE `demande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_DEMANDE_MANAGER` (`manager`),
  ADD KEY `FK_DEMANDE_THEME` (`theme`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_MESSAGE_AUTEUR` (`auteur`),
  ADD KEY `FK_MESSAGE_DEMANDE` (`demande`);

--
-- Index pour la table `permanence`
--
ALTER TABLE `permanence`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_POST_EXPERT` (`expert`);

--
-- Index pour la table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `unite`
--
ALTER TABLE `unite`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `FK_ROLE_USER` (`role`),
  ADD KEY `FK_SITE_MANAGER` (`site`),
  ADD KEY `FK_UNITE_EXPERT` (`unite`);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `assurePermanence`
--
ALTER TABLE `assurePermanence`
  ADD CONSTRAINT `FK_EXPERT_PERMANENCE` FOREIGN KEY (`expert_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_PERMANENCE_EXPERT` FOREIGN KEY (`permanence_id`) REFERENCES `permanence` (`id`);

--
-- Contraintes pour la table `demande`
--
ALTER TABLE `demande`
  ADD CONSTRAINT `FK_DEMANDE_MANAGER` FOREIGN KEY (`manager`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_DEMANDE_THEME` FOREIGN KEY (`theme`) REFERENCES `theme` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_MESSAGE_AUTEUR` FOREIGN KEY (`auteur`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_MESSAGE_DEMANDE` FOREIGN KEY (`demande`) REFERENCES `demande` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_POST_EXPERT` FOREIGN KEY (`expert`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_ROLE_USER` FOREIGN KEY (`role`) REFERENCES `role_user` (`id`),
  ADD CONSTRAINT `FK_SITE_MANAGER` FOREIGN KEY (`site`) REFERENCES `site` (`id`),
  ADD CONSTRAINT `FK_UNITE_EXPERT` FOREIGN KEY (`unite`) REFERENCES `unite` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
