ALTER TABLE `tokens` ADD `token_type` VARCHAR(255) NULL AFTER `token_use`;
UPDATE `tokens` SET `token_type` = "report";