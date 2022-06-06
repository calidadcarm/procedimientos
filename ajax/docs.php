<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

//include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");

Session::checkRight("plugin_procedimientos", READ);
if (isset($_POST['procedimientos_id']) && !empty($_POST['procedimientos_id'])) {

		    echo "<div class='center'>";
				echo "<table class='tab_cadre_fixehov'>";
	
	
		   $query = "SELECT DISTINCT `itemtype`
					FROM `glpi_documents_items`
					WHERE `items_id` = '".$_POST['procedimientos_id']."' AND `itemtype` = 'PluginProcedimientosProcedimiento'
					ORDER BY `itemtype`";
		  $result = $DB->query($query);
		  $number = $DB->numrows($result);
		  $i = 0;
		  if (Session::isMultiEntitiesMode()) {
			 $colsup = 1;
		  } else {
			 $colsup = 0;
		  }
	  
	  $proc = new PluginProcedimientosProcedimiento();
	  $proc->getFromDB($_POST['procedimientos_id']);  
      
	  if ($number > 0) {
		 echo "<tr><th colspan=5 class='center'>Documentaci&oacute;n del procedimiento '".$proc->fields['name']."'</th>";
         echo "<tr><th>".__('Heading')."</th>";
         echo "<th>".__('Name')."</th>";
         echo "<th>".__('Web link')."</th>";
         echo "<th>".__('File')."</th>";
         echo "<th>".__('Entity')."</th>";
         echo "</tr>";
      }		  
		  
		  
			 for ($i=0 ; $i < $number ; $i++) {
			 $type = $DB->result($result, $i, "itemtype");
			 if (!class_exists($type)) {
				continue;
			 }
			 $item = new $type();
			  $column = "name";
			  $query1 = "SELECT glpi_documents.*, glpi_documents_items.id AS IDD, glpi_entities.id AS entity
				FROM glpi_documents_items, glpi_documents LEFT JOIN glpi_entities ON (glpi_entities.id = glpi_documents.entities_id)
				WHERE glpi_documents.id = glpi_documents_items.documents_id
					AND glpi_documents_items.itemtype = 'PluginProcedimientosProcedimiento'
					AND glpi_documents_items.items_id = ".$_POST['procedimientos_id']."
					AND glpi_documents.is_deleted = 0
				ORDER BY glpi_entities.completename, glpi_documents.name";		
			
				if ($result_linked1 = $DB->query($query1)) {
				   if ($DB->numrows($result_linked1)) {

					 $document = new Document();
					 while ($data = $DB->fetch_assoc($result_linked1)) {
						 $item->getFromDB($data["id"]);
						 Session::addToNavigateListItems($type,$data["id"]);
						 $ID = "";
						 $downloadlink = NOT_AVAILABLE;
							if ($document->getFromDB($data["id"])) {
							   $downloadlink = $document->getDownloadLink();
							}						 
						 

						 if($_SESSION["glpiis_ids_visible"] || empty($data["name"])) {
							$ID = " (".$data["id"].")";
						 }

						 echo "<tr class='tab_bg_1'>";
							echo "<td class='center'>".Dropdown::getDropdownName("glpi_documentcategories",
																				 $data["documentcategories_id"]);
							echo "</td>";						  
						  

						 $nombre = $data['name'];
						 echo "<td class='center' ".
							   (isset($data['deleted']) && $data['deleted']?"class='tab_bg_2_2'":"").">".
							   $nombre."</td>";
						echo "<td class='center'>";
						if (!empty($data["link"])) {
						   echo "<a target=_blank href='".Toolbox::formatOutputWebLink($data["link"])."'>".$data["link"];
						   echo "</a>";
						} else {;
						   echo "&nbsp;";
						}
						echo "</td>";								   
						 echo "<td class='center'>$downloadlink</td>";						
						if (Session::isMultiEntitiesMode()) {
							echo "<td class='center'>".Dropdown::getDropdownName("glpi_entities",
																				 $data['entity']).
								  "</td>";
						 }
						 echo "</tr>";
					  }

				   }
				}
			 }
		 
		echo "</table>";
		echo "</div>";
}
?>