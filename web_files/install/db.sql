-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Ven 30 Juin 2017 à 17:54
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
-- Structure de la table `banned_users`
--

CREATE TABLE `banned_users` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `expiration` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commended_list`
--

CREATE TABLE `commended_list` (
  `id` int(11) NOT NULL,
  `commendbot_id` int(11) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `steamid` varchar(255) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `commendedby_userid` int(11) DEFAULT NULL,
  `commendedby_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------


--
-- Structure de la table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `current_version` varchar(255) NOT NULL,
  `licence_key` varchar(255) NOT NULL,
  `steam_api_key` varchar(255) NOT NULL,
  `website_navtitle` varchar(255) NOT NULL,
  `website_title` varchar(255) NOT NULL,
  `captcha_secret_key` varchar(255) NOT NULL,
  `captcha_website_key` varchar(255) NOT NULL,
  `reportbot_number` int(11) NOT NULL,
  `commendbot_number` int(11) NOT NULL,
  `log_prefix` varchar(255) NOT NULL,
  `report_path` varchar(255) NOT NULL,
  `report_log_path` varchar(255) NOT NULL,
  `commend_path` varchar(255) NOT NULL,
  `commend_log_path` varchar(255) NOT NULL,
  `report_timer` int(11) NOT NULL,
  `commend_timer` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);


--
-- AUTO_INCREMENT pour la table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Structure de la table `faq`
--


CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `homepage`
--

CREATE TABLE `homepage` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `language_name` varchar(255) DEFAULT NULL,
  `lang_code` varchar(255) DEFAULT NULL,
  `lang_icon` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `language`
--

INSERT INTO `language` (`id`, `language_name`, `lang_code`, `lang_icon`) VALUES
(7, 'Français', 'fr', 'fr'),
(8, 'English', 'en', 'en');

-- --------------------------------------------------------

--
-- Structure de la table `navbar`
--

