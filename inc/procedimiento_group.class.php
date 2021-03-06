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

class PluginProcedimientosProcedimiento_Group extends CommonDBRelation {

   // From CommonDBRelation
   static public $itemtype_1 = 'PluginProcedimientosProcedimiento';
   static public $items_id_1 = 'plugin_procedimientos_procedimientos_id';
    
   static public $itemtype_2 = 'Group';
   static public $items_id_2 = 'groups_id';
   
   static $rightname = "plugin_procedimientos";
   
   static function cleanForGroup(CommonDBTM $group) {
      $temp = new self();
      $temp->deleteByCriteria(
         array('groups_id' => $group->getField('id'))
      );
   }
   
   static function cleanForItem(CommonDBTM $item) {
      $temp = new self();
	  if ($item->getType()== 'Group'){
		 $temp->deleteByCriteria(
				array('groups_id' => $item->getField('id')));
	  } else if ($item->getType()== 'PluginProcedimientosProcedimiento') {
		  $temp->deleteByCriteria(
				array('plugin_procedimientos_procedimientos_id' => $item->getField('id')));
	  }
   }
   
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
		if ($item->getType()=='PluginProcedimientosProcedimiento') {
               if ($_SESSION['glpishow_count_on_tabs']) {
                  return self::createTabEntry(_n('Grupos', 'Grupos', Session::getPluralNumber()), self::countForProcedimiento($item));
               }
               return _n('Grupos', 'Grupos', Session::getPluralNumber());
		}
	}


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {  
      if ($item->getType()=='PluginProcedimientosProcedimiento') {        
        self::showForProcedimiento($item);
      } 
      return true;
   }
   
   static function countForProcedimiento(PluginProcedimientosProcedimiento $item) {
	  
      return countElementsInTable('glpi_plugin_procedimientos_procedimientos_groups',
                                            ['plugin_procedimientos_procedimientos_id' => $item->getField('id')]);	  
	  
   }

   function addItem($values) {

      $this->add(array('plugin_procedimientos_procedimientos_id' =>$values["plugin_procedimientos_procedimientos_id"],
                        'groups_id'=>$values["groups_id"]));
    
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
         echo "<form name='procedimientogroup_form$rand' id='procedimientogroup_form$rand' method='post'
               action='".Toolbox::getItemTypeFormURL("PluginProcedimientosProcedimiento_Group")."'>";
      if ($canedit) {
         echo "<div class='firstbloc'>";


         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_2'><th colspan='2'>A&ntilde;adir grupos a los que mostrar procedimientos</th></tr>";

         echo "<tr class='tab_bg_1'><td class='center'>";
         
		 Group::dropdown(array('name'      => 'groups_id',
                               'entity'    => $procedimiento->fields["entities_id"],
							    'condition' => ['is_assign' => 1]));		
         echo "</td><td class='center'>";
         echo "<input type='submit' name='addgroup' value=\""._sx('button', 'Add')."\" class='submit'>";
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
      $query     = "SELECT `glpi_groups`.*,
                           `glpi_plugin_procedimientos_procedimientos_groups`.`id` AS IDD, ";


      $query .= "`glpi_entities`.`id` AS entity
                  FROM `glpi_plugin_procedimientos_procedimientos_groups`, `glpi_groups`, `glpi_entities` ";
      $query .= "WHERE plugin_procedimientos_procedimientos_id=".$instID." and `glpi_groups`.`id` = `glpi_plugin_procedimientos_procedimientos_groups`.`groups_id`
				 GROUP BY `glpi_groups`.id ORDER BY `glpi_groups`.name";
      if ($result_linked = $DB->query($query)) {
               if ($DB->numrows($result_linked)) {
                  while ($data = $DB->fetchassoc($result_linked)) {
                     $linkname = $data["name"];
                     if ($_SESSION["glpiis_ids_visible"]
                         || empty($data["name"])) {
                        $linkname = sprintf(__('%1$s (%2$s)'), $linkname, $data["id"]);
                     }

                     $link = '../../../front/group.form.php';
                     $name = "<a href=\"".$link."?id=".$data["id"]."\">".$linkname."</a>";

                     echo "<tr class='tab_bg_1'>";

                     if ($canedit) {
                        echo "<td width='10' style='padding-top: 0'>";
                        echo "<button type='submit'  value='".$data["id"]."' name='elimina' style='border:0; background-color: Transparent;' 
						onclick=\"return confirm('¿Seguro que deseas quitarle a este grupo este procedimientos?');\">
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
    * Export in an array all the data of the current instanciated FORMS
    * @param boolean $remove_uuid remove the uuid key
    *
    * @return array the array with all data (with sub tables)
    */
   public function export($remove_uuid = false) {
      if (!$this->getID()) {
         return false;
      }
	  $glpi_Grupo_obj  = new Group;
      $procedimiento_groups = $this->fields;
	  
      $groups_id=$procedimiento_groups['groups_id'];
	  
	  $glpi_Grupo_obj->getFromDB($groups_id);
	  
	  $procedimiento_groups['name']=$glpi_Grupo_obj->fields["name"];
	  
      // remove key and fk
      unset($procedimiento_groups['id'],
            $procedimiento_groups['plugin_procedimientos_procedimientos_id'],
			$procedimiento_groups['uuid']);
      

    return $procedimiento_groups;
   
}   
   
   
}
?>