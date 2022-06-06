<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */
include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");

// Install process for plugin : need to return true if succeeded
function plugin_procedimientos_install() {
   global $DB;

   Toolbox::logInFile("procedimientos", "Plugin installation\n");
	
    if (!$DB->TableExists("glpi_plugin_procedimientos_procedimientos")){ 
	
		$fichero_install = GLPI_ROOT . '/plugins/procedimientos/sql/install.sql';
		if (file_exists($fichero_install)){
			Session::addMessageAfterRedirect("Ejecutando fichero <strong><font color='#40b122'>INSTALL.sql</font></strong>",true);
			$DB->runFile($fichero_install);
			Session::addMessageAfterRedirect("<br>Scripts ejecutado<br>",true);
		} else {
			Session::addMessageAfterRedirect("No existe el fichero ".$fichero_install,true);
		} 		

	}	
	
	
    if (($DB->TableExists("glpi_plugin_procedimientos_procedimientos")) and (!$DB->TableExists("glpi_followuptypes"))){ 
		   
      $DB->query("CREATE TABLE `glpi_followuptypes` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	  `comment` text COLLATE utf8_unicode_ci,
	  PRIMARY KEY (`id`),
	  KEY `name` (`name`)
	) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

	$DB->query("INSERT INTO `glpi_followuptypes` (`id`,`name`,`comment`) VALUES (9,'Comunicación con el solicitante','Cuando queramos informar al solicitante sobre el ticket. Seleccionar Privado \"No\"\r\n');");
	$DB->query("INSERT INTO `glpi_followuptypes` (`id`,`name`,`comment`) VALUES (10,'Comunicación entre técnicos','Cuando reasignamos el ticket a otro grupo técnico y le queremos pasar información');");
	$DB->query("INSERT INTO `glpi_followuptypes` (`id`,`name`,`comment`) VALUES (11,'Mal escalado','Cuando nos llega un ticket que no es para nuestro grupo.');");
	$DB->query("INSERT INTO `glpi_followuptypes` (`id`,`name`,`comment`) VALUES (12,'Petición de Información','Cuando necesitamos información del solicitante para poder tramitar el ticket. Debe de seleccionarse Privado \"No\"');");
	$DB->query("INSERT INTO `glpi_followuptypes` (`id`,`name`,`comment`) VALUES (13,'Anotación','');");


	if (!$DB->fieldExists("glpi_itilfollowups","followuptypes_id")){

	$DB->query("ALTER TABLE `glpi_itilfollowups` 
	ADD COLUMN `followuptypes_id` INT(11) NULL DEFAULT NULL AFTER `sourceof_items_id`;");

	}

   }
// [INICIO] [CRI] - JMZ18G - 06/11/2020 Añadir actiontime al detalle de la tarea
	if (!$DB->fieldExists("glpi_plugin_procedimientos_tareas","actiontime")){

	$DB->query("ALTER TABLE `glpi_plugin_procedimientos_tareas` 
	ADD COLUMN `actiontime` INT(11) NULL DEFAULT 0 AFTER `tasktemplates_id`;");

	}
// [FIN] [CRI] - JMZ18G - 06/11/2020 Añadir actiontime al detalle de la tarea

// [INICIO] [CRI] - JMZ18G - 06/05/2022 Añadir accion Eliminar Técnicos

$query = "SELECT * FROM glpi_plugin_procedimientos_tipoaccions where uuid = 'c0dff0d6-9e4abb40-5a61e7e35e2256.00000009';";

if ($result = $DB->query($query)) {
	
	if ($DB->numrows($result)==0) {

	$params = [
	
		'entities_id' => 0, 
		'is_recursive' => 1, 
		'name' => 'Eliminar Técnicos',
		'uuid' =>  'c0dff0d6-9e4abb40-5a61e7e35e2256.00000009',

	];

	$DB->insert("glpi_plugin_procedimientos_tipoaccions", $params);
	
	}

}

// [FINAL] [CRI] - JMZ18G - 06/05/2022 Añadir accion Eliminar Técnicos

  // *******************************************************************************************
  //  [INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
  // ******************************************************************************************* 
	if (!$DB->fieldExists("glpi_plugin_procedimientos_procedimientos_forms","plugin_formcreator_targettickets_id")){

		$DB->query("ALTER TABLE `glpi_plugin_procedimientos_procedimientos_forms` 
								ADD COLUMN `plugin_formcreator_targettickets_id` INT(11) NULL DEFAULT 0 AFTER `plugin_formcreator_forms_id`,
								DROP KEY `unicity`,
								ADD UNIQUE KEY `unicity` (`plugin_procedimientos_procedimientos_id`,`plugin_formcreator_forms_id`, `plugin_formcreator_targettickets_id`)");

		$DB->query("UPDATE glpi_plugin_procedimientos_procedimientos_forms AS a
								LEFT join glpi_plugin_formcreator_targettickets b
								on  a.plugin_formcreator_forms_id = b.plugin_formcreator_forms_id
								SET
								a.plugin_formcreator_targettickets_id = IF(b.id IS NOT NULL, b.id, 0)");

		$plugin = new Plugin();

		if($plugin->isInstalled('formcreator') || $plugin->isActivated('formcreator')) {

			$DB->query("UPDATE glpi_plugin_formcreator_targettickets AS a
										LEFT join glpi_plugin_procedimientos_procedimientos_forms b
										on  a.id = b.plugin_formcreator_targettickets_id and b.plugin_formcreator_targettickets_id IS NOT NULL
										SET
										a.plugin_procedimientos_procedimientos_id = IF(b.plugin_procedimientos_procedimientos_id IS NOT NULL, b.plugin_procedimientos_procedimientos_id, 0)");
		}

	}
  // *******************************************************************************************
  //  [FINAL] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
  // *******************************************************************************************

   PluginProcedimientosProfile::initProfile();
   PluginProcedimientosProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);	
   
   return true;
}


// Uninstall process for plugin : need to return true if succeeded
function plugin_procedimientos_uninstall() {
	/*global $DB;
	  Toolbox::logInFile("procedimientos", "Plugin Uninstallation\n");
	
    if ($DB->TableExists("glpi_plugin_procedimientos_procedimientos")){ 
	
		$fichero_install = GLPI_ROOT . '/plugins/procedimientos/sql/uninstall.sql';
		if (file_exists($fichero_install)){
			Session::addMessageAfterRedirect("Ejecutando fichero <strong><font color='#b14522'>UNINSTALL.sql</font></strong>",true);
			$DB->runFile($fichero_install);
			Session::addMessageAfterRedirect("<br>Scripts ejecutado<br>",true);
		} else {
			Session::addMessageAfterRedirect("No existe el fichero ".$fichero_install,true);
		} 		

	}*/
	
   return true;
}


function plugin_procedimientos_postinit() {   
   return true;
}

function plugin_procedimientos_getAddSearchOptions($itemtype) {
   global $LANG;

   $sopt = array();
   
   //echo $itemtype;
 
   if ($itemtype == 'PluginProcedimientosAccion') {
	 	
		$sopt['tareas'] = 'Detalles tarea';
		
		$sopt[1105]['table']     = 'glpi_taskcategories';
        $sopt[1105]['field']     = 'name';		 
        $sopt[1105]['name']      = 'Tipo Tarea SD';	
		$sopt[1105]['datatype']      = 'dropdown';
		$sopt[1105]['massiveaction'] = false;
		$sopt[1105]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_tareas',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));
		/*$sopt['escalados'] = 'Detalles escalado';
		
		$sopt[1106]['table']     = 'glpi_groups';
        $sopt[1106]['field']     = 'completename';
        $sopt[1106]['itemlink']     = 'groups_id_asignado';
	    $sopt[1106]['condition']     = 'is_assign';	
        $sopt[1106]['name']      = 'Grupo asignado';	
		$sopt[1106]['datatype']      = 'dropdown';
		$sopt[1106]['massiveaction'] = false;
		$sopt[1106]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_escalados',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));
		$sopt[1107]['table']     = 'glpi_users';
        $sopt[1107]['field']     = 'completename';
        $sopt[1107]['itemlink']     = 'users_id_asignado';
        $sopt[1107]['name']      = 'T&eacute;cnico asignado';	
		$sopt[1107]['datatype']      = 'dropdown';
		$sopt[1107]['massiveaction'] = false;
		$sopt[1107]['right']  = 'own_ticket'; // Sólo usuarios técnicos
		$sopt[1107]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_escalados',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));																  
		$sopt[1108]['table']     = 'glpi_groups';
        $sopt[1108]['field']     = 'completename';
        $sopt[1108]['itemlink']     = 'groups_id_observ';
	    $sopt[1108]['condition']     = 'is_assign';	
        $sopt[1108]['name']      = 'Grupo observador';	
		$sopt[1108]['datatype']      = 'dropdown';
		$sopt[1108]['massiveaction'] = false;
		$sopt[1108]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_escalados',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));
		$sopt[1109]['table']     = 'glpi_users';
        $sopt[1109]['field']     = 'completename';
        $sopt[1109]['itemlink']     = 'users_id_observ';
        $sopt[1109]['name']      = 'Usuario observador';	
		$sopt[1109]['datatype']      = 'dropdown';
		$sopt[1109]['massiveaction'] = false;
		$sopt[1109]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_escalados',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));		*/															  
		$sopt['seguimientos'] = 'Detalles seguimiento';
		
		$sopt[1110]['table']     = 'glpi_plugin_procedimientos_seguimientos';
        $sopt[1110]['field']     = 'content';
        $sopt[1110]['name']      = 'Descripci&oacute;n';	
		$sopt[1110]['datatype']      = 'text';
		$sopt[1110]['massiveaction'] = false;
		$sopt[1110]['joinparams']    = array('jointype' => 'child');

		$sopt[1111]['table']     = 'glpi_plugin_procedimientos_seguimientos';
        $sopt[1111]['field']     = 'is_private';
        $sopt[1111]['name']      = 'Es privado';	
		$sopt[1111]['datatype']      = 'bool';
		$sopt[1111]['massiveaction'] = false;
		$sopt[1111]['joinparams']    = array('jointype' => 'child');
		
		$sopt[1112]['table']     = 'glpi_requesttypes';
        $sopt[1112]['field']     = 'name';
        $sopt[1112]['name']      = 'Origen del seguimiento';	
		$sopt[1112]['datatype']  = 'dropdown';
		$sopt[1112]['massiveaction'] = false;
		$sopt[1112]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_seguimientos',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));		
		$sopt[1113]['table']     = 'glpi_followuptypes';
        $sopt[1113]['field']     = 'name';
        $sopt[1113]['name']      = 'Tipo de seguimiento';	
		$sopt[1113]['datatype']  = 'dropdown';
		$sopt[1113]['massiveaction'] = false;
		$sopt[1113]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_seguimientos',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));

		$sopt['modificar'] = 'Detalles Modificar Ticket';
		
		$sopt[1114]['table']     = 'glpi_requesttypes';
        $sopt[1114]['field']     = 'name';
        $sopt[1114]['name']      = 'Origen del ticket';	
		$sopt[1114]['datatype']  = 'dropdown';
		$sopt[1114]['massiveaction'] = false;
		$sopt[1114]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_updatetickets',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
														 'condition' => '')));																  

		$sopt[1115]['table']     = 'glpi_itilcategories';
        $sopt[1115]['field']     = 'name';
        $sopt[1115]['name']      = 'Categor&iacute;a del ticket';	
		$sopt[1115]['datatype']  = 'dropdown';
		$sopt[1115]['massiveaction'] = false;
		$sopt[1115]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_updatetickets',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
														 'condition' => '')));	 
		$sopt[1116]['table']     = 'glpi_itilcategories';
        $sopt[1116]['field']     = 'name';
        $sopt[1116]['name']      = 'Estado del ticket';	
		$sopt[1116]['datatype']  = 'dropdown';
		$sopt[1116]['massiveaction'] = false;
		$sopt[1116]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_updatetickets',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
														 'condition' => '')));	 														 
	}
	if ($itemtype == 'PluginProcedimientosProcedimiento') {
	    $sopt['visibilidad'] = 'Visibilidad';
		
		$sopt[1116]['table']     = 'glpi_groups';
        $sopt[1116]['field']     = 'completename';
	    $sopt[1116]['condition']     = 'is_assign';	
        $sopt[1116]['name']      = 'Grupo';	
        $sopt[1116]['forcegroupby']    = true;	
		$sopt[1116]["splititems"] = true;			
		$sopt[1116]['datatype']      = 'dropdown';
		$sopt[1116]['massiveaction'] = false;
		$sopt[1116]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_groups',
                                                 'joinparams'
                                                         => array('jointype'  => 'child', 'condition' => '')));	

		$sopt[1117]['table']     = 'glpi_ticketrecurrents';
        $sopt[1117]['field']     = 'name';
        $sopt[1117]['name']      = 'Ticket recurrente';	
		$sopt[1117]['datatype']      = 'dropdown';
        $sopt[1117]['forcegroupby']    = true;	
      //  $sopt[1117]['usehaving']    = true;	
		$sopt[1117]["splititems"] = true;		
		$sopt[1117]['massiveaction'] = false;
		$sopt[1117]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_ticketrecurrents',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
														 'condition' => '')));	 														  	
 
 		$sopt[1118]['table']     = 'glpi_plugin_formcreator_forms';
        $sopt[1118]['field']     = 'name';
        $sopt[1118]['name']      = 'Pedido de cat&aacute;logo';	
		$sopt[1118]['datatype']      = 'dropdown';
        $sopt[1118]['forcegroupby']    = true;	
		$sopt[1118]["splititems"] = true;					
		$sopt[1118]['massiveaction'] = false;
		$sopt[1118]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_forms',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));	 
	    $sopt['elementos'] = 'Elementos';
		$sopt[1120]['table']      = 'glpi_plugin_procedimientos_accions';
		$sopt[1120]['name']       =  __('Accion/es', 'Accion/es');
		$sopt[1120]['field']      = 'name';
        $sopt[1120]['datatype']   = 'itemlink';
		$sopt[1120]['linkfield']   = 'items_id';
		$sopt[1120]['forcegroupby']  = true;
		$sopt[1120]['massiveaction']  = false;
        $sopt[1120]['joinparams']     =  array('beforejoin' => array('table' => 'glpi_plugin_procedimientos_procedimientos_items',
																	 'joinparams' => array('jointype' => 'child', 
																	                       'condition' => "AND `NEWTABLE`.`itemtype`= 'PluginProcedimientosAccion'")));
		$sopt[1121]['table']      = 'glpi_plugin_procedimientos_condicions';
		$sopt[1121]['name']       =  __('Condicion/es', 'Condicion/es');
		$sopt[1121]['field']      = 'name';
        $sopt[1121]['datatype']   = 'itemlink';
		$sopt[1121]['linkfield']   = 'items_id';
		$sopt[1121]['forcegroupby']    = true;
		$sopt[1121]['massiveaction']  = false;
        $sopt[1121]['joinparams']     =  array('beforejoin' => array('table' => 'glpi_plugin_procedimientos_procedimientos_items',
																	 'joinparams' => array('jointype' => 'child', 
																	                       'condition' => "AND `NEWTABLE`.`itemtype`= 'PluginProcedimientosCondicion'")));
