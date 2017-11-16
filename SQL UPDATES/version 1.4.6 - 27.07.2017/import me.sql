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
  
UPDATE  `config` SET  `current_version` =  '1.4.6';