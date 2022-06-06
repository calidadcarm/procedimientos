<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Noviembre 2016

   ----------------------------------------------------------
 */

include ("../../../inc/includes.php");

Html::header(__('link', 'link'), $_SERVER['PHP_SELF'] ,"config", "PluginProcedimientosConfig", "link");

// Check if plugin is activated...
$plugin = new Plugin();
if(!$plugin->isInstalled('procedimientos') || !$plugin->isActivated('procedimientos')) {
   Html::displayNotFoundError();
}

if (Session::haveRight('plugin_procedimientos',READ)) {
	Search::Show('PluginProcedimientosLink');
	Html::footer();
} else {
	Html::displayRightError();
}

?>