// [INICIO] JDMZ18G INFORGES  RELACIONADA CONSIGO MISMA Y CON OTRA TABLA
		$sopt[1119]['table']      = 'glpi_plugin_procedimientos_procedimientos';
		$sopt[1119]['name']       =  __('Procedimiento anidado', 'Procedimiento anidado');
		$sopt[1119]['field']      = "name";
        $sopt[1119]['datatype']   = 'itemlink';
		$sopt[1119]['itemlink']   = 'items_id';
		$sopt[1119]['massiveaction']  = false;
		$sopt[1119]['usehaving']       = true;
		$sopt[1119]['forcegroupby']    = true;
        $sopt[1119]['itemlink_type']  = 'PluginProcedimientosProcedimiento';
        $sopt[1119]['joinparams']     =  array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_items',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => "AND `NEWTABLE`.`itemtype`= 'PluginProcedimientosProcedimiento'")));	
// [FIN] JDMZ18G INFORGES																  
																						   
	}
    if (($itemtype == 'Ticket') && (Session::haveRight('plugin_procedimientos', READ))) {	
		$sopt['ptrabajo'] = 'Procedimientos de Trabajo';
			
		$sopt[1319]['table']      = 'glpi_plugin_procedimientos_procedimientos';
		$sopt[1319]['name']       =  __('Procedimiento relacionado', 'Procedimiento relacionado');
		$sopt[1319]['field']      = "name";
        $sopt[1319]['datatype']   = 'itemlink';
		$sopt[1319]['itemlink']   = 'plugin_procedimientos_procedimientos_id';
		$sopt[1319]['massiveaction']  = false;
        $sopt[1319]['itemlink_type']  = 'PluginProcedimientosProcedimiento';
        $sopt[1319]['joinparams']     =  array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_tickets',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));	
																  
		$sopt[1320]['table']      = 'glpi_plugin_procedimientos_procedimientos';
		$sopt[1320]['name']       =  __('Proc. Activo', 'Proc. Activo');
		$sopt[1320]['field']      = "active";
        $sopt[1320]['datatype']   = 'bool';
		$sopt[1320]['itemlink']   = 'plugin_procedimientos_procedimientos_id';
		$sopt[1320]['massiveaction']  = false;
        $sopt[1320]['itemlink_type']  = 'PluginProcedimientosProcedimiento';
        $sopt[1320]['joinparams']     =  array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_tickets',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));
																  
		$sopt[1321]['table']      = 'glpi_plugin_procedimientos_procedimientos';
		$sopt[1321]['name']       =  __('Proc. Borrado', 'Proc. Borrado');
		$sopt[1321]['field']      = "is_deleted";
        $sopt[1321]['datatype']   = 'bool';
		$sopt[1321]['itemlink']   = 'plugin_procedimientos_procedimientos_id';
		$sopt[1321]['massiveaction']  = false;
        $sopt[1321]['itemlink_type']  = 'PluginProcedimientosProcedimiento';
        $sopt[1321]['joinparams']     =  array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_tickets',
                                                 'joinparams'
                                                         => array('jointype'  => 'child', 'condition' => '')));	
																
	} 
    if (($itemtype == 'TicketRecurrent') && (Session::haveRight('plugin_procedimientos', READ))) {	
		$sopt['ptrabajo'] = 'P.Trabajo';
			
		$sopt[1323]['table']      = 'glpi_plugin_procedimientos_procedimientos';
		$sopt[1323]['name']       =  __('Procedimiento', 'Procedimiento');
		$sopt[1323]['field']      = "name";
        $sopt[1323]['datatype']   = 'itemlink';
		$sopt[1323]['itemlink']   = 'plugin_procedimientos_procedimientos_id';
		$sopt[1323]['massiveaction']  = false;
		$sopt[1323]['usehaving']       = true;
		$sopt[1323]['forcegroupby']    = true;
        $sopt[1323]['itemlink_type']  = 'PluginProcedimientosProcedimiento';
        $sopt[1323]['joinparams']     =  array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_ticketrecurrents',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));		
  	}
    if (($itemtype == 'PluginFormcreatorForm') && (Session::haveRight('plugin_procedimientos', READ))) {	
		$sopt['ptrabajo'] = 'P.Trabajo';
			
		$sopt[1324]['table']      = 'glpi_plugin_procedimientos_procedimientos';
		$sopt[1324]['name']       =  __('Procedimiento', 'Procedimiento');
		$sopt[1324]['field']      = "name";
        $sopt[1324]['datatype']   = 'itemlink';
		$sopt[1324]['itemlink']   = 'plugin_procedimientos_procedimientos_id';
		$sopt[1324]['massiveaction']  = false;
		$sopt[1324]['usehaving']       = true;
		$sopt[1324]['forcegroupby']    = true;
        $sopt[1324]['itemlink_type']  = 'PluginProcedimientosProcedimiento';
        $sopt[1324]['joinparams']     =  array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_procedimientos_forms',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));		
  	}	
	return $sopt;
}


