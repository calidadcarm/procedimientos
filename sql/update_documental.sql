CREATE TABLE IF NOT EXISTS `glpi_plugin_procedimientos_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documentcategories_id` int(11) NOT NULL DEFAULT '0',
  `documents_id` int(11) NOT NULL DEFAULT '0',
  `itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `items_id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentcategories_id` (`documentcategories_id`),
  KEY `documents_id` (`documents_id`),
  KEY `itemtype` (`itemtype`),
  KEY `items_id` (`items_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;