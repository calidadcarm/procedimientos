<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */

global $DB;

include ('../../../inc/includes.php');
include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");


if (!isset($_GET["id"])) {
   $_GET["id"] = "";
}
if (!isset($_GET["withtemplate"])) {
   $_GET["withtemplate"] = "";
}

if (isset($_POST['plugin_procedimientos_procedimientos_id'])) {
	$lineas =  getLineasProcedimiento($_POST['plugin_procedimientos_procedimientos_id']);
}

$PluginProcedimientosCondicion = new PluginProcedimientosCondicion();

if (isset($_POST["add"])) {
  /*  $_POST['way_yes'] = $lineas[$_POST['way_yes']];
    $_POST['way_no'] = 	$lineas[$_POST['way_no']];
   // $_POST['comment'] = "Respuesta <strong>".$_POST['tag_0']."</strong>, ir a #linea: <strong><span id='linea_1'>".$_POST['way_yes']."</span></strong><BR>Respuesta <strong> ".$_POST['tag_1']."</strong>, ir a #linea: <strong><span id='linea_2'>".$_POST['way_no']."</span></strong>";	
	*/
	$POST=$PluginProcedimientosCondicion->lineas($_POST);
        $newID=$PluginProcedimientosCondicion->add($PluginProcedimientosCondicion->prepareInputForAdd($POST));
	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/procedimientos/front/procedimiento.form.php?id=".$POST['plugin_procedimientos_procedimientos_id']);	
	
} else if (isset($_POST["purge"])) {
    // Borro el elemento del procedimiento
   	$temp = new PluginProcedimientosProcedimiento_Item();
    $temp->deleteByCriteria(array('itemtype' => 'PluginProcedimientosCondicion',
                                  'items_id' => $_POST['id'])); 

	$PluginProcedimientosCondicion->delete($_POST,1);
	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/procedimientos/front/procedimiento.form.php?id=".$_POST['plugin_procedimientos_procedimientos_id']);
	
} else if (isset($_POST["update"])) {
   /* $_POST['way_yes'] = $lineas[$_POST['way_yes']];
    $_POST['way_no'] = 	$lineas[$_POST['way_no']]; 	
    $_POST['comment'] = "Respuesta <strong>".$_POST['tag_0']."</strong>, ir a #linea: <strong>".$_POST['way_yes']."</strong><BR>Respuesta <strong> ".$_POST['tag_1']."</strong>, ir a #linea: <strong>".$_POST['way_no']."</strong>";	*/
	
	$POST=$PluginProcedimientosCondicion->lineas($_POST);
	
	$PluginProcedimientosCondicion->update($POST);
	
	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/procedimientos/front/procedimiento.form.php?id=".$_POST['plugin_procedimientos_procedimientos_id']);
 }                                          
  else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
   );
    if (Session::haveRight('plugin_procedimientos',UPDATE)) {
		$PluginProcedimientosCondicion ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}     
}



?>