/***********************************************************************************************************************************
Función que se ejecuta cuando actualizamos una validación en un ticket. 
**************************************************************************************************************************************/
function plugin_procedimientos_update_Validation($item) {
	global $DB;
	$id = $item->getField('id');
	$state = $item->getField('status');
	$tickets_id = $item->getField('tickets_id');
	Toolbox::logInFile("procedimientos", "Validación de Ticket con ID ".$id." modifica su estado (".$state.") en el ticket con ID ".$tickets_id. "\n");

	if ($state > 2){ // Estado "Concedido o Rechazado"		
		$procedimientos_id = get_procedimiento_principal($tickets_id); 
		if (isset($procedimientos_id)){ // Si existe un procedimiento ejecutándose para ese ticket.			
			$select = "SELECT id from `glpi_plugin_procedimientos_procedimientos_tickets`
					   WHERE tickets_id=".$tickets_id." and itemtype='PluginProcedimientosAccion' and instancia_id=".$id." and state=2;";					   
			$result_select = $DB->query($select);
            Toolbox::logInFile("procedimientos", "Select: ".$select. "\n");			
            $row = $DB->fetch_array($result_select);
			if (isset($row['id'])){
				$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET `state`=1 
					   WHERE id=".$row['id'].";";
				$result_update = $DB->query($update);
				ejecutar_Procedimiento($tickets_id);			
			}
		}
	}
	return true;	
		
}

