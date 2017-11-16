ALTER TABLE `reported_list` ADD `inventory_value` VARCHAR(255) NOT NULL AFTER `steamid`;
UPDATE `reported_list` SET `inventory_value` = 'Unknown';