<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginProcedimientosProcedimiento_TicketRecurrent extends CommonDBRelation {

   // From CommonDBRelation
   static public $itemtype_1 = 'PluginProcedimientosProcedimiento';
   static public $items_id_1 = 'plugin_procedimientos_procedimientos_id';
    
   static public $itemtype_2 = 'TicketRecurrent';
   static public $items_id_2 = 'ticketrecurrents_id';
   
   static $rightname = "plugin_procedimientos";
   
   static function cleanForGroup(CommonDBTM $ticketrecurrent) {
      $temp = new self();
      $temp->deleteByCriteria(
         array('ticketrecurrents_id' => $ticketrecurrent->getField('id'))
      );
   }
   
   static function cleanForItem(CommonDBTM $item) {
      $temp = new self();
	  if ($item->getType()== 'TicketRecurrent'){
		 $temp->deleteByCriteria(
				array('ticketrecurrents_id' => $item->getField('id')));
	  } else if ($item->getType()== 'PluginProcedimientosProcedimiento') {
		  $temp->deleteByCriteria(
				array('plugin_procedimientos_procedimientos_id' => $item->getField('id')));
	  }
   }
   
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
		if ($item->getType()=='PluginProcedimientosProcedimiento') {
			if ($_SESSION['glpishow_count_on_tabs']) {
                  return self::createTabEntry(_n('Tickets Recurrentes', 'Tickets Recurrentes', Session::getPluralNumber()), self::countForProcedimiento($item));
            }			
            return _n('Tickets Recurrentes','Tickets Recurrentes',2);
		}
	}


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {  
      if ($item->getType()=='PluginProcedimientosProcedimiento') {        
        self::showForProcedimiento($item);
      } 
      return true;
   }
   
   static function countForProcedimiento(PluginProcedimientosProcedimiento $item) {
	  
      return countElementsInTable('glpi_plugin_procedimientos_procedimientos_ticketrecurrents',
                                            ['plugin_procedimientos_procedimientos_id' => $item->getField('id')]);		  
	  
   }

   function addItem($values) {

      $this->add(array('plugin_procedimientos_procedimientos_id' =>$values["plugin_procedimientos_procedimientos_id"],
                        'ticketrecurrents_id'=>$values["ticketrecurrents_id"]));
    
   }
   
	/**
	* Muestra los elementos de un procedimientos.
    **/
	
   static function showForProcedimiento(PluginProcedimientosProcedimiento $procedimiento) {
      global $DB, $CFG_GLPI;
	  
      $instID = $procedimiento->fields['id'];
	
      if (!$procedimiento->can($instID, READ)) {
         return false;
      }
      $canedit = $procedimiento->can($instID, UPDATE);

      $rand   = mt_rand();
         echo "<form name='procedimientoticketrecurrent_form$rand' id='procedimientoticketrecurrent_form$rand' method='post'
               action='".Toolbox::getItemTypeFormURL("PluginProcedimientosProcedimiento_TicketRecurrent")."'>";
      if ($canedit) {
         echo "<div class='firstbloc'>";


         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_2'><th colspan='2'>A&ntilde;adir tickets recurrentes a los que mostrar procedimientos</th></tr>";

         echo "<tr class='tab_bg_1'><td class='center'>";
         
		 TicketRecurrent::dropdown(array('name'      => 'ticketrecurrents_id',
                               'entity'    => $procedimiento->fields["entities_id"]
									// ,'condition' => '`is_active`'
							   ));	// Solo si están activos?
         echo "</td><td class='center'>";
         echo "<input type='submit' name='addticketrecurrent' value=\""._sx('button', 'Add')."\" class='submit'>";
         echo "<input type='hidden' name='plugin_procedimientos_procedimientos_id' value='$instID'>";
         echo "</td></tr>";
         echo "</table>";
         echo "</div>";
      }

      echo "<div class='spaced'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";

      if ($canedit) {
         echo "<th width='10'>&nbsp;</th>";
      }
      echo "<th>Elementos</th>";
      echo "</tr>";

      $column = "name";
      $query     = "SELECT `glpi_ticketrecurrents`.*,
                           `glpi_plugin_procedimientos_procedimientos_ticketrecurrents`.`id` AS IDD, ";


      $query .= "`glpi_entities`.`id` AS entity
                  FROM `glpi_plugin_procedimientos_procedimientos_ticketrecurrents`, `glpi_ticketrecurrents`, `glpi_entities` ";
      $query .= "WHERE plugin_procedimientos_procedimientos_id=".$instID." and `glpi_ticketrecurrents`.`id` = `glpi_plugin_procedimientos_procedimientos_ticketrecurrents`.`ticketrecurrents_id`
				 GROUP BY `glpi_ticketrecurrents`.id ORDER BY `glpi_ticketrecurrents`.name";
      if ($result_linked = $DB->query($query)) {
               if ($DB->numrows($result_linked)) {
                  while ($data = $DB->fetchassoc($result_linked)) {
                     $linkname = $data["name"];
                     if ($_SESSION["glpiis_ids_visible"]
                         || empty($data["name"])) {
                        $linkname = sprintf(__('%1$s (%2$s)'), $linkname, $data["id"]);
                     }

                     $link = '../../../front/ticketrecurrent.form.php';
                     $name = "<a href=\"".$link."?id=".$data["id"]."\">".$linkname."</a>";

                     echo "<tr class='tab_bg_1'>";

                     if ($canedit) {
                        echo "<td width='10' style='padding-top: 0'>";
                        echo "<button type='submit'  value='".$data["id"]."' name='elimina' style='border:0; background-color: Transparent;' 
						onclick=\"return confirm('¿Seguro que deseas quitarle a este ticket recurrente este procedimiento?');\">
						<img src='".$_SESSION["glpiroot"]."/plugins/procedimientos/imagenes/error.png' /></button>";
                        echo "</td>";
                     }
                     echo "<td ".
                           (isset($data['is_deleted']) && $data['is_deleted']?"class='tab_bg_2_2'":"").
                          ">".$name."</td>";
                     echo "</tr>";
                  }
               }
      }
      echo "</table>";
      if ($canedit) {
         $paramsma['ontop'] =false;
         
      }
	  Html::closeForm();
      echo "</div>";
	  //echo "</form>"; INFORGES - 24/11/2017

   }
   

   /**
    * @since version 0.84
   **/
   function getForbiddenStandardMassiveAccion() {

      $forbidden   = parent::getForbiddenStandardMassiveAccion();
      $forbidden[] = 'update';
      return $forbidden;
   }
   
       /**
    * Export in an array all the data of the current instanciated ITEMS TICKETS
    * @param boolean $remove_uuid remove the uuid key
    *
    * @return array the array with all data (with sub tables)
    */
   public function export($remove_uuid = false) {
      if (!$this->getID()) {
         return false;
      }

      $procedimiento_item = $this->fields;
	  
	  	$glpi_recurrentes_obj  = new TicketRecurrent;	

   	  $glpi_recurrentes_obj->getFromDB($procedimiento_item['ticketrecurrents_id']);
	  
	  $procedimiento_item['name']=$glpi_recurrentes_obj->fields["name"];	  
      
     /* if (empty($procedimiento_item['uuid'])) {
                 
         $procedimiento_item['uuid']=plugin_procedimientos_getUuid();

      }*/
      
	        // remove key and fk
      unset($procedimiento_item['id'],
            $procedimiento_item['plugin_procedimientos_procedimientos_id']);

      return $procedimiento_item;
   }
   

}
?>