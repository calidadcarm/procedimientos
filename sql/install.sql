CREATE TABLE `glpi_plugin_procedimientos_accions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `date_mod` datetime DEFAULT NULL,
  `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `plugin_procedimientos_tipoaccions_id` tinyint(1) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`),
  KEY `date_mod` (`date_mod`),
  KEY `type` (`plugin_procedimientos_tipoaccions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_validacions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_accions_id` int(11) NOT NULL,
  `groups_id` int(11) NOT NULL DEFAULT '0',
  `users_id_validate` int(11) NOT NULL DEFAULT '0',
  `comment_submission` text COLLATE utf8_unicode_ci,
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validador` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `groups_id` (`groups_id`),
  KEY `users_id_validate` (`users_id_validate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_updatetickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_accions_id` int(11) NOT NULL,
  `requesttypes_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `itilcategories_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '1',
  `slts_ttr_id` int(11) NOT NULL DEFAULT '0',
  `solutiontemplates_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_accions_id`),
  KEY `requesttypes_id` (`requesttypes_id`),
  KEY `status` (`status`),
  KEY `itilcategories_id` (`itilcategories_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_tipoaccions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` text COLLATE utf8_unicode_ci,
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_procedimientos_tipoaccions` (`id`,`entities_id`,`is_recursive`,`name`,`comment`,`uuid`) VALUES (1,0,1,'Tarea','','c0dff0d6-9e4abb40-5a61e7e35e2256.00000003');
INSERT INTO `glpi_plugin_procedimientos_tipoaccions` (`id`,`entities_id`,`is_recursive`,`name`,`comment`,`uuid`) VALUES (2,0,1,'Escalado','','c0dff0d6-9e4abb40-5a61e7e35e2256.00000004');
INSERT INTO `glpi_plugin_procedimientos_tipoaccions` (`id`,`entities_id`,`is_recursive`,`name`,`comment`,`uuid`) VALUES (3,0,1,'Modificación ticket','','c0dff0d6-9e4abb40-5a61e7e35e2256.00000005');
INSERT INTO `glpi_plugin_procedimientos_tipoaccions` (`id`,`entities_id`,`is_recursive`,`name`,`comment`,`uuid`) VALUES (4,0,1,'Seguimiento','','c0dff0d6-9e4abb40-5a61e7e35e2256.00000006');
INSERT INTO `glpi_plugin_procedimientos_tipoaccions` (`id`,`entities_id`,`is_recursive`,`name`,`comment`,`uuid`) VALUES (5,0,1,'Validación','','c0dff0d6-9e4abb40-5a61e7e35e2256.00000007');





CREATE TABLE `glpi_plugin_procedimientos_tareas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_accions_id` int(11) NOT NULL,
  `taskcategories_id` int(11) DEFAULT NULL,
  `users_id_tech` int(11) NOT NULL DEFAULT '0',
  `groups_id_tech` int(11) NOT NULL DEFAULT '0',
  `is_private` tinyint(1) NOT NULL DEFAULT '1',
  `state` int(11) NOT NULL DEFAULT '1',
  `tasktemplates_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_accions_id`),
  KEY `taskcategories_id` (`taskcategories_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_seguimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_accions_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL DEFAULT '0',
  `content` longtext COLLATE utf8_unicode_ci,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `requesttypes_id` int(11) DEFAULT '0',
  `followuptypes_id` int(11) DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'for display and transfert',
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_accions_id`),
  KEY `users_id` (`users_id`),
  KEY `is_private` (`is_private`),
  KEY `requesttypes_id` (`requesttypes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_saltos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `date_mod` datetime DEFAULT NULL,
  `goto` tinyint(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `goto_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `date_mod` (`date_mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_procedimientos_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL DEFAULT '0',
  `tickets_id` int(11) NOT NULL DEFAULT '0',
  `line` int(11) NOT NULL DEFAULT '0',
  `itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `items_id` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `instancia_id` int(11) DEFAULT NULL,
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_procedimientos_id`,`tickets_id`,`line`),
  KEY `tickets_id` (`tickets_id`),
  KEY `plugin_procedimientos_procedimientos_id` (`plugin_procedimientos_procedimientos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_procedimientos_ticketrecurrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL DEFAULT '0',
  `ticketrecurrents_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_procedimientos_id`,`ticketrecurrents_id`),
  KEY `ticketrecurrents_id` (`ticketrecurrents_id`),
  KEY `plugin_procedimientos_procedimientos_id` (`plugin_procedimientos_procedimientos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_procedimientos_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL DEFAULT '0',
  `date_mod` datetime DEFAULT NULL,
  `line` int(11) NOT NULL DEFAULT '0',
  `itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `items_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`id`),
  KEY `plugin_procedimientos_procedimientos_id` (`plugin_procedimientos_procedimientos_id`),
  KEY `date_mod` (`date_mod`),
  KEY `line` (`line`),
  KEY `itemtype` (`itemtype`),
  KEY `items_id` (`items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_procedimientos_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL DEFAULT '0',
  `groups_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_procedimientos_id`,`groups_id`),
  KEY `groups_id` (`groups_id`),
  KEY `plugin_procedimientos_procedimientos_id` (`plugin_procedimientos_procedimientos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_procedimientos_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL DEFAULT '0',
  `plugin_formcreator_forms_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_procedimientos_id`,`plugin_formcreator_forms_id`),
  KEY `plugin_formcreator_forms_id` (`plugin_formcreator_forms_id`),
  KEY `plugin_procedimientos_procedimientos_id` (`plugin_procedimientos_procedimientos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_procedimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `date_mod` datetime DEFAULT NULL,
  `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `date_mod` (`date_mod`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_marcadors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` text COLLATE utf8_unicode_ci,
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `glpi_plugin_procedimientos_marcadors` (`id`,`name`,`comment`,`uuid`) VALUES (1,'Inicio','Iniciar procedimiento','c0dff0d6-9e4abb40-5a61e7e35e2256.00000001');
INSERT INTO `glpi_plugin_procedimientos_marcadors` (`id`,`name`,`comment`,`uuid`) VALUES (2,'Fin','Finalizar procedimiento','c0dff0d6-9e4abb40-5a61e7e35e2256.00000002');



CREATE TABLE `glpi_plugin_procedimientos_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_mod` datetime DEFAULT NULL,
  `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `date_mod` (`date_mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_escalados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_accions_id` int(11) NOT NULL,
  `users_id_asignado` int(11) DEFAULT NULL,
  `groups_id_asignado` int(11) DEFAULT NULL,
  `users_id_observ` int(11) DEFAULT NULL,
  `groups_id_observ` int(11) DEFAULT NULL,
  `suppliers_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_procedimientos_accions_id`),
  KEY `users_id_asignado` (`users_id_asignado`),
  KEY ` group_id_asignado` (`groups_id_asignado`),
  KEY ` users_id_observ` (`users_id_observ`),
  KEY ` group_id_observ` (`groups_id_observ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documentcategories_id` int(11) NOT NULL DEFAULT '0',
  `documents_id` int(11) NOT NULL DEFAULT '0',
  `itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `items_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentcategories_id` (`documentcategories_id`),
  KEY `documents_id` (`documents_id`),
  KEY `itemtype` (`itemtype`),
  KEY `items_id` (`items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_condicions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_procedimientos_procedimientos_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `date_mod` datetime DEFAULT NULL,
  `way_yes` tinyint(11) NOT NULL DEFAULT '0',
  `way_no` tinyint(11) NOT NULL DEFAULT '0',
  `tag_0` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Si',
  `tag_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_1` int(11) NOT NULL DEFAULT '0',
  `tag_id_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_2` int(11) NOT NULL DEFAULT '0',
  `tag_id_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_3` int(11) NOT NULL DEFAULT '0',
  `tag_id_3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_4` int(11) NOT NULL DEFAULT '0',
  `tag_id_4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_5` int(11) NOT NULL DEFAULT '0',
  `tag_id_5` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `line_id_1` tinyint(11) NOT NULL DEFAULT '0',
  `line_id_2` tinyint(11) NOT NULL DEFAULT '0',
  `line_id_3` tinyint(11) NOT NULL DEFAULT '0',
  `line_id_4` tinyint(11) NOT NULL DEFAULT '0',
  `line_id_5` tinyint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `date_mod` (`date_mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_procedimientos_accions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `date_mod` datetime DEFAULT NULL,
  `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `plugin_procedimientos_tipoaccions_id` tinyint(1) NOT NULL DEFAULT '0',
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`),
  KEY `date_mod` (`date_mod`),
  KEY `type` (`plugin_procedimientos_tipoaccions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


