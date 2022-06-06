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

$PluginProcedimientosProcedimiento_Ticket = new PluginProcedimientosProcedimiento_Ticket();
$tickets_id =  $_POST['tickets_id'];
$procedimientos_id =  $_POST['_procedimiento'];

		$campo_1="";
		$campo_2="";
	    $state="";
		$instancia="";

foreach($_POST as $key => $value){
switch ($key) {
    case "line_id_1":
		$campo_1=$key;
		$campo_2="tag_id_1";	
        $state="`state`='1',";
		$instancia="`instancia_id` = '0'";		
        break;
    case "line_id_2":
		$campo_1=$key;
		$campo_2="tag_id_2";
        $state="`state`='1',";
		$instancia="`instancia_id` = '1'";
        break;
    case "line_id_3":
		$campo_1=$key;
		$campo_2="tag_id_3";	
		$state="`state`='1',";
        $instancia="`instancia_id` = '2'";
        break;
	case "line_id_4":
		$campo_1=$key;
		$campo_2="tag_id_4";	
		$state="`state`='1',";
        $instancia="`instancia_id` = '3'";
        break;
	case "line_id_5":
		$campo_1=$key;
		$campo_2="tag_id_5";	
        $state="`state`='1',";
		$instancia="`instancia_id` = '4'";
        break;	
				
/*	case "checked":
        $instancia="`instancia_id` = '4'";
        break;		
	case "crear":
        $instancia="`instancia_id` = '4'";
        break;	*/	
		
}
}

if ((!empty($state) and (!empty($instancia)) and (!empty($campo_1)) and (!empty($campo_2)))) {

	    $select_wayyes = "Select `plugin_procedimientos_procedimientos_id`,$campo_1, `$campo_2`, `name` from `glpi_plugin_procedimientos_condicions` where `id`='".$_POST['items_id']."';";
	$result_wayyes = $DB->query($select_wayyes);
	$condicion = $DB->fetch_array($result_wayyes);
	if (isset($condicion[$campo_1])){
		$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET ".$state." ".$instancia." 
				   WHERE `plugin_procedimientos_procedimientos_id`='".$condicion['plugin_procedimientos_procedimientos_id']."' and `tickets_id`='".$tickets_id."' and
				   `itemtype`='PluginProcedimientosCondicion' and `items_id`='".$_POST['items_id']."';";
		//echo "<br>Update respuesta SI".$update ;
		$result_update = $DB->query($update);
		
		// Actualiza histórico del ticket.
		$message = $condicion["name"].":".$condicion[$campo_2];
		//Actualizamos el histórico 
		$insert_logs = "INSERT INTO glpi_logs (itemtype, itemtype_link, items_id, linked_action, user_name, date_mod, new_value) 
                        VALUES ('Ticket', 'PluginProcedimientosCondicion', ".$_POST['tickets_id'].", '18', '".getUserName($_SESSION['glpiID'])."', 
						NOW(),'".$message."')";
		$result = $DB->query($insert_logs);		
		
		if ($condicion[$campo_1] < $_POST['linea_condicion']){
	//		print_r($_POST);
		//	echo "<BR>".$condicion['plugin_procedimientos_procedimientos_id']."<BR>";
		///	echo "<BR>". $condicion[$campo_1]."<BR>";
		//	exit;
			reset_camino_salto_atras($condicion['plugin_procedimientos_procedimientos_id'], $tickets_id, $condicion[$campo_1], $_POST['linea_condicion']);
		} else if ($condicion[$campo_1] > $_POST['linea_condicion']){
			reset_camino_salto_adelante($condicion['plugin_procedimientos_procedimientos_id'], $tickets_id, $condicion[$campo_1], $_POST['linea_condicion']);
		}
	}
	
	Html::redirect($_SERVER['HTTP_REFERER']);

}

