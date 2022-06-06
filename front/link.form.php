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
                       

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";

$PluginProcedimientosLink = new PluginProcedimientosLink();
//print_r($_POST);

if (isset($_POST["add"])) {	
        $newID=$PluginProcedimientosLink->add($PluginProcedimientosLink->prepareInputForAdd($_POST));
    Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["delete"])) {

	$PluginProcedimientosLink->delete($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginProcedimientosLink'));
	
} else if (isset($_POST["purge"])) {

	$PluginProcedimientosLink->delete($_POST,1);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginProcedimientosLink'));
	
} else if (isset($_POST["update"])) {
	$PluginProcedimientosLink->update($_POST);
	Html::redirect($_SERVER['HTTP_REFERER']);
 }                                          
  else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "link"
   );
    if (Session::haveRight('plugin_procedimientos',READ)) {
		$PluginProcedimientosLink ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}     
}
?>