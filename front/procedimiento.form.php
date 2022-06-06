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

$PluginProcedimientosProcedimiento = new PluginProcedimientosProcedimiento();

if (isset($_GET["boton"])) {
	
	$linea_original = $_GET["line"];
	$procedimientos_id = $_GET["procedimientos_id"];
	$id_origen = $_GET["id"];
	
	if ($_GET["boton"]=="abajo") { // BOTON BAJAR - Line a intercambiar es una más.
	// Obtenemos el ID del elemento siguiente a la linea actual
		$linea_intercambio = $linea_original+1;
	}
	else { // BOTON SUBIR - Line a intercambiar es una menos.
	// Obtenemos el ID del elemento anterior a la linea actual
		$linea_intercambio = $linea_original-1;
	}
	
	$query = "select id from glpi_plugin_procedimientos_procedimientos_items where `line`=".$linea_intercambio." 
				  and `plugin_procedimientos_procedimientos_id`=".$procedimientos_id;
 			  
	$result = $DB->query($query);
	$id_intercambio = $DB->fetch_assoc($result);		
	
	if ($id_intercambio['id']!= NULL) PluginProcedimientosProcedimiento_Item::intercambia($id_origen,$linea_original,$id_intercambio['id'], $linea_intercambio);
	
	// Array con todas las condiciones del procedimiento.
	$select_condiciones= "Select * from `glpi_plugin_procedimientos_procedimientos_items`
						  inner join `glpi_plugin_procedimientos_condicions` on (`glpi_plugin_procedimientos_condicions`.`id`=`glpi_plugin_procedimientos_procedimientos_items`.`items_id`)
						  where `glpi_plugin_procedimientos_procedimientos_items`.`itemtype`='PluginProcedimientosCondicion' 
						  and `glpi_plugin_procedimientos_procedimientos_items`.`plugin_procedimientos_procedimientos_id`=".$procedimientos_id .";";
	$result_condiciones = $DB->query($select_condiciones);
	// Procedimiento con condiciones.
	if ($DB->numrows($result_condiciones)){
		//$lineas =  getLineasProcedimiento($procedimientos_id);
		//print_r($lineas);
		while ($cond = $DB->fetch_assoc($result_condiciones)) {
			if ($cond['way_yes'] == $linea_original){
				$cond['way_yes'] = 99999;
			}
			if ($cond['way_no'] == $linea_original){
				$cond['way_no'] = 99999;
			}	 
			if ($cond['way_yes'] == $linea_intercambio){
				$cond['way_yes'] = $linea_original;
			}
			if ($cond['way_no'] == $linea_intercambio){
				$cond['way_no'] = $linea_original;
			}
			
			$update = "update `glpi_plugin_procedimientos_condicions` set 
			          `way_yes`=".$cond['way_yes'].", 
					  `way_no`=".$cond['way_no'].",
					  `comment`='Respuesta <strong>Si</strong>, ir a #linea: <strong>".$cond['way_yes']."</strong><BR>Respuesta <strong> No</strong>, ir a #linea: <strong>".$cond['way_no']."</strong>'
					   where `id`=".$cond['items_id'].";";
			$result_update = $DB->query($update);
			
			if ($cond['way_yes'] == 99999){
				$cond['way_yes'] = $linea_intercambio;
			}
			if ($cond['way_no'] == 99999){
				$cond['way_no'] = $linea_intercambio;
			}	 			
			$update = "update `glpi_plugin_procedimientos_condicions` set 
			          `way_yes`=".$cond['way_yes'].", 
					  `way_no`=".$cond['way_no'].",
					  `comment`='Respuesta <strong>Si</strong>, ir a #linea: <strong>".$cond['way_yes']."</strong><BR>Respuesta <strong> No</strong>, ir a #linea: <strong>".$cond['way_no']."</strong>'
					   where `id`=".$cond['items_id'].";";
			$result_update = $DB->query($update);
					 
		}
	}	
	// Array con los saltos de linea
	$select_saltos = "Select `glpi_plugin_procedimientos_saltos`.* from `glpi_plugin_procedimientos_procedimientos_items`
			  inner join `glpi_plugin_procedimientos_saltos` on (`glpi_plugin_procedimientos_saltos`.`id`=`glpi_plugin_procedimientos_procedimientos_items`.`items_id`)
			  where `glpi_plugin_procedimientos_procedimientos_items`.`itemtype`='PluginProcedimientosSalto' 
			  and `glpi_plugin_procedimientos_procedimientos_items`.`plugin_procedimientos_procedimientos_id`='".$procedimientos_id."'";
	//echo "<br>Select salto:".$select_saltos."<br>";
	$result_saltos = $DB->query($select_saltos);
	if ($DB->numrows($result_saltos)){
		while ($salto = $DB->fetch_array($result_saltos)){ // Para cada salto que se encuentra
			//echo "<br>Datos del salto: ";
			//	print_r($salto);
			// Renumera goto
			if ($salto['goto'] == $linea_original) {
			    // Actualiza salto
				$update = "Update `glpi_plugin_procedimientos_saltos` SET `comment`='Ir a #linea: <strong>".$linea_intercambio."</strong>', 
						`goto`='".$linea_intercambio."'					
					   WHERE `id`='".$salto['id']."';";	
				//echo "<br>Select update:".$update."<br>";					   
				$DB->query($update);
			}				
		}	
	}

	Html::redirect($_SERVER['HTTP_REFERER']);
}

