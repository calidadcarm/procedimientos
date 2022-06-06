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
                   
include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");				   

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";


$PluginProcedimientosProcedimiento_Group = new PluginProcedimientosProcedimiento_Group();

 if (isset($_POST["addgroup"])) {   
   if ($_POST['groups_id']>0) {
   
   	  if (plugin_procedimientos_existeGrupo($_POST['plugin_procedimientos_procedimientos_id'],$_POST['groups_id']))
	  { // si ESTE GRUPO YA existe DENTRO DE NUESTRO PROCEDIMIENTO
			
		Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>Grupo </font><FONT color='#a42090'> %s </font> <FONT color='red'>duplicado en el procedimiento</font></STRONG>.<br>", "procedimiento"),
                                                  $_POST['groups_id']));
			
	  } else {
   
       $PluginProcedimientosProcedimiento_Group ->addItem($_POST);
	   
		    }
   }
   Html::back();  
} else if (isset($_POST["elimina"])){
	$query= "delete from glpi_plugin_procedimientos_procedimientos_groups where plugin_procedimientos_procedimientos_id=".$_POST["plugin_procedimientos_procedimientos_id"]."
			 and groups_id=".$_POST["elimina"];
    $DB->query($query);
	Html::back();

} else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
 );
			   
   $PluginProcedimientosProcedimiento_Group->display($_GET["id"]);
   Html::footer();
   
}
?>
