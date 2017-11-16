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
  
  