ALTER TABLE `glpi_plugin_procedimientos_validacions` CHANGE COLUMN `users_id` `groups_id` INT(11) NOT NULL DEFAULT '0' COMMENT '' ;
INSERT INTO `glpi_plugin_procedimientos_tipoaccions` VALUES (5,0,1,'Validación','');

ALTER TABLE `glpi_plugin_procedimientos_condicions` 
ADD COLUMN `tag_0` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'Si' COMMENT '' AFTER `way_no`,
ADD COLUMN `tag_1` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'No' COMMENT '' AFTER `tag_0`;
UPDATE `glpi_plugin_procedimientos_condicions` SET `tag_0`='Si', `tag_1`='No';

ALTER TABLE `glpi_plugin_procedimientos_validacions` DROP INDEX `unicity`;