/***********************************************************************************************************************************
Función que se ejecuta antes de actualizar una tarea en un ticket. 
CONTROLAMOS LA PRE-ACTUALIZACIÓN DE UNA TAREA PARA AÑADIR COMO TECNICO DE LA TAREA AL USUARIO QUE LA CIERRA Y SU GRUPO POR DEFECTO Y PARA AÑADIR AL USUARIO QUE LA MODIFICA EN CASO DE SER CERRADA CON CHEKBOX	
**************************************************************************************************************************************/
function plugin_procedimientos_pre_update_TicketTask($item) {

		$users_id = Session::getLoginUserID();

	   if ((isset($item->input["state"])) 
			and ($item->input["state"] == Planning::DONE)
			and ($item->input["state"] <> $item->fields["state"])
			and (((empty($item->fields["users_id_tech"])) and (empty($item->input["users_id_tech"])))
			 or ((!empty($item->fields["users_id_tech"])) and (empty($item->input["users_id_tech"]))))
			) { 

				if ((!empty($item->fields["users_id_tech"]))
					and (empty($item->input["users_id_tech"]))) {
			
					if (isset($item->input["check"])) {
						$item->input["users_id_tech"] = $item->fields["users_id_tech"];
						unset($item->input["check"]);
					} else {	
						$item->input["users_id_tech"] = $users_id;	
					}	
					
				} else {
					$item->input["users_id_tech"] = $users_id;
				}
	   }

	   if (!isset($item->input["users_id_editor"])) { 
			$item->input["users_id_editor"] = $users_id;
	   }	   

		 if ((isset($item->input["state"])) 
		 and ($item->input["state"] == Planning::DONE)) {
		
		 $user = new User;
		 $user->getFromDB($item->input["users_id_tech"]);
		 $groups_id = $user->getField('groups_id');
 
		 if ((($groups_id <> $item->fields["groups_id_tech"]) ||
		 ((isset($item->input["groups_id_tech"])) and ($item->input["groups_id_tech"]<>$groups_id)))
		 and ($groups_id>0))  {		
			 
				if ((!isset($item->input["status"])) and (isset($item->input["_status"]))) {
					$item->input["status"] = $item->input["_status"];
				}
			
				if (empty($item->fields["groups_id_tech"])) {
				/*if ((!empty($item->fields["groups_id_tech"]))
					 and (empty($item->input["groups_id_tech"]))) {*/
			 
					 if (isset($item->input["check"])) {
						 $item->input["groups_id_tech"] = $groups_id;
						 unset($item->input["check"]);
					 } else {	
						 $item->input["groups_id_tech"] = $groups_id;	
					 }	
					 
				 } else {
					// $item->input["groups_id_tech"] = $groups_id;
				 }
 
		 }
			 
		}

		aviso_grupo($item);

	   return $item;
}

function plugin_procedimientos_pre_add_TicketTask($item) {

	$users_id = Session::getLoginUserID();

	 if ((isset($item->input["state"])) 
		and ($item->input["state"] == Planning::DONE)		
		and (((empty($item->fields["users_id_tech"])) and (empty($item->input["users_id_tech"])))
		 or ((!empty($item->fields["users_id_tech"])) and (empty($item->input["users_id_tech"]))))
		) { 

			if ((!empty($item->fields["users_id_tech"]))
				and (empty($item->input["users_id_tech"]))) {
		
				if (isset($item->input["check"])) {
					$item->input["users_id_tech"] = $item->fields["users_id_tech"];
					unset($item->input["check"]);
				} else {	
					$item->input["users_id_tech"] = $users_id;	
				}	
				
			} else {
				$item->input["users_id_tech"] = $users_id;
			}
	 }

	 if (!isset($item->input["users_id_editor"])) { 
		$item->input["users_id_editor"] = $users_id;
	 }	   

	 if ((isset($item->input["state"])) 
		and ($item->input["state"] == Planning::DONE)) {
	 
		$user = new User;
		$user->getFromDB($item->input["users_id_tech"]);
		$groups_id = $user->getField('groups_id');

		if (($groups_id <> $item->input["groups_id_tech"] ) and ($groups_id>0)) {

				if (empty($item->fields["groups_id_tech"])) {	

				/*if ((!empty($item->fields["groups_id_tech"]))
					and (empty($item->input["groups_id_tech"]))) {*/
			
					if (isset($item->input["check"])) {
						$item->input["groups_id_tech"] = $item->fields["groups_id_tech"];
						unset($item->input["check"]);
					} else {	
						$item->input["groups_id_tech"] = $groups_id;	
					}	
					
				} else {
				 //	$item->input["groups_id_tech"] = $groups_id;
				}

		}
			
	 }

	 aviso_grupo($item);

	 return $item;
}

/***********************************************************************************************************************************
Función que se ejecuta cuando actualizamos una tarea en un ticket. 
**************************************************************************************************************************************/
function plugin_procedimientos_update_TicketTask($item) {
    global $DB;
	$id = $item->getField('id');
	$state = $item->getField('state');
	$tickets_id = $item->getField('tickets_id');
	Toolbox::logInFile("procedimientos", "Tarea de Ticket con ID ".$id." modifica su estado (".$state.") en el ticket con ID ".$tickets_id. "\n"); 
	if ($state == 2){ // Estado "Hecho"		
		$procedimientos_id = get_procedimiento_principal($tickets_id); 
		if (isset($procedimientos_id)){ // Si existe un procedimiento ejecutándose para ese ticket.			
			$select = "SELECT id from `glpi_plugin_procedimientos_procedimientos_tickets`
					   WHERE tickets_id=".$tickets_id." and itemtype='PluginProcedimientosAccion' and instancia_id=".$id." and state=2;";					   
			$result_select = $DB->query($select);
            Toolbox::logInFile("procedimientos", "Select: ".$select. "\n");			
            $row = $DB->fetch_array($result_select);
			if (isset($row['id'])){
				$update = "UPDATE `glpi_plugin_procedimientos_procedimientos_tickets` SET `state`=1 
					   WHERE id=".$row['id'].";";
				$result_update = $DB->query($update);
				ejecutar_Procedimiento($tickets_id);			
			}
		}
	}
	return true;
}

/***********************************************************************************************************************************
Función que encuentra el id del procedimiento asociado en la descripción de un destino FORMCREATOR
**************************************************************************************************************************************/

function plugin_procedimientos_destination($description) {
	$content = explode("[Procedimiento de trabajo asociado",$description);
	if (count($content)>1){
		return intval(preg_replace("/[^0-9]/", "",$content[1]));
	} else {
		return 0;	
	}
}