if (isset($_POST["add"])) {

	$newID=$PluginProcedimientosProcedimiento->add($PluginProcedimientosProcedimiento->prepareInputForAdd($_POST));
    Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["delete"])) {

	$PluginProcedimientosProcedimiento->delete($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginProcedimientosProcedimiento'));
	
} else if (isset($_POST["purge"])) {

	$PluginProcedimientosProcedimiento->delete($_POST,1);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginProcedimientosProcedimiento'));
	
} else if (isset($_POST["update"])) {
	
	$PluginProcedimientosProcedimiento->update($_POST);
	Html::redirect($_SERVER['HTTP_REFERER']);
	
 } else if (isset($_POST["additem"])) {
   if (isset($_POST["_type"]) && !empty($_POST["_type"])
       && isset($_POST["plugin_procedimientos_procedimientos_id"]) && $_POST["plugin_procedimientos_procedimientos_id"]) {
	  
	  $item = new PluginProcedimientosProcedimiento_Item();
		  
	  //buscamos el último num de linea, le sumamos uno y se lo ponemos al nuevo elemento
      $query = "SELECT max(line) as linea FROM `glpi_plugin_procedimientos_procedimientos_items` 
					where plugin_procedimientos_procedimientos_id=".$_POST["procedimientos_id"];
      $result = $DB->query($query);
	  $data = $DB->fetch_assoc($result);
      $_POST["line"] = $data["linea"]+1;
	               
      switch ($_POST["_type"]) {
         case 'PluginProcedimientosMarcador': // Se añade elemento Marcador
           if (isset($_POST['plugin_procedimientos_marcadors_id']) && $_POST['plugin_procedimientos_marcadors_id']) {
				$_POST['itemtype'] = 'PluginProcedimientosMarcador';
				$_POST['items_id'] = $_POST['plugin_procedimientos_marcadors_id'];
		   }
		   break;

         case 'PluginProcedimientosAccion': // Se añade elemento Accion
           if (isset($_POST['plugin_procedimientos_accions_id']) && $_POST['plugin_procedimientos_accions_id']) {
				$_POST['itemtype'] = 'PluginProcedimientosAccion';
				$_POST['items_id'] = $_POST['plugin_procedimientos_accions_id'];			
            }
            break;

         case 'PluginProcedimientosProcedimiento': // Se añade elemento Procedimiento
            if (isset($_POST['plugin_procedimientos_procedimientos_id']) && $_POST['plugin_procedimientos_procedimientos_id']) {
				$_POST['itemtype'] = 'PluginProcedimientosProcedimiento';
				$_POST['items_id'] = $_POST['plugin_procedimientos_procedimientos_id'];
				$_POST['plugin_procedimientos_procedimientos_id'] = $_POST['procedimientos_id'];					
            }
            break;

         case 'PluginProcedimientosCondicion': // Se CREA elemento Condicion y se añade al procedimiento
			$PluginProcedimientosCondicion = new PluginProcedimientosCondicion();
			/*$condicion['plugin_procedimientos_procedimientos_id'] = $_POST["plugin_procedimientos_procedimientos_id"];
			$lineas =  getLineasProcedimiento($_POST['plugin_procedimientos_procedimientos_id']);
			$condicion['name'] = $_POST["name"];
			$condicion['way_yes'] = $lineas[$_POST['way_yes']];
			$condicion['way_no'] = 	$lineas[$_POST['way_no']];
			$condicion['tag_0'] = 	$_POST['tag_0'];
			$condicion['tag_1'] =   $_POST['tag_1'];
			$condicion['comment'] = "Respuesta <strong>".$condicion['tag_0']."</strong>, ir a #linea: <strong>".$condicion['way_yes']."</strong><BR>Respuesta <strong> ".$condicion['tag_1'] ."</strong>, ir a #linea: <strong>".$condicion['way_no']."</strong>";				
			unset($condicion['id']);			
			$_POST['items_id'] = $PluginProcedimientosCondicion->add($condicion);	*/
			$POST = $PluginProcedimientosCondicion->lineas($_POST); 
			$_POST['items_id'] = $PluginProcedimientosCondicion->add($POST);				
 			$_POST['itemtype'] = 'PluginProcedimientosCondicion';
            break;

         case 'PluginProcedimientosSalto': // Se CREA elemento Salto y se añade al procedimiento
			$PluginProcedimientosSalto= new PluginProcedimientosSalto();
		/*	$salto['plugin_procedimientos_procedimientos_id'] = $_POST["plugin_procedimientos_procedimientos_id"];
			$lineas =  getLineasProcedimiento($_POST['plugin_procedimientos_procedimientos_id']);
			$salto['name'] = $_POST["name"];
			$salto['goto'] = $lineas[$_POST['goto']];
			$salto['comment'] = "Ir a #linea: <strong>".$salto['goto']."</strong>";				
			unset($salto['id']);
			$_POST['items_id'] = $PluginProcedimientosSalto->add($salto);		*/	
					
			$POST = $PluginProcedimientosSalto->lineas($_POST);
				 
		    $_POST['items_id'] = $PluginProcedimientosSalto->add($POST);			
 			$_POST['itemtype'] = 'PluginProcedimientosSalto';
            break;

         case 'PluginProcedimientosLink': // Se añade elemento Enlace
           if (isset($_POST['plugin_procedimientos_links_id']) && $_POST['plugin_procedimientos_links_id']) {
				$_POST['itemtype'] = 'PluginProcedimientosLink';
				$_POST['items_id'] = $_POST['plugin_procedimientos_links_id'];			
            }
            break;			
      }
      if (!is_null($item)) {
         $item->add($PluginProcedimientosProcedimiento->prepareInputForAdd($_POST));
      }
   }
   Html::back();

}                                          
  else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "procedimiento"
   );
    if (Session::haveRight('plugin_procedimientos',READ)) {
		$PluginProcedimientosProcedimiento ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}     
}

?>