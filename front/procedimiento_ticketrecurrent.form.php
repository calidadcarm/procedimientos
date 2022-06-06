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
                       

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";


$PluginProcedimientosProcedimiento_TicketRecurrent = new PluginProcedimientosProcedimiento_TicketRecurrent();

 if (isset($_POST["addticketrecurrent"])) {   
   if ($_POST['ticketrecurrents_id']>0) {
       $PluginProcedimientosProcedimiento_TicketRecurrent ->addItem($_POST);
   }
   Html::back();  
} else if (isset($_POST["elimina"])){
	$query= "delete from glpi_plugin_procedimientos_procedimientos_ticketrecurrents where plugin_procedimientos_procedimientos_id=".$_POST["plugin_procedimientos_procedimientos_id"]."
			 and ticketrecurrents_id=".$_POST["elimina"];
    $DB->query($query);
	Html::back();

} else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
	);
			   
   $PluginProcedimientosProcedimiento_TicketRecurrent->display($_GET["id"]);
   Html::footer();
   
}
?>