/***********************************************************************************************************************************
Función que se ejecuta cuando creamos un ticket. 
**************************************************************************************************************************************/
function plugin_procedimientos_add_Ticket($item) {
    global $DB;	
	
	$tickets_id = $item->getField('id');
	$ticket = new Ticket; 
	$ticket->getFromDB($tickets_id);
	$entities_id = $ticket->fields['entities_id'];

	$pedido = false;

   // *******************************************************************************************
   //  [INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
   // *******************************************************************************************	

/*	$procedimientos_id = plugin_procedimientos_destination($ticket->fields['content']);
	
	if ($procedimientos_id>0) {
		
		$procedure = new PluginProcedimientosProcedimiento;

		$params = [
		"id" => $procedimientos_id,
		"active" => 1,
		"entities_id" => $entities_id,
		"is_deleted" => 0
		];

		$procedimiento = $procedure->find($params);

		if (!empty($procedimiento)) {

			instancia_procedimiento($procedimientos_id, $tickets_id);
			ejecutar_Procedimiento($tickets_id);				
			$pedido = true;

		}				
		
	} */
/*	
	// Buscamos id pedido de catalogo del ticket (si lo hay)
	if ($DB->TableExists("glpi_plugin_formcreator_forms") && $DB->TableExists("glpi_plugin_formcreator_forms_items") && ($pedido == false)) {
		// Buscamos si dicho pedido de catálogo está en algún procedimiento de trabajo. si es así lo mostramos
		if (isset($_POST['formcreator_form'])){
		//if (isset($_POST['plugin_formcreator_forms_id'])){ 
		//para la migración formcreator	
			$plugin_formcreator_forms_id = $_POST['formcreator_form'];
			//$plugin_formcreator_forms_id = $_POST['plugin_formcreator_forms_id'];
			//para la migración formcreator
			if (isset($plugin_formcreator_forms_id)){
				$query ="SELECT 
					`plugin_procedimientos_procedimientos_id`
					FROM `glpi_plugin_procedimientos_procedimientos_forms`
					INNER JOIN `glpi_plugin_procedimientos_procedimientos` on
					( `glpi_plugin_procedimientos_procedimientos`.`id`= `glpi_plugin_procedimientos_procedimientos_forms`.`plugin_procedimientos_procedimientos_id`)
					where 
					`glpi_plugin_procedimientos_procedimientos`.`is_deleted`= 0 and `glpi_plugin_procedimientos_procedimientos`.`active`= 1 and 
			        `glpi_plugin_procedimientos_procedimientos`.`entities_id` = $entities_id and 		
					`glpi_plugin_procedimientos_procedimientos_forms`.`plugin_formcreator_forms_id`='".$plugin_formcreator_forms_id."';";
					
					/*echo $query;
					exit();*/
					
		/*		$result = $DB->query($query);
				$row = $DB->fetch_array($result);		
				if (isset($row['plugin_procedimientos_procedimientos_id'])){
					$procedimientos_id = $row['plugin_procedimientos_procedimientos_id'];
					// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
					$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
					$DB->query($query);
					// Instanciamos y ejecutamos procedimiento correspondiente.
					instancia_procedimiento($procedimientos_id, $tickets_id);
					ejecutar_Procedimiento($tickets_id);				
					$pedido = true;
				}
			}
		}
	} */	 

	// Buscamos id pedido de catalogo del ticket (si lo hay)
	if ($DB->TableExists("glpi_plugin_formcreator_forms") && $DB->TableExists("glpi_plugin_formcreator_forms_items")) {
		// Buscamos si dicho pedido de catálogo está en algún procedimiento de trabajo. si es así lo mostramos
				
		if ((isset($_POST['plugin_formcreator_forms_id'])) and (isset($_POST['plugin_formcreator_targettickets_id']))){ 

			$plugin_formcreator_forms_id         = $_POST['plugin_formcreator_forms_id'];
			$plugin_formcreator_targettickets_id = $_POST['plugin_formcreator_targettickets_id'];
			
			//Toolbox::logInFile("procedimientos", " _POST: " . print_r($_POST, TRUE) . "\r\n\r\n"); 
			
			$query ="SELECT 
			`plugin_procedimientos_procedimientos_id`
			FROM `glpi_plugin_procedimientos_procedimientos_forms`
			INNER JOIN `glpi_plugin_procedimientos_procedimientos` on
			( `glpi_plugin_procedimientos_procedimientos`.`id`= `glpi_plugin_procedimientos_procedimientos_forms`.`plugin_procedimientos_procedimientos_id`)
			where 
			`glpi_plugin_procedimientos_procedimientos`.`is_deleted`= 0 and `glpi_plugin_procedimientos_procedimientos`.`active`= 1 and 
					`glpi_plugin_procedimientos_procedimientos`.`entities_id` = $entities_id and 		
			`glpi_plugin_procedimientos_procedimientos_forms`.`plugin_formcreator_forms_id`='".$plugin_formcreator_forms_id."' and 
			`glpi_plugin_procedimientos_procedimientos_forms`.`plugin_formcreator_targettickets_id`='".$plugin_formcreator_targettickets_id."';";
			
			$result = $DB->query($query);
			
			$row = $DB->fetch_array($result);

			if (isset($row['plugin_procedimientos_procedimientos_id'])){
					$procedimientos_id = $row['plugin_procedimientos_procedimientos_id'];
					// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
					$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
					$DB->query($query);
					// Instanciamos y ejecutamos procedimiento correspondiente.
					instancia_procedimiento($procedimientos_id, $tickets_id);
					ejecutar_Procedimiento($tickets_id);				
					$pedido = true;
			
			} else {

				$procedimientos_id = plugin_procedimientos_destination($ticket->fields['content']);
				//Toolbox::logInFile("procedimientos", " procedimientos_id: " . $procedimientos_id . "\r\n\r\n"); 
				if ($procedimientos_id>0) {
					
					$procedure = new PluginProcedimientosProcedimiento;
			
					$params = [
					"id" => $procedimientos_id,
					"active" => 1,
					"entities_id" => $entities_id,
					"is_deleted" => 0
					];
			
					$procedimiento = $procedure->find($params);
			
					if (!empty($procedimiento)) {
			
						instancia_procedimiento($procedimientos_id, $tickets_id);
						ejecutar_Procedimiento($tickets_id);				
						$pedido = true;
			
					}				
					
				}

			}

		} else {

			$procedimientos_id = plugin_procedimientos_destination($ticket->fields['content']);
			//Toolbox::logInFile("procedimientos", " procedimientos_id: " . $procedimientos_id . "\r\n\r\n"); 
			if ($procedimientos_id>0) {
				
				$procedure = new PluginProcedimientosProcedimiento;
		
				$params = [
				"id" => $procedimientos_id,
				"active" => 1,
				"entities_id" => $entities_id,
				"is_deleted" => 0
				];
		
				$procedimiento = $procedure->find($params);
		
				if (!empty($procedimiento)) {
		
					instancia_procedimiento($procedimientos_id, $tickets_id);
					ejecutar_Procedimiento($tickets_id);				
					$pedido = true;
		
				}				
				
			}

		}

	}
   // *******************************************************************************************
   //  [FINAL] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
   // *******************************************************************************************	


	if ($pedido == false) {			
		// Comprobamos si el ticket procede de un "Ticket recurrente", para ello comprobamos su nombre en la tabla "glpi_ticketrecurrents"
		$origen = $item->getField('requesttypes_id');
		if ($origen == 8){ // Solo si el origen es glpi (8) es ticket recurrente

			//$entity = $_SESSION["glpiactive_entity"];
			$entity = $item->getField('entities_id');
			
			// posibles nombres de los distintos tickets_recurrentes
			$query_tt_names ="SELECT name, value,  glpi_ticketrecurrents.tickettemplates_id, glpi_ticketrecurrents.id as ticketrecurrents_id
							  FROM glpi_ticketrecurrents
							  JOIN glpi_tickettemplatepredefinedfields on (glpi_ticketrecurrents.tickettemplates_id = glpi_tickettemplatepredefinedfields.tickettemplates_id)
							  WHERE glpi_tickettemplatepredefinedfields.num=1 and entities_id=".$entity." and is_active=1";

			$result_tt = $DB->query($query_tt_names);
			$num_row_tt = $DB->numrows($result_tt);			
			if ($num_row_tt > 0){
				$encontrado = FALSE;
				$name_ticket = $item->getField('name');
				while (($row_tt = $DB->fetch_array($result_tt))&& ($encontrado == FALSE)) {
					//Tratamos el campo value para modificar los parametros dd, mm, aaaa con los datos del ticket
					$date_ticket = $item->getField('date');
					$date = strtotime($date_ticket);
					$dia = "[".date("Y", $date)."]";
					$mes = "[".date("m", $date)."]";
					$year = "[".date("d", $date)."]";
					$caso_especial = date("d", $date)."/".date("m", $date);
					
					$nombre_plantilla = str_replace("dd/mm-dd/mm]", $caso_especial, $row_tt['value']);					
					$nombre_plantilla = str_replace("[dd]", $dia, $nombre_plantilla);
					$nombre_plantilla = str_replace("[mm]", $mes, $nombre_plantilla);
					$nombre_plantilla = str_replace("[aaaa]", $year, $nombre_plantilla);
					
					//echo "<br>Nombre plantilla tratada: ".$nombre_plantilla."<br>";
					$pos = strpos($name_ticket, $nombre_plantilla);
					
					if (($name_ticket == $row_tt['name'])||($pos!== FALSE)){ // Nombre del ticket igual al del t.recurrente o el definido en su plantilla
						$encontrado = TRUE;
						if (isset($row_tt["ticketrecurrents_id"])){ 
							$query2 = "SELECT plugin_procedimientos_procedimientos_id 
							FROM glpi_plugin_procedimientos_procedimientos_ticketrecurrents
							INNER JOIN glpi_plugin_procedimientos_procedimientos on 
							(glpi_plugin_procedimientos_procedimientos_ticketrecurrents.plugin_procedimientos_procedimientos_id = glpi_plugin_procedimientos_procedimientos.id)
							where ticketrecurrents_id=".$row_tt["ticketrecurrents_id"]." and glpi_plugin_procedimientos_procedimientos.is_deleted=0 and 
							glpi_plugin_procedimientos_procedimientos.active=1;";
							
							$result2 = $DB->query($query2);
							$row2 = $DB->fetch_array($result2);
							if (isset($row2["plugin_procedimientos_procedimientos_id"])){
								$procedimientos_id = $row2["plugin_procedimientos_procedimientos_id"];
								// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
								$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
								$DB->query($query);
								// Instanciamos y ejecutamos procedimiento correspondiente.
								instancia_procedimiento($procedimientos_id, $tickets_id);
								ejecutar_Procedimiento($tickets_id);
							}
						}						
					}				
				}
			}				
		}
	}
	return true;
}
/***********************************************************************************************************************************
Función que se ejecuta cuando actualizamos un ticket. 
Comprueba si ha cambiado el pedido de catálogo.
**************************************************************************************************************************************/