CREATE TABLE `navbar` (
  `id` int(11) NOT NULL,
  `parentid` int(11) NOT NULL DEFAULT '0',
  `display_order` varchar(255) NOT NULL DEFAULT '1',
  `type` varchar(255) NOT NULL DEFAULT 'item',
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL DEFAULT '#',
  `text_color` varchar(255) NOT NULL DEFAULT 'white',
  `access_level` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `navbar`
--

INSERT INTO `navbar` (`id`, `parentid`, `display_order`, `type`, `name`, `link`, `text_color`, `access_level`) VALUES
(1, 0, '1', 'item', 'Home', 'index.php', 'white', 'all'),
(2, 0, '2', 'dropdown_parent', 'Action', '', 'white', 'all'),
(3, 2, '1', 'item', 'Report', 'report.php', 'white', 'all'),
(4, 2, '2', 'item', 'Commend', 'commend.php', 'white', 'all'),
(5, 0, '3', 'dropdown_parent', 'Tools', '', 'white', 'all'),
(6, 5, '2', 'item', 'SteamID Converter', 'converter.php', 'white', 'all'),
(7, 5, '2', 'item', 'Whitelist', 'whitelist.php', 'white', 'all'),
(8, 0, '4', 'dropdown_parent', 'Lists', '', 'white', 'all'),
(9, 8, '1', 'item', 'Banned Players', 'banned.php', 'white', 'all'),
(10, 8, '2', 'item', 'Whitelisted', 'whitelisted.php', 'white', 'all'),
(11, 0, '5', 'dropdown_parent', 'Login / Register', '', 'white', 'non_logged'),
(12, 11, '', 'item', 'Login', 'login.php', 'white', 'all'),
(13, 11, '3', 'item', 'Register', 'register.php', 'white', 'all'),
(14, 0, '6', 'dropdown_parent', 'My Account', '', 'white', 'logged'),
(15, 14, '', 'item', 'Dashboard', 'account.php', 'white', 'all'),
(16, 14, '', 'item', 'Rewards', 'rewards.php', 'white', 'all'),
(17, 14, '', 'item', 'Logout', '?logout', 'white', 'all'),
(18, 0, '7', 'item', 'FAQ', 'faq.php', 'red', 'all'),
(19, 0, '8', 'item', 'Admin', 'admin', 'white', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `addedby_uid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_order` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` varchar(255) NOT NULL,
  `product_currency` varchar(255) NOT NULL,
  `product_url` varchar(255) NOT NULL,
  `product_description` text,
  `product_image_url` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `products_categories`
--

CREATE TABLE `products_categories` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `reported_list`
--

CREATE TABLE `reported_list` (
  `id` int(11) NOT NULL,
  `reportbot_id` varchar(11) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `steamid` varchar(255) NOT NULL,
  `inventory_value` varchar(255) NOT NULL,
  `ow` varchar(255) NOT NULL DEFAULT 'false',
  `vac` varchar(255) NOT NULL DEFAULT 'false',
  `ip` varchar(255) DEFAULT NULL,
  `reportedby_userid` int(11) DEFAULT NULL,
  `reportedby_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `reported_list` ADD `ban_date` TIMESTAMP NULL DEFAULT NULL AFTER `vac`;


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
  `pointsper_commend` float DEFAULT '0',
  `steamgroup_url` varchar(255) DEFAULT NULL,
  `steamgroup_points` float DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `rewards_config`
--

INSERT INTO `rewards_config` (`id`, `tag`, `tag_minconsecutivedays`, `tag_pointsperday`, `pointsper_report`, `pointsper_commend`) VALUES
(1, 'Ellow', 5, 1, 0.05, 0.5);

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
  `uip` varchar(255) DEFAULT NULL,
  `points` float NOT NULL DEFAULT '0',
  `joined_steamgroup` tinyint(1) NOT NULL DEFAULT '0',
  `tag_datebegin` date NOT NULL,
  `tag_consecutivedays` int(11) NOT NULL DEFAULT '0',
  `enable_notification` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `support_categories`
--

CREATE TABLE `support_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `label_type` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `message` longblob NOT NULL,
  `date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` longblob NOT NULL,
  `type` varchar(255) NOT NULL,
  `state` int(11) DEFAULT NULL,
  `admin_viewed` tinyint(1) DEFAULT NULL,
  `user_viewed` tinyint(1) DEFAULT NULL,
  `date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `theme_name` varchar(255) NOT NULL,
  `use_this_theme` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `themes`
--

INSERT INTO `themes` (`id`, `theme_name`, `use_this_theme`) VALUES
(1, 'cerulean', 0),
(2, 'cosmo', 1),
(3, 'cyborg', 0),
(4, 'darkly', 0),
(5, 'default', 0),
(6, 'flatly', 0),
(7, 'journal', 0),
(8, 'lumen', 0),
(9, 'paper', 0),
(10, 'readable', 0),
(11, 'sandstone', 0),
(12, 'simplex', 0),
(13, 'slate', 0),
(14, 'solar', 0),
(15, 'spacelab', 0),
(16, 'superhero', 0),
(17, 'united', 0),
(18, 'yeti', 0);

-- --------------------------------------------------------

--
-- Structure de la table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_use` int(11) NOT NULL,
  `token_type` varchar(255) DEFAULT NULL,
  `token_generation` timestamp NOT NULL,
  `token_ownerid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `tokens_activation`
--

CREATE TABLE `tokens_activation` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `uip` varchar(255) NOT NULL,
  `date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `steamid` varchar(17) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_subscribed` tinyint(1) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `whitelist`
--

CREATE TABLE `whitelist` (
  `id` int(11) NOT NULL,
  `steamid` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `added_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `referral_codes` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `referral_codes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `referral_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  
CREATE TABLE `referral_users` (
  `id` int(11) NOT NULL,
  `owner_userid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `referral_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `referral_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
--
-- Index pour les tables exportées
--

--
-- Index pour la table `banned_users`
--
ALTER TABLE `banned_users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commended_list`
--
ALTER TABLE `commended_list`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `homepage`
--
ALTER TABLE `homepage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `navbar`
--
ALTER TABLE `navbar`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `products_categories`
--
ALTER TABLE `products_categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reported_list`
--
ALTER TABLE `reported_list`
  ADD PRIMARY KEY (`id`);

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
-- Index pour la table `support_categories`
--
ALTER TABLE `support_categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tokens_activation`
--
ALTER TABLE `tokens_activation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `whitelist`
--
ALTER TABLE `whitelist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `banned_users`
--
ALTER TABLE `banned_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `commended_list`
--
ALTER TABLE `commended_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT pour la table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `homepage`
--
ALTER TABLE `homepage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `navbar`
--
ALTER TABLE `navbar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT pour la table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `products_categories`
--
ALTER TABLE `products_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `reported_list`
--
ALTER TABLE `reported_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table `support_categories`
--
ALTER TABLE `support_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT pour la table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;
--
-- AUTO_INCREMENT pour la table `tokens_activation`
--
ALTER TABLE `tokens_activation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table `whitelist`
--
ALTER TABLE `whitelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;