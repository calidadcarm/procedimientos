<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Octubre 2016

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

$PluginProcedimientosSalto = new PluginProcedimientosSalto();

if (isset($_POST["add"])) {
   // $_POST['goto'] = $lineas[$_POST['goto']];
   // $_POST['comment'] = "Ir a #linea: <strong>".$_POST['goto']."</strong>";
	$lineas=$PluginProcedimientosSalto->lineas($_POST);
	$comentario=explode("|",$lineas)[0];
	$linea=explode("|",$lineas)[1];	
	$_POST['comment']=$comentario;	
	$_POST['goto']=$linea;	
        $newID=$PluginProcedimientosSalto->add($PluginProcedimientosSalto->prepareInputForAdd($_POST));
	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/procedimientos/front/procedimiento.form.php?id=".$_POST['plugin_procedimientos_procedimientos_id']);	
	
} else if (isset($_POST["purge"])) {
    // Borro el elemento del procedimiento
   	$temp = new PluginProcedimientosProcedimiento_Item();
    $temp->deleteByCriteria(array('itemtype' => 'PluginProcedimientosSalto',
                                  'items_id' => $_POST['id'])); 

	$PluginProcedimientosSalto->delete($_POST,1);
	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/procedimientos/front/procedimiento.form.php?id=".$_POST['plugin_procedimientos_procedimientos_id']);
	
} else if (isset($_POST["update"])) {
    // $_POST['goto'] = $lineas[$_POST['goto']];
	//$_POST['comment'] = "Ir a #linea: <strong>".$_POST['goto']."</strong>";	

	$POST = $PluginProcedimientosSalto->lineas($_POST);

	$PluginProcedimientosSalto->update($POST);
	Html::redirect($CFG_GLPI["root_doc"] . "/plugins/procedimientos/front/procedimiento.form.php?id=".$POST['plugin_procedimientos_procedimientos_id']);
 }                                          
  else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
   );
    if (Session::haveRight('plugin_procedimientos',UPDATE)) {
		$PluginProcedimientosSalto ->display(array('id' => $_GET["id"]));
		Html::footer(); 
	} else {
			Html::displayRightError();
	}     
}


?>