function plugin_procedimientos_update_Ticket($item) {
	global $DB;

$tickets_id = $item->getField('id');	

if ((isset($_POST["actualizarPedido"]))
				&&($_POST["actualizarPedido"]=='Actualizar')
				&&(isset($_POST["plugin_formcreator_forms_id"]))
				&&(isset($_POST["plugin_formcreator_targettickets_ids"]))){
		// Buscamos id pedido de catalogo del ticket (si lo hay)
		Toolbox::logInFile("procedimientos", " update_Ticket_POST : ".print_r($_POST, TRUE) ."\r\n\r\n"); 
		$relation = new PluginProcedimientosProcedimiento_Form();

		$params = [
      // "plugin_procedimientos_procedimientos_id" => $values['plugin_procedimientos_procedimientos_id'],
         "plugin_formcreator_forms_id" => $_POST['plugin_formcreator_forms_id'],
         "plugin_formcreator_targettickets_id" => $_POST['plugin_formcreator_targettickets_id']
         ];
   
    $procedure = $relation->find($params);

		if (!empty($procedure)) {

			//Toolbox::logInFile("procedimientos", " procedure : ".print_r($procedure, TRUE) ."\r\n\r\n"); 

					$item = current($procedure);
					$procedimientos_id = $item['plugin_procedimientos_procedimientos_id'];
					
					$select_proc = "SELECT plugin_procedimientos_procedimientos_id 
									FROM glpi_plugin_procedimientos_procedimientos_tickets
									where tickets_id=".$tickets_id."
									order by id;";
					$result_proc = $DB->query($select_proc);
					$num_rows = $DB->numrows($result_proc);
	
					if ($num_rows > 0){	
						$proc_actual = $DB->fetch_array($result_proc);
						$proc_actual_ID = $proc_actual['plugin_procedimientos_procedimientos_id'];
						
						if ($procedimientos_id != $proc_actual_ID){
							// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
							$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
							$DB->query($query);
							// Instanciamos y ejecutamos procedimiento correspondiente.
							// echo "<br>En updateTicket<br>";
							//echo "<br>procedimientos_id <> proc_actual_ID<br>";
							instancia_procedimiento($procedimientos_id, $tickets_id);
							ejecutar_Procedimiento($tickets_id);
							//unset($_POST["actualizarPedido"]);
							//unset($_POST["peticion_id"]);
							$pedido = true;
						}
					} else {
						instancia_procedimiento($procedimientos_id, $tickets_id);
						ejecutar_Procedimiento($tickets_id);
					}
				} else { // emb97m - INFORGES - No hay procedimiento para el pedido de catálogo.
					// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
					$query_proc = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
					$DB->query($query_proc);						
				}
		}
		return true;
	}

