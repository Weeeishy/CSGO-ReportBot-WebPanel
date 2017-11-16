DROP TABLE IF EXISTS nav_tabs;
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

--
-- Index pour les tables exportées
--

--
-- Index pour la table `navbar`
--
ALTER TABLE `navbar`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `navbar`
--
ALTER TABLE `navbar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
