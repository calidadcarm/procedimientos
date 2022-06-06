<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */
 
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");
include_once (GLPI_ROOT."/plugins/procedimientos/inc/profile.class.php");

Session::checkRight("profile","w");
$prof=new PluginProcedimientosProfile();


//Save profile
if (isset ($_POST['UPDATE'])) {
	$prof->update($_POST);
	Html::back();
}

?>