/*function plugin_procedimientos_update_Ticket($item) {
    global $DB;

	$tickets_id = $item->getField('id');	

	if ((isset($_POST["actualizarPedido"]))&&($_POST["actualizarPedido"]=='Actualizar')&&(isset($_POST["peticion_id"]))){
			// Buscamos id pedido de catalogo del ticket (si lo hay)
			Toolbox::logInFile("procedimientos", " update_Ticket_POST : ".print_r($_POST, TRUE) ."\r\n\r\n"); 
			if ($DB->TableExists("glpi_plugin_formcreator_forms") && $DB->TableExists("glpi_plugin_formcreator_forms_items")) {
					// Buscamos si dicho pedido de catálogo está en algún procedimiento de trabajo. si es así lo mostramos
					// CAMINO1
					$query = "SELECT 
						`glpi_plugin_procedimientos_procedimientos`.`id` as id_proc, 
						`glpi_plugin_procedimientos_procedimientos`.`name` as nombre 
						FROM `glpi_plugin_formcreator_forms` inner join `glpi_plugin_formcreator_forms_items`
						on `glpi_plugin_formcreator_forms`.`id`=`glpi_plugin_formcreator_forms_items`.`plugin_formcreator_forms_id`
						inner join `glpi_plugin_procedimientos_procedimientos_forms`
						on `glpi_plugin_procedimientos_procedimientos_forms`.`plugin_formcreator_forms_id`= `glpi_plugin_formcreator_forms_items`.`plugin_formcreator_forms_id`
						inner join `glpi_plugin_procedimientos_procedimientos`
						on `glpi_plugin_procedimientos_procedimientos_forms`.`plugin_procedimientos_procedimientos_id`=`glpi_plugin_procedimientos_procedimientos`.`id`
						where `glpi_plugin_formcreator_forms_items`.`itemtype`='Ticket' and `glpi_plugin_formcreator_forms`.`is_active`=1
						and `glpi_plugin_procedimientos_procedimientos`.`is_deleted`=0  and `glpi_plugin_formcreator_forms_items`.`items_id`='".$tickets_id."';";
	
					$result = $DB->query($query);
					$row = $DB->fetch_array($result);		
					if (isset($row['id_proc'])){
						$procedimientos_id = $row['id_proc'];
						
						$select_proc = "SELECT plugin_procedimientos_procedimientos_id 
										FROM glpi_plugin_procedimientos_procedimientos_tickets
										where tickets_id=".$tickets_id."
										order by id;";
						$result_proc = $DB->query($select_proc);
						$num_rows = $DB->numrows($result_proc);
		
						if ($num_rows > 0){	
							$proc_actual = $DB->fetch_array($result_proc);
							$proc_actual_ID = $proc_actual['plugin_procedimientos_procedimientos_id'];
							
							if ($procedimientos_id != $proc_actual_ID){
								// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
								$query = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
								$DB->query($query);
								// Instanciamos y ejecutamos procedimiento correspondiente.
								// echo "<br>En updateTicket<br>";
								//echo "<br>procedimientos_id <> proc_actual_ID<br>";
								instancia_procedimiento($procedimientos_id, $tickets_id);
								ejecutar_Procedimiento($tickets_id);
								//unset($_POST["actualizarPedido"]);
								//unset($_POST["peticion_id"]);
								$pedido = true;
							}
						} else {
							instancia_procedimiento($procedimientos_id, $tickets_id);
							ejecutar_Procedimiento($tickets_id);
						}
					} else { // emb97m - INFORGES - No hay procedimiento para el pedido de catálogo.
						// Borramos de los elementos de posibles anteriores procedimientos asociados al ticket correspondiente
						$query_proc = "delete from glpi_plugin_procedimientos_procedimientos_tickets where tickets_id=".$tickets_id;
						$DB->query($query_proc);						
					}
			}
	}
	return true;
}*/

////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////

