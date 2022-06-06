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

$PluginProcedimientosAccion = new PluginProcedimientosAccion();


if (isset($_POST["accion_id"])) {
    if(isset($_POST["tipoaccion"])){    
		switch ($_POST["tipoaccion"]){
			case 1: //Tarea
			
				$select = "SELECT id FROM `glpi_plugin_procedimientos_tareas` where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
				$result = $DB->query($select );
				$existe = $DB->numrows($result);

					// [INICIO] [CRI] - JMZ18G - 09/11/2020 SI EXISTE PLANTILLA VACIAMOS LOS CAMPOS DE LA TAREA 
					if ($_POST["tasktemplates_id"]>0) {
						//$template = new TaskTemplate();
						//$template->getFromDB($_POST["tasktemplates_id"]);						
						$_POST["taskcategories_id"] = 0; //$template->fields["taskcategories_id"];
						$_POST["users_id_tech"] 	= 0; //$template->fields["users_id_tech"];
						$_POST["groups_id_tech"] 	= 0; //$template->fields["groups_id_tech"];
						$_POST["actiontime"] 		= 0; //$template->fields["actiontime"];
						$_POST["state"] 			= 0; //$template->fields["state"];
						$_POST["is_private"] 		= 0; //$template->fields["is_private"];
					}
         		    // [INICIO] [CRI] - JMZ18G - 09/11/2020 SI EXISTE PLANTILLA VACIAMOS LOS CAMPOS DE LA TAREA 


				if ($existe>0) {
					// INFORGES - emb97m - 12/03/2018  - Nuevos campos de tareas 9.1.6	
					
					$query= "UPDATE `glpi_plugin_procedimientos_tareas` 
							SET `taskcategories_id`=".$_POST["taskcategories_id"].",
							`users_id_tech`=".$_POST["users_id_tech"].",
							`groups_id_tech`=".$_POST["groups_id_tech"].",
							`tasktemplates_id`=".$_POST["tasktemplates_id"].",
							`actiontime`=".$_POST["actiontime"].",
							`state`=".$_POST["state"].",
							`is_private`=".$_POST["is_private"]."							
							where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];							
													
					$result = $DB->query($query);	
					
					if (!empty($_POST["documents_id"])){
					$query= "INSERT INTO `glpi_plugin_procedimientos_documents` (`items_id`, `documents_id`, `documentcategories_id`, `itemtype`, `uuid` )
					         VALUES (".$_POST["accion_id"].",".$_POST["documents_id"].",".$_POST["_rubdoc"].",'PluginProcedimientosTarea', '".plugin_procedimientos_getUuid()."')";
					$result = $DB->query($query);						
					}
					
				} else {
					// INFORGES - emb97m - 12/03/2018  - Nuevos campos de tareas 9.1.6	

					$query= "INSERT INTO `glpi_plugin_procedimientos_tareas` (`plugin_procedimientos_accions_id`, `taskcategories_id`, `users_id_tech`,
							`groups_id_tech`, `tasktemplates_id`, `state`, `actiontime`, `is_private`)
					         VALUES (".$_POST["accion_id"].",".$_POST["taskcategories_id"].",".$_POST["users_id_tech"].",".$_POST["groups_id_tech"].",
							         ".$_POST["tasktemplates_id"].",".$_POST["state"].",".$_POST["actiontime"].",".$_POST["is_private"].");";						

					$result = $DB->query($query);					
					
					if (!empty($_POST["documents_id"])){
					$query= "INSERT INTO `glpi_plugin_procedimientos_documents` (`items_id`, `documents_id`, `documentcategories_id`, `itemtype`, `uuid` )
					         VALUES (".$_POST["accion_id"].",".$_POST["documents_id"].",".$_POST["_rubdoc"].",'PluginProcedimientosTarea', '".plugin_procedimientos_getUuid()."')";
					$result = $DB->query($query);	
					}
				}
			break;
			case 2: //Escalado
				$select = "SELECT id FROM `glpi_plugin_procedimientos_escalados` where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
				$result = $DB->query($select );
				$existe = $DB->numrows($result);
				if ($existe>0) {
					$query= "UPDATE `glpi_plugin_procedimientos_escalados` SET 
					    `users_id_asignado`=".$_POST["users_id_asignado"].",
						`groups_id_asignado`=".$_POST["groups_id_asignado"].",
						`users_id_observ`=".$_POST["users_id_observ"].",
						`groups_id_observ`=".$_POST["groups_id_observ"].",
						`suppliers_id`=".$_POST["suppliers_id"]."
						 where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
					$result = $DB->query($query);					
				} else {
					$query= "INSERT INTO `glpi_plugin_procedimientos_escalados` 
							(`plugin_procedimientos_accions_id`, `users_id_asignado`, `groups_id_asignado`, `users_id_observ`, `groups_id_observ`, `suppliers_id`)
					         VALUES (".$_POST["accion_id"].",".$_POST["users_id_asignado"].",".$_POST["groups_id_asignado"].",".$_POST["users_id_observ"].",".$_POST["groups_id_observ"].",".$_POST["suppliers_id"].");";
					$result = $DB->query($query);
				}
			break;
			case 3:  // Modificación ticket
				$select = "SELECT id FROM `glpi_plugin_procedimientos_updatetickets` where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
				$result = $DB->query($select );
				$existe = $DB->numrows($result);
				if ($existe>0) {
					$query= "UPDATE `glpi_plugin_procedimientos_updatetickets` SET 
							`requesttypes_id`=".$_POST["requesttypes_id"].",
							`status`=".$_POST["status"].",
							`itilcategories_id`=".$_POST["itilcategories_id"].",
							`type`=".$_POST["type"].",
							`slts_ttr_id`=".$_POST["slts_ttr_id"].",
							`solutiontemplates_id`=".$_POST["solutiontemplates_id"]."
						 where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
					$result = $DB->query($query);					
				} else{
					$query= "INSERT INTO `glpi_plugin_procedimientos_updatetickets` (`plugin_procedimientos_accions_id`, `requesttypes_id`, `status`, `itilcategories_id`, `type`, `slts_ttr_id`, `solutiontemplates_id`)
					         VALUES (".$_POST["accion_id"].",".$_POST["requesttypes_id"].",".$_POST["status"].",".$_POST["itilcategories_id"].",".$_POST["type"].",".$_POST["slts_ttr_id"].",".$_POST["solutiontemplates_id"].");";
					$result = $DB->query($query);
				}
			break;
			case 4: // Seguimiento
				$select = "SELECT id FROM `glpi_plugin_procedimientos_seguimientos` where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
				$result = $DB->query($select );
				$existe = $DB->numrows($result);
				if ($existe>0) {
					$query= "UPDATE `glpi_plugin_procedimientos_seguimientos` SET 
					    `content`='".$_POST["content"]."',`is_private`=".$_POST["is_private"].",`requesttypes_id`=".$_POST["requesttypes_id"].", `followuptypes_id`=".$_POST["followuptypes_id"]."
						  where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
					$result = $DB->query($query);					
					
					if (!empty($_POST["documents_id"])){
					$query= "INSERT INTO `glpi_plugin_procedimientos_documents` (`items_id`, `documents_id`, `documentcategories_id`, `itemtype`, `uuid` )
					         VALUES (".$_POST["accion_id"].",".$_POST["documents_id"].",".$_POST["_rubdoc"].",'PluginProcedimientosSeguimiento', '".plugin_procedimientos_getUuid()."')";
					$result = $DB->query($query);						
					}
					
				} else{
					$query= "INSERT INTO `glpi_plugin_procedimientos_seguimientos` (`plugin_procedimientos_accions_id`, `content`, `is_private`, `requesttypes_id`, `followuptypes_id` )
					         VALUES (".$_POST["accion_id"].",'".$_POST["content"]."',".$_POST["is_private"].",".$_POST["requesttypes_id"].",".$_POST["followuptypes_id"].");";
					$result = $DB->query($query);		

					if (!empty($_POST["documents_id"])){	
					$query= "INSERT INTO `glpi_plugin_procedimientos_documents` (`items_id`, `documents_id`, `documentcategories_id`, `itemtype`, `uuid` )
					         VALUES (".$_POST["accion_id"].",".$_POST["documents_id"].",".$_POST["_rubdoc"].",'PluginProcedimientosSeguimiento', '".plugin_procedimientos_getUuid()."')";
					$result = $DB->query($query);				
					
					}
					
				}
			break;
			case 5: // Validación
			$existe=1;

			
				if  ($_POST["validatortype"]=="validador") {
					$existe=0;
				$validador = $_POST["validador"].$_SESSION["glpiID"];
								
				if ($result = $DB->query($validador)) {
				
				   if ($DB->numrows($result)) {
				$existe = $DB->numrows($result);	   
				   }
				
				}
																	
					  }
					  
			if ($existe==1) {  
				$delete = "DELETE FROM `glpi_plugin_procedimientos_validacions` where `plugin_procedimientos_accions_id`=".$_POST["accion_id"];
				$result = $DB->query($delete);
				if (isset($_POST['groups_id'])){
					$groups_id = $_POST['groups_id'];
				} else{
					$groups_id = 0;
				}
				if (($_POST["validatortype"]<>"validador") and (isset($_POST["users_id_validate"]))){
					$array_usuarios = $_POST["users_id_validate"];
					foreach ($array_usuarios as $usuario) {
						$query= "INSERT INTO `glpi_plugin_procedimientos_validacions` (`plugin_procedimientos_accions_id`, `comment_submission`, `groups_id`, `users_id_validate`)
					         VALUES (".$_POST["accion_id"].",'".$_POST["comment_submission"]."',".$groups_id.",".$usuario.");";
						$result = $DB->query($query);
					}					
				} else {   
				
				if ($_POST["validatortype"]=="validador") {
				
				$query= "INSERT INTO `glpi_plugin_procedimientos_validacions` (`plugin_procedimientos_accions_id`, `comment_submission`, `validador`)
				VALUES (".$_POST["accion_id"].",'".$_POST["comment_submission"]."','".$_POST["validador"]."');";
				 
				$result = $DB->query($query);
				
				}
				
				}
			} else {  
			Session::addMessageAfterRedirect("<strong>Query <font color='#b12231'>NO VALIDA</font>.</strong>
			<BR><BR><font color='#22b147'>".$validador."</font>
			<br><br><strong>Resultados: <font color='#b12231'>".$existe."</font>.</strong>" ,true);
			}
			break;				
		}
	}
    Html::redirect($_SERVER['HTTP_REFERER']);	
}

