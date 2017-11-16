ALTER TABLE `commended_list` ADD `commendedby_token` VARCHAR(255) NULL AFTER `commendedby_userid`;
ALTER TABLE `reported_list` ADD `reportedby_token` VARCHAR(255) NULL AFTER `reportedby_userid`;