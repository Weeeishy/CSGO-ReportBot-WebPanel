ALTER TABLE `reported_list` ADD `ban_date` TIMESTAMP NULL DEFAULT NULL AFTER `vac`;
UPDATE  `config` SET  `current_version` =  '1.4.4';