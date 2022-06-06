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
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();
 
if (isset($_POST)){
	
	include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");
	
	global $DB;
	
				$select = "DELETE FROM `glpi_plugin_procedimientos_documents` WHERE `id`=".$_POST["id"];
				$result = $DB->query($select );
				
echo "OK";
	 
} else {

   die("Sorry. You can't access directly to this file");
   
}	



?>