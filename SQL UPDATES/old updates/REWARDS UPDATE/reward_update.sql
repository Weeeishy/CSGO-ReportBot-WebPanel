-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Lun 05 Juin 2017 à 09:02
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `report_bot`
--

-- --------------------------------------------------------

--
-- Structure de la table `rewards_config`
--

CREATE TABLE `rewards_config` (
  `id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `tag_minconsecutivedays` int(11) NOT NULL DEFAULT '0',
  `tag_pointsperday` float NOT NULL DEFAULT '0',
  `pointsper_report` float NOT NULL DEFAULT '0',
  `pointsper_commend` float DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `rewards_config`
--

INSERT INTO `rewards_config` (`id`, `tag`, `tag_minconsecutivedays`, `tag_pointsperday`, `pointsper_report`, `pointsper_commend`) VALUES
(1, 'Your Tag', 5, 1, 0.05, 0.5);

-- --------------------------------------------------------

--
-- Structure de la table `rewards_history`
--

CREATE TABLE `rewards_history` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `generated_token` varchar(255) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  `uses` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `description` longtext,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `rewards_list`
--

CREATE TABLE `rewards_list` (
  `id` int(11) NOT NULL,
  `points_cost` int(11) NOT NULL DEFAULT '1',
  `reward_type` varchar(255) NOT NULL,
  `reward_uses` int(11) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `rewards_users`
--

CREATE TABLE `rewards_users` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `points` float NOT NULL DEFAULT '0',
  `tag_datebegin` date NOT NULL,
  `tag_consecutivedays` int(11) NOT NULL DEFAULT '0',
  `enable_notification` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `rewards_config`
--
ALTER TABLE `rewards_config`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rewards_history`
--
ALTER TABLE `rewards_history`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rewards_list`
--
ALTER TABLE `rewards_list`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rewards_users`
--
ALTER TABLE `rewards_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `rewards_config`
--
ALTER TABLE `rewards_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `rewards_history`
--
ALTER TABLE `rewards_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT pour la table `rewards_list`
--
ALTER TABLE `rewards_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `rewards_users`
--
ALTER TABLE `rewards_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;