/*
if (isset($_POST["way_yes"])) { // Respuesta condición 'SI'
    $select_wayyes = "Select `plugin_procedimientos_procedimientos_id`,`way_yes`, `tag_0`, `name` from `glpi_plugin_procedimientos_condicions` where `id`='".$_POST['items_id']."';";
	$result_wayyes = $DB->query($select_wayyes);
	$condicion = $DB->fetch_array($result_wayyes);
	if (isset($condicion['way_yes'])){
		$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET `state`='1', `instancia_id` = '0'
				   WHERE `plugin_procedimientos_procedimientos_id`='".$condicion['plugin_procedimientos_procedimientos_id']."' and `tickets_id`='".$tickets_id."' and
				   `itemtype`='PluginProcedimientosCondicion' and `items_id`='".$_POST['items_id']."';";
		//echo "<br>Update respuesta SI".$update ;
		$result_update = $DB->query($update);
		
		// Actualiza histórico del ticket.
		$message = $condicion["name"].":".$condicion['tag_0'];
		//Actualizamos el histórico 
		$insert_logs = "INSERT INTO glpi_logs (itemtype, itemtype_link, items_id, linked_action, user_name, date_mod, new_value) 
                        VALUES ('Ticket', 'PluginProcedimientosCondicion', ".$_POST['tickets_id'].", '18', '".getUserName($_SESSION['glpiID'])."', 
						NOW(),'".$message."')";
		$result = $DB->query($insert_logs);		
		
		if ($condicion['way_yes'] < $_POST['linea_condicion']){
	//		print_r($_POST);
		//	echo "<BR>".$condicion['plugin_procedimientos_procedimientos_id']."<BR>";
		///	echo "<BR>". $condicion['way_yes']."<BR>";
		//	exit;
			reset_camino_salto_atras($condicion['plugin_procedimientos_procedimientos_id'], $tickets_id, $condicion['way_yes'], $_POST['linea_condicion']);
		} else if ($condicion['way_yes'] > $_POST['linea_condicion']){
			reset_camino_salto_adelante($condicion['plugin_procedimientos_procedimientos_id'], $tickets_id, $condicion['way_yes'], $_POST['linea_condicion']);
		}
	}
	
	Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["way_no"])) { // Respuesta condición 'NO'
    $select_wayno = "Select `plugin_procedimientos_procedimientos_id`,`way_no`, `tag_1`, `name` from `glpi_plugin_procedimientos_condicions` where `id`='".$_POST['items_id']."';";
	$result_wayno = $DB->query($select_wayno);
	$condicion = $DB->fetch_array($result_wayno);
	if (isset($condicion['way_no'])){
		$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET `state`='1' , `instancia_id` = '1'
				   WHERE `plugin_procedimientos_procedimientos_id`='".$condicion['plugin_procedimientos_procedimientos_id']."' and `tickets_id`='".$tickets_id."' and
				   `itemtype`='PluginProcedimientosCondicion' and `items_id`='".$_POST['items_id']."';";
		//echo "<br>Update respuesta NO".$update ;
		$result_update = $DB->query($update);
		
		// Actualiza histórico del ticket.
		$message = $condicion["name"].":".$condicion['tag_1'];
		//Actualizamos el histórico 
		$insert_logs = "INSERT INTO glpi_logs (itemtype, itemtype_link, items_id, linked_action, user_name, date_mod, new_value) 
                        VALUES ('Ticket', 'PluginProcedimientosCondicion', ".$_POST['tickets_id'].", '18', '".getUserName($_SESSION['glpiID'])."', 
						NOW(),'".$message."')";
		$result = $DB->query($insert_logs);	
		
		if ($condicion['way_no'] < $_POST['linea_condicion']){
			reset_camino_salto_atras($condicion['plugin_procedimientos_procedimientos_id'], $tickets_id, $condicion['way_no'], $_POST['linea_condicion']);
		} else if ($condicion['way_no'] > $_POST['linea_condicion']){
			reset_camino_salto_adelante($condicion['plugin_procedimientos_procedimientos_id'], $tickets_id, $condicion['way_no'], $_POST['linea_condicion']);
		}
		
	}
				
	Html::redirect($_SERVER['HTTP_REFERER']);

}*/
else if (isset($_POST["checked"])) {
		$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET `state`='1'
				   WHERE `plugin_procedimientos_procedimientos_id`='".$procedimientos_id."' and `tickets_id`='".$tickets_id."' and
				   `itemtype`='PluginProcedimientosLink' and `items_id`='".$_POST['items_id']."';";
		$result_update = $DB->query($update);
		ejecutar_Procedimiento($tickets_id);
		Html::redirect($_SERVER['HTTP_REFERER']);
}
else if (isset($_POST["solved"])) {
		$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET `state`='0'
				   WHERE `plugin_procedimientos_procedimientos_id`='".$procedimientos_id."' and `tickets_id`='".$tickets_id."' and
				   `itemtype`='PluginProcedimientosAccion' and `items_id`='".$_POST['items_id']."';";
		$result_update = $DB->query($update);
		ejecutar_Procedimiento($tickets_id);
		Html::redirect($_SERVER['HTTP_REFERER']);
}
else if (isset($_POST["crear"])) {
	
	if ($procedimientos_id>0) {
		// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
		$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
		$DB->query($query);
		// Instanciamos y ejecutamos procedimiento correspondiente.
		instancia_procedimiento($procedimientos_id, $tickets_id);
		ejecutar_Procedimiento ($tickets_id);
	} else {
		// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
		$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
		$DB->query($query);
		
	}	
	
    Html::redirect($_SERVER['HTTP_REFERER']);	
}                                          
else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
   );
    if (Session::haveRight('plugin_procedimientos',UPDATE)) {
		$PluginProcedimientosProcedimiento ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}    
}
?>