function plugin_procedimientos_MassiveActions($type) {
   switch ($type) {
	  
      case 'PluginProcedimientosProcedimiento' :
		if (Session::haveRight("plugin_procedimientos",UPDATE)) {             
                return array(
                'PluginProcedimientosProcedimiento'.MassiveAction::CLASS_ACTION_SEPARATOR.'plugin_procedimientos_renumerar' => 'Renumerar lineas',
                'PluginProcedimientosProcedimiento'.MassiveAction::CLASS_ACTION_SEPARATOR.'Export' => _sx('button', 'Export')
                            );
		}
		break;
   }
   return array();
}	
	
   // *******************************************************************************************
   //  [INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
   // *******************************************************************************************  
	 // Captura del evento modificar el destino de un formulario de formcreator.
	 function plugin_procedimientos_update_TargetTicket($item) {

		global $DB;
		$params = [
			"plugin_formcreator_targettickets_id" => $item->getField('id'),
			"plugin_formcreator_forms_id" => $item->getField('plugin_formcreator_forms_id'),
			"plugin_procedimientos_procedimientos_id" => $item->getField('plugin_procedimientos_procedimientos_id'),
			];
		
		Toolbox::logInFile("procedimientos", "El destino ".$params['plugin_formcreator_targettickets_id'] ." del pedido de catálogo ".$params['plugin_formcreator_forms_id']." modifica su procedimiento al ID (".$params['plugin_procedimientos_procedimientos_id'].") \n"); 
		
		if (isset($item->oldvalues['plugin_procedimientos_procedimientos_id'])) { // SI SE HA MODIFICADO EL Procedimiento Asociado
	
			$PluginProcedimientosProcedimiento_Form = new PluginProcedimientosProcedimiento_Form();
	
			$find_params = [
				"plugin_formcreator_targettickets_id" => $item->getField('id'),
				"plugin_formcreator_forms_id" => $item->getField('plugin_formcreator_forms_id')
				];
	
			$procedure = $PluginProcedimientosProcedimiento_Form->find($find_params);
	
			if (!empty($procedure)) { 
				
				$item = current($procedure);
				$params['id'] = $item['id'];
	
				if ($params['plugin_procedimientos_procedimientos_id'] > 0){ // Si hemos enlazado un procedimiento 		
				
					$PluginProcedimientosProcedimiento_Form->update($params);
				
				} else {
	
					$PluginProcedimientosProcedimiento_Form->delete($params);
	
				}
			
			} else {
	
				if ($params['plugin_procedimientos_procedimientos_id'] > 0){


					$procedure = new PluginProcedimientosProcedimiento();
					$procedure->getFromDB($params['plugin_procedimientos_procedimientos_id']); 
		
					$target = new PluginFormcreatorTargetTicket();
					$target->getFromDB($params['plugin_formcreator_targettickets_id']);   					

					$params_message = [
            "header" 		=> sprintf(__("<H3>Detalles de la relación creada:</H3>","procedimiento")),
            "message"   => sprintf(__("<strong>Destino:</strong> <br><br><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/targetticket.form.php?id=".$params['plugin_formcreator_targettickets_id']."'> %s </a></font><br><br><strong>Procedimiento:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/procedimientos/front/procedimiento.form.php?id=".$item->input['plugin_procedimientos_procedimientos_id']."'> %s </a></font><br>","procedimiento"),$target->fields['name'] ,$procedure->fields["name"]),
            "footer" 		=> sprintf(__("","procedimiento"),$target->fields['name'])
            ];	
         
         Session::addMessageAfterRedirect(PluginProcedimientosProcedimiento_Form::plugin_procedimientos_get_message($params_message, "s" , $color = '#076301'), false, INFO); 						
	
					$PluginProcedimientosProcedimiento_Form->add($params);
				
				}
	
			}
	
		}
		return true;
	}
		
	// Captura del evento eliminar un destino de un formulario de formcreator.
	function plugin_procedimientos_delete_TargetTicket($item) {
		global $DB;
		$PluginProcedimientosProcedimiento_Form = new PluginProcedimientosProcedimiento_Form();
	
		$params = [
			"plugin_formcreator_targettickets_id" => $item->getField('id'),
			"plugin_formcreator_forms_id" => $item->getField('plugin_formcreator_forms_id')
			];
	
		$procedure = $PluginProcedimientosProcedimiento_Form->find($params);
	
		if (!empty($procedure)) { 
			$item = current($procedure);
			$params['id'] = $item['id'];		
			Toolbox::logInFile("procedimientos", "El destino ". $params['plugin_formcreator_targettickets_id'] .
												 " del pedido de catálogo ".$params['plugin_formcreator_forms_id'].
												 " ha sido eliminado y tambien su relación con el" .
												 " procedimiento: ID (".$item['plugin_procedimientos_procedimientos_id'].") \n"); 
			
			$sql = "DELETE FROM `glpi_plugin_procedimientos_procedimientos_forms` WHERE (`id` = '" . $item['id'] . "')";
			$DB->query($sql);
			// Comentado debido a que tambien se ejecuta la función plugin_procedimientos_delete_RelationForm
			//$PluginProcedimientosProcedimiento_Form->delete($params);
	
		}
	
		return true;
	}
	
	// Captura del evento pre-actualización de la relación entre pedido de catálogo y procedimiento.
	function plugin_procedimientos_update_RelationForm($item) {
	
		global $DB;
		//Toolbox::logInFile("procedimientos", " parseAnswerValues: " . print_r($item, TRUE) . "\r\n\r\n"); 
		if ($item->input["plugin_formcreator_targettickets_id"]<1) {
		
			$params = [
				"header" 	=> sprintf(__("El campo destino es requerido:","procedimiento")),
				"message" => sprintf(__("No es posible incluir una relación sin destino.","procedimiento")),
				"footer" 	=> sprintf(__("!La relación solicitada no se ha modificado!","procedimiento"))
				];	
			
			//Toolbox::logInFile("procedimientos", " input: " . print_r($item->input, TRUE) . "\r\n\r\n"); 
	
			Session::addMessageAfterRedirect(PluginProcedimientosProcedimiento_Form::plugin_procedimientos_get_message($params, "!"), false, ERROR); 	
			$item->input = [];
			return false;
		
		}
		
		$new_target   = (isset($item->input["plugin_formcreator_targettickets_id"]) ? $item->input["plugin_formcreator_targettickets_id"] : $item->fields['plugin_formcreator_targettickets_id'] );
		$old_target   = $item->fields['plugin_formcreator_targettickets_id'];
		$procedure_id = (isset($item->input["plugin_procedimientos_procedimientos_id"]) ? $item->input["plugin_procedimientos_procedimientos_id"] : $item->fields['plugin_procedimientos_procedimientos_id'] );

		$target = new PluginFormcreatorTargetTicket();
		$target->getFromDB($new_target);  
	
		$form = new PluginFormcreatorForm();
		$form->getFromDB($target->fields['plugin_formcreator_forms_id']); 
	
		$procedure  = new PluginProcedimientosProcedimiento();
		$procedure->getFromDB($procedure_id); 
		//Toolbox::logInFile("procedimientos", " procedure: " . print_r($procedure, TRUE) . "\r\n\r\n"); 
		$params = [
			"plugin_formcreator_forms_id" 						=> $target->fields['plugin_formcreator_forms_id'],
			"plugin_formcreator_targettickets_id" 		=> $new_target,
			"plugin_procedimientos_procedimientos_id" => $procedure_id
			];
	
		$relation = $item->find($params);
		//Toolbox::logInFile("procedimientos", " params: " . print_r($params, TRUE) . "\r\n\r\n"); 

		//Toolbox::logInFile("procedimientos", " parseAnswerValues: " . print_r($relation, TRUE) . "\r\n\r\n"); 
	
		if (empty($relation)) {
		
		$sql = "UPDATE `glpi_plugin_formcreator_targettickets` SET `plugin_procedimientos_procedimientos_id` = '0' WHERE (`id` = $old_target)";
		$DB->query($sql);
	
		$sql = "UPDATE `glpi_plugin_formcreator_targettickets` SET `plugin_procedimientos_procedimientos_id` = '" . $procedure_id . "' WHERE (`id` = '".$new_target."')";
		$DB->query($sql);
	
		$sql = 	"UPDATE `glpi_plugin_procedimientos_procedimientos_forms` 
		SET `plugin_formcreator_forms_id` = '" . $target->fields['plugin_formcreator_forms_id'] . "',
				`plugin_formcreator_targettickets_id` = '" . $new_target . "' 
		WHERE (`id` = '".$item->fields["id"]."')";
	
		$DB->query($sql);
	//	$text = sprintf(__("<strong>Pedido de catálogo:</strong> <br><br><font color = '#076301'> %s </font><br><br><strong>Destino:</strong> <br><br><font color = '#076301'> %s </font><br>","procedimiento"),$form->fields['name'] ,$target->fields['name']);
		$text = sprintf(__("<strong>Destino:</strong> <br><br><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/targetticket.form.php?id=".$new_target."'> %s </a></font><br><br><strong>Procedimiento:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/procedimientos/front/procedimiento.form.php?id=".$procedure_id."'> %s </a></font><br>","procedimiento"),$form->fields['name'] ,$procedure->fields["name"]);
		$params = [
			"header" 	=> sprintf(__("<H3>Detalles de la relación modificada:</H3>","procedimiento")),
			"message" => $text,
			"footer" 	=> sprintf(__("","procedimiento"),$target->fields['name'])
			];	
		
		Session::addMessageAfterRedirect(PluginProcedimientosProcedimiento_Form::plugin_procedimientos_get_message($params, "s" , $color = '#076301'), false, INFO); 		
	
		return true;
	
		} else {
					
			$item->input = [];
	
			$params = [
				"header" 	=> sprintf(__("Este procedimiento ya contiene la siguiente relación solicitada:","procedimiento")),
				"message"   => sprintf(__("<strong>Pedido de catálogo:</strong> <br><br><font color = '#7c0068'> %s </font><br><br><strong>Destino:</strong> <br><br><font color = '#7c0068'> %s </font><br>","procedimiento"),$form->fields['name'] ,$target->fields['name']),
				"footer" 	=> sprintf(__("!La relación solicitada no se ha modificado!","procedimiento"))
				];			
			
			Session::addMessageAfterRedirect(PluginProcedimientosProcedimiento_Form::plugin_procedimientos_get_message($params, "!"), false, ERROR); 	
	
		}
	
		//Toolbox::logInFile("procedimientos", " parseAnswerValues: " . print_r($item, TRUE) . "\r\n".$sql."\r\n"); 
	
	}
	
	// Captura del evento eliminar el relación entre pedido de catálogo y procedimiento.
	function plugin_procedimientos_delete_RelationForm($item) {
		global $DB;
		$sql = "UPDATE `glpi_plugin_formcreator_targettickets` SET `plugin_procedimientos_procedimientos_id` = '0' WHERE (`id` = ". $item->fields["plugin_formcreator_targettickets_id"].")";
		$DB->query($sql);
	
		$target = new PluginFormcreatorTargetTicket();
		$target->getFromDB($item->fields['plugin_formcreator_targettickets_id']);  
	
		$form = new PluginFormcreatorForm();
		$form->getFromDB($item->fields['plugin_formcreator_forms_id']); 	
	
		$params = [
			"header" 	=> sprintf(__("<H3>Detalles de la relación eliminada:</H3>","procedimiento")),
			"message" => sprintf(__("<strong>Pedido de catálogo:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/form.form.php?id=".$item->fields['plugin_formcreator_forms_id']."'> %s </a></font><br><br><strong>Destino:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/targetticket.form.php?id=".$item->fields['plugin_formcreator_targettickets_id']."'> %s </a></font><br>","procedimiento"),$form->fields['name'] ,$target->fields['name']),
			"footer" 	=> sprintf(__("","procedimiento"),$target->fields['name'])
			];	
	 
		Session::addMessageAfterRedirect(PluginProcedimientosProcedimiento_Form::plugin_procedimientos_get_message($params, "d" , $color = ''), false, INFO); 		
	
		//Toolbox::logInFile("procedimientos", " delete: " . print_r($item, TRUE) . "\r\n".$sql."\r\n"); 
	
	}
	
		 // *******************************************************************************************
		 //  [INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
		 // ******************************************************************************************* 
	

?>
