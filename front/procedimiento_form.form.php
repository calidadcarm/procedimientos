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


$PluginProcedimientosProcedimiento_Form = new PluginProcedimientosProcedimiento_Form();

 if (isset($_POST["addform"])) {   
   if ($_POST['plugin_formcreator_forms_id']>0) {
       $PluginProcedimientosProcedimiento_Form ->addItem($_POST);
   }
   Html::back();  
} else if (isset($_GET["import_form"])) {
     // Import form
      Session::checkRight("entity", UPDATE);
      Html::header(
         $PluginProcedimientosProcedimiento_Form::getTypeName(2),
         $_SERVER['PHP_SELF'],
         'config',
         'PluginProcedimientosConfig',
         'procedimiento'
      );

      if (version_compare(GLPI_VERSION, '9.2', 'ge')) {
         Html::requireJs('fileupload');
      }

      $PluginProcedimientosProcedimiento_Form->showImportForm();
      Html::footer();
      
} else if (isset($_POST["import_send"])) {
      // Import form
      Session::checkRight("entity", UPDATE);
      $PluginProcedimientosProcedimiento_Form->importJson($_REQUEST);
      Html::back();

} else if (isset($_POST["elimina"])){
	$query= "delete from glpi_plugin_procedimientos_procedimientos_forms where plugin_procedimientos_procedimientos_id=".$_POST["plugin_procedimientos_procedimientos_id"]."
			 and plugin_formcreator_forms_id=".$_POST["elimina"];
    $DB->query($query);
	Html::back();

} else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
 );
			   
   $PluginProcedimientosProcedimiento_Form->display($_GET["id"]);
   Html::footer();
   
}
?>
