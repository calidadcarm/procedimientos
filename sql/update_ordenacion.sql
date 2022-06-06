ALTER TABLE glpi_plugin_procedimientos_condicions ADD id_1 int(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD tag_id_1 varchar(255) COLLATE utf8_unicode_ci NULL;
ALTER TABLE glpi_plugin_procedimientos_condicions ADD id_2 int(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD tag_id_2 varchar(255) COLLATE utf8_unicode_ci NULL;
ALTER TABLE glpi_plugin_procedimientos_condicions ADD id_3 int(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD tag_id_3 varchar(255) COLLATE utf8_unicode_ci NULL;
ALTER TABLE glpi_plugin_procedimientos_condicions ADD id_4 int(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD tag_id_4 varchar(255) COLLATE utf8_unicode_ci NULL;
ALTER TABLE glpi_plugin_procedimientos_condicions ADD id_5 int(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD tag_id_5 varchar(255) COLLATE utf8_unicode_ci NULL;

ALTER TABLE `glpi_plugin_procedimientos_procedimientos_items` 
DROP INDEX `unicity` ,
ADD UNIQUE INDEX `unicity` (`id` ASC);
