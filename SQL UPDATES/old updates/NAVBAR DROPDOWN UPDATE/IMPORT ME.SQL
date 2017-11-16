ALTER TABLE `nav_tabs` ADD `is_dropdown` BOOLEAN NULL AFTER `nav_order`;
ALTER TABLE `nav_tabs` ADD `is_dropdown_parent` BOOLEAN NULL AFTER `is_dropdown`;
ALTER TABLE `nav_tabs` ADD `is_dropdown_item` BOOLEAN NULL AFTER `is_dropdown_parent`;
ALTER TABLE `nav_tabs` ADD `dropdown_parentid` INT(11) NULL AFTER `is_dropdown_item`;
UPDATE `nav_tabs` SET `is_dropdown` = 0, `is_dropdown_item` = 0, `dropdown_parentid` = 0, `is_dropdown_parent` = 0;
