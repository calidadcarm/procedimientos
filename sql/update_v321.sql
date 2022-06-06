ALTER TABLE `glpi_plugin_procedimientos_tareas` 
ADD COLUMN `users_id_tech` INT(11) NOT NULL DEFAULT '0' AFTER `taskcategories_id`,
ADD COLUMN `groups_id_tech` INT(11) NOT NULL DEFAULT '0' AFTER `users_id_tech`,
ADD COLUMN `is_private` TINYINT(1) NOT NULL DEFAULT '0' AFTER `groups_id_tech`,
ADD COLUMN `state` INT(11) NOT NULL DEFAULT '1' AFTER `is_private`,
ADD COLUMN `tasktemplates_id` INT(11) NOT NULL DEFAULT '0' AFTER `state`;

ALTER TABLE `glpi_plugin_procedimientos_seguimientos` 
ADD COLUMN `filename` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT 'for display and transfert' AFTER `followuptypes_id`,
ADD COLUMN `tag` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL AFTER `filename`;

ALTER TABLE `glpi_plugin_procedimientos_tareas` 
CHANGE COLUMN `is_private` `is_private` TINYINT(1) NOT NULL DEFAULT '1' ;

ALTER TABLE `glpi_plugin_procedimientos_escalados` 
ADD COLUMN `suppliers_id` INT(11) NULL DEFAULT NULL AFTER `groups_id_observ`;