if (isset($_POST["add"])) {	
    $newID=$PluginProcedimientosAccion->add($PluginProcedimientosAccion->prepareInputForAdd($_POST)); 

	if ($_POST["plugin_procedimientos_tipoaccions_id"] == 1){ // Detalles de tarea por defecto.
		// INFORGES - emb97m - 14/05/2018  - Nuevos campos de tareas 9.1.6		
		$query_detalles= "INSERT INTO `glpi_plugin_procedimientos_tareas` 
	        (plugin_procedimientos_accions_id, taskcategories_id, 
			 users_id_tech, 
			 groups_id_tech, 
			 is_private, 
			 state, 
			 tasktemplates_id)
			 VALUES (".$newID.",0,0,0,1,1,0);";
		$result = $DB->query($query_detalles);
	} 

	if ($_POST["plugin_procedimientos_tipoaccions_id"] == 4){ // Detalles de tarea por defecto.
		// INFORGES - jmz18g - 18/05/2018  - Nuevos campos de tareas 9.1.6		
				
	         $query_detalles= "INSERT INTO `glpi_plugin_procedimientos_seguimientos` 
		 (`plugin_procedimientos_accions_id`, `content`, `is_private`, `requesttypes_id`, `followuptypes_id` )
	     VALUES (".$newID.",'".$_POST["comment"]."',1,0,0)";
	
		$result = $DB->query($query_detalles);
	} 	
	 
		 
		 
    Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["delete"])) {

	$PluginProcedimientosAccion->delete($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginProcedimientosAccion'));
	
} else if (isset($_POST["purge"])) {

	$PluginProcedimientosAccion->delete($_POST,1);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginProcedimientosAccion'));
	
} else if (isset($_POST["update"])) {
	
	// Antes de actualizar comprueba si hay un cambio de tipo para eliminar registro antiguo.
	if ($_POST['plugin_procedimientos_tipoaccions_id'] != 1){
		$select = "DELETE FROM `glpi_plugin_procedimientos_tareas` WHERE `plugin_procedimientos_accions_id`=".$_POST["id"];
		$result = $DB->query($select );		
	}
	if ($_POST['plugin_procedimientos_tipoaccions_id'] != 2){
		$select = "DELETE FROM `glpi_plugin_procedimientos_escalados` WHERE `plugin_procedimientos_accions_id`=".$_POST["id"];
		$result = $DB->query($select );		
	}
	if ($_POST['plugin_procedimientos_tipoaccions_id'] != 3){
		$select = "DELETE FROM `glpi_plugin_procedimientos_updatetickets` WHERE `plugin_procedimientos_accions_id`=".$_POST["id"];
		$result = $DB->query($select );		
	}
	if ($_POST['plugin_procedimientos_tipoaccions_id'] != 4){
		$select = "DELETE FROM `glpi_plugin_procedimientos_seguimientos` WHERE `plugin_procedimientos_accions_id`=".$_POST["id"];
		$result = $DB->query($select );		
	}
	if ($_POST['plugin_procedimientos_tipoaccions_id'] != 5){
		$select = "DELETE FROM `glpi_plugin_procedimientos_validacions` WHERE `plugin_procedimientos_accions_id`=".$_POST["id"];
		$result = $DB->query($select );		
	}	
	$PluginProcedimientosAccion->update($_POST);
	Html::redirect($_SERVER['HTTP_REFERER']);
 }                                          
  else {
	  
   Html::header(__('Procedimientos', 'procedimientos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginProcedimientosConfig",
      "accion"
   );
    if (Session::haveRight('plugin_procedimientos',READ)) {

		// [INICIO] [CRI] JMZ18G 15/03/2021 PONER TEXTO ENRIQUECIDO EN EL CAMPO DESCRIPCIÓN
		if (!isset($_SESSION['glpi_js_toload']['tinymce'])) {
		Html::requireJs('tinymce');
		} 		
		// [FINAL] [CRI] JMZ18G 15/03/2021 PONER TEXTO ENRIQUECIDO EN EL CAMPO DESCRIPCIÓN
		
		$PluginProcedimientosAccion ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}     
}


?>