ALTER TABLE `rewards_config` ADD `steamgroup_url` VARCHAR(255) NULL AFTER `pointsper_commend`;
ALTER TABLE `rewards_config` ADD `steamgroup_points` FLOAT NOT NULL DEFAULT '0' AFTER `steamgroup_url`;
ALTER TABLE `rewards_users` ADD `uip` VARCHAR(255) NULL DEFAULT NULL AFTER `uid`;
ALTER TABLE `rewards_users` ADD `joined_steamgroup` BOOLEAN NOT NULL DEFAULT FALSE AFTER `points`;