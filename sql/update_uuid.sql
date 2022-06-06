ALTER TABLE `glpi_plugin_procedimientos_accions` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_condicions` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_marcadors` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_procedimientos` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_procedimientos_items` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_tipoaccions` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_saltos` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

ALTER TABLE `glpi_plugin_procedimientos_links` 
ADD COLUMN `uuid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL; 

UPDATE `glpi_plugin_procedimientos_marcadors` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000001' WHERE `id`='1';
UPDATE `glpi_plugin_procedimientos_marcadors` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000002' WHERE `id`='2';
UPDATE `glpi_plugin_procedimientos_marcadors` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000003' WHERE `id`='3';

UPDATE `glpi_plugin_procedimientos_tipoaccions` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000004' WHERE `id`='1';
UPDATE `glpi_plugin_procedimientos_tipoaccions` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000005' WHERE `id`='2';
UPDATE `glpi_plugin_procedimientos_tipoaccions` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000006' WHERE `id`='3';
UPDATE `glpi_plugin_procedimientos_tipoaccions` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000007' WHERE `id`='4';
UPDATE `glpi_plugin_procedimientos_tipoaccions` SET `uuid`='c0dff0d6-9e4abb40-5a61e7e35e2256.00000008' WHERE `id`='5';

SET SQL_SAFE_UPDATES = 0;
update glpi_plugin_procedimientos_procedimientos set uuid=REPLACE('c0dff0d6-9e4abb40-5a5f6b644a89a1.85276220','.85276220',concat('.000',FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*100))) where uuid is null;
update glpi_plugin_procedimientos_links set uuid=REPLACE('c0dff0d6-9e4abb40-5a5f6b644a89a2.85276220','.85276220',concat('.000',FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*100))) where uuid is null;
update glpi_plugin_procedimientos_accions set uuid=REPLACE('c0dff0d6-9e4abb40-5a5f6b644a89a3.85276220','.85276220',concat('.000',FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*100))) where uuid is null;
update glpi_plugin_procedimientos_condicions set uuid=REPLACE('c0dff0d6-9e4abb40-5a5f6b644a89a4.85276220','.85276220',concat('.000',FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*100))) where uuid is null;
update glpi_plugin_procedimientos_procedimientos_items set uuid=REPLACE('c0dff0d6-9e4abb40-5a5f6b644a89a5.85276220','.85276220',concat('.000',FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*100))) where uuid is null;
update glpi_plugin_procedimientos_saltos set uuid=REPLACE('c0dff0d6-9e4abb40-5a5f6b644a89a6.85276220','.85276220',concat('.000',FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*10),FLOOR(RAND()*100))) where uuid is null;