<?php

/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */

include ("../../../inc/includes.php");

Html::header(__('Procedimientos', 'Procedimientos'), $_SERVER['PHP_SELF'] ,"config", "PluginProcedimientosConfig");

// Check if plugin is activated...
$plugin = new Plugin();
if(!$plugin->isInstalled('procedimientos') || !$plugin->isActivated('procedimientos')) {
   Html::displayNotFoundError();
}

if (Session::haveRight('plugin_procedimientos', READ)) {
	PluginProcedimientosConfig::showConfigPage();
	Html::footer();
} else {
	Html::displayRightError();
}

?>