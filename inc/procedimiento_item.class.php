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
 
class PluginProcedimientosProcedimiento_Item extends CommonDBRelation{

   // From CommonDBRelation
   static public $itemtype_1          = 'PluginProcedimientosProcedimiento';
   static public $items_id_1          = 'plugin_procedimientos_procedimientos_id';

   static public $itemtype_2          = 'itemtype';
   static public $items_id_2          = 'items_id';

   static public $types = array('PluginProcedimientosMarcador', 'PluginProcedimientosAccion', 'PluginProcedimientosProcedimiento', 'PluginProcedimientosCondicion',
								'PluginProcedimientosSalto', 'PluginProcedimientosLink');
 

   /**
    * @since version 0.84
    *
   **/
   function getForbiddenStandardMassiveAccion() {

      $forbidden   = parent::getForbiddenStandardMassiveAccion();
      $forbidden[] = 'update';
      return $forbidden;
   }


   /**
    * Count connection for an item
    *
    * @param $item   CommonDBTM object
    *
    * @return integer: count
   **/
   static function countForItem(CommonDBTM $item) {
								   
      return countElementsInTable('glpi_plugin_procedimientos_procedimientos_items',
                                            ['itemtype' => $item->getType(),
											 'items_id' => $item->getField('id')]);									   
								   
   }
   
   static function countForProcedimiento(PluginProcedimientosProcedimiento $procedimiento) {
								  
      return countElementsInTable('glpi_plugin_procedimientos_procedimientos_items',
                                            ['plugin_procedimientos_procedimientos_id' => $procedimiento->getField('id')]);									  
								  
   }


   static function countForAll(PluginProcedimientosProcedimiento $procedimiento, CommonDBTM $item) {

      return countElementsInTable('glpi_plugin_procedimientos_procedimientos_items',
                                            ['plugin_procedimientos_procedimientos_id' => $procedimiento->getField('id'),
											'itemtype' => $item->getType(),
											'items_id' => $item->getField('id')]);	

   }


   /**
    * Accions done when item is deleted from the database
    * Overloaded to manage autoupdate feature
    *
    *@return nothing
   **/
   function cleanDBonPurge() {
	  global $DB;
	
	  if ($this->fields['itemtype'] == 'PluginProcedimientosCondicion'){
			// Borro la tabla detalles condicions si itemtype='PluginProcedimientosCondicion'	
			$temp = new PluginProcedimientosCondicion();
			$temp->deleteByCriteria(array('plugin_procedimientos_procedimientos_id' => $this->fields['plugin_procedimientos_procedimientos_id'],
			'id' => $this->fields['items_id']));
	  }
	  if ($this->fields['itemtype'] == 'PluginProcedimientosSalto'){
			// Borro la tabla detalles saltos si itemtype='PluginProcedimientosSalto'	
			$temp = new PluginProcedimientosSalto();
			$temp->deleteByCriteria(array('plugin_procedimientos_procedimientos_id' => $this->fields['plugin_procedimientos_procedimientos_id'],
			'id' => $this->fields['items_id']));
	  }
	  
	  $update_linea = "update `glpi_plugin_procedimientos_procedimientos_items` set `line`='99999' where `id`='".$this->fields['id']."';";
	  $result_update = $DB->query($update_linea);

	 renumerar_procedimiento($this->fields['plugin_procedimientos_procedimientos_id']);

	  return true;
   }


   /**
    * @since version 0.85
    *
    * @see CommonDBTM::getMassiveAccionsForItemtype()
   **/
   static function getMassiveAccionsForItemtype(array &$accions, $itemtype, $is_deleted=0,
                                                CommonDBTM $checkitem=NULL) {

      $accion_prefix = __CLASS__.MassiveAccion::CLASS_ACTION_SEPARATOR;
      $specificities = self::getRelationMassiveAccionsSpecificities();

      if (in_array($itemtype, $specificities['itemtypes'])) {
         $accions[$accion_prefix.'add']    = _x('button', 'Connect');
         $accions[$accion_prefix.'remove'] = _x('button', 'Disconnect');
      }
      parent::getMassiveAccionsForItemtype($accions, $itemtype, $is_deleted, $checkitem);
   }


   /**
   * Disconnect an item to its procedimientos
   *
   * @param $item    CommonDBTM object: the Market, Condition, Accion, Procedimientos
   *
   * @return boolean : accion succeeded
   */
   function disconnectForItem(CommonDBTM $item) {
      global $DB;

      if ($item->getField('id')) {
         $query = "SELECT `id`
                   FROM `glpi_plugin_procedimientos_procedimientos_items`
                   WHERE `itemtype` = '".$item->getType()."'
                         AND `items_id` = '".$item->getField('id')."'";
         $result = $DB->query($query);

         if ($DB->numrows($result) > 0) {
            $ok = true;
            while ($data = $DB->fetchassoc($result)) {
               if ($this->can($data["id"],UPDATE)) {
                  $ok &= $this->delete($data);
               }
            }
            return $ok;
         }
      }
      return false;
   }

   /**
    * @see CommonGLPI::getTabNameForItem()
   **/
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      global $CFG_GLPI;

      // Can exists on template
      if (PluginProcedimientosProcedimiento::canView()) {
         switch ($item->getType()) {
            case 'PluginProcedimientosProcedimiento' :
               if ($_SESSION['glpishow_count_on_tabs']) {
                  return self::createTabEntry(_n('Elementos', 'Elementos', Session::getPluralNumber()), self::countForProcedimiento($item));
               }
               return _n('Elementos', 'Elementos', Session::getPluralNumber());

         }
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      switch ($item->getType()) {
         case 'PluginProcedimientosProcedimiento' :
            self::showForProcedimiento($item);
      }
      return true;
   }
   

  // Pestaña "Elementos" en un procedimiento	
  static function showForProcedimiento(PluginProcedimientosProcedimiento $procedimiento) {
      global $DB, $CFG_GLPI;

      $instID = $procedimiento->fields['id'];
	  $entities_id = $procedimiento->fields['entities_id'];

      if (!$procedimiento->can($instID, READ)) {
         return false;
      }
      $canedit = $procedimiento->can($instID, UPDATE);
      $rand    = mt_rand();

	  $items = getItemsForProcedimiento($instID, $entities_id);
	  
      if ($canedit) {
         echo "<div class='firstbloc'>";
         echo "<form name='procedimientoitems_form$rand' id='procedimientoitems_form$rand' ";
         echo " method='post' action='".Toolbox::getItemTypeFormURL('PluginProcedimientosProcedimiento')."'>";
         echo "<input type='hidden' name='plugin_procedimientos_procedimientos_id' value='$instID'>";
         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_1'><th colspan='4'>".__('Añadir un elemento')."</tr>";
         echo "<tr class='tab_bg_2'><td width='100px'>";

         $types = array('PluginProcedimientosMarcador', 'PluginProcedimientosAccion', 'PluginProcedimientosCondicion', 'PluginProcedimientosProcedimiento', 
						'PluginProcedimientosSalto','PluginProcedimientosLink');

         $addrand = Dropdown::showItemTypes('_type', $types, array('width' => '100%'));
         $params  = array('type'  => '__VALUE__',
                          'right' => 'plugin_procedimientos',
						  'procedimientos_id' => $instID);

         Ajax::updateItemOnSelectEvent("dropdown__type".$addrand,"items$rand",
                                       $CFG_GLPI["root_doc"]."/plugins/procedimientos/ajax/items.php", $params);
									   
		 echo "<script type='text/javascript'>
				 $('#dropdown__type".$addrand."').change(function(){ $('#alta_item').prop( 'disabled', true ); });  
		       </script>";										   

         echo "</td>";
         echo "<td><span id='items$rand'></span>";
         echo "</td></tr>";
         echo "</table>";
         Html::closeForm();
         echo "</div>";
      }	  
      echo "<div class='spaced'>";
      if ($canedit) {
         Html::openMassiveActionsForm('mass'.__CLASS__.$rand);
         $massiveactionparams = array('container' => 'mass'.__CLASS__.$rand);
         Html::showMassiveActions($massiveactionparams);
      }
	  
         echo '<div id="drag">';
         echo '<input type="hidden" name="_plugin_fields_containers_id"
                  id="plugin_fields_containers_id" value="' . $instID . '" />';	  
	  
      echo "<table class='tab_cadre_fixehov'>";
	  $header_begin  = "<tr>";
      $header_top    = '';
      $header_bottom = '';
      $header_end    = '';

      if ($canedit) {
         $header_top    .= "<th width='10'>".Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
         $header_top    .= "</th>";
         $header_bottom .= "<th width='10'>".Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
         $header_bottom .= "</th>";
      }
	/*  if (Session::haveRight('plugin_procedimientos', UPDATE)) {
		$header_end .= "<th>".__('Orden')."</th>";	
	  }*/
      $header_end .= "<th>".__('#Linea')."</th>";	  	  
      $header_end .= "<th>".__('Elemento')."</th>";
	  $header_end .= "<th>".__('Type')."</th>";
      $header_end .= "<th>".__('Name')."</th>";
      $header_end .= "<th>".__('Descripci&oacute;n')."</th>";
	  if (Session::haveRight('plugin_procedimientos', UPDATE)) {
	 $header_end .= "<th>".__('Mover')."</th>";
	  }
      $header_end .= "</tr>";
      echo $header_begin.$header_top.$header_end;

      $totalnb = 0;
      foreach ($items as $linea => $item) {
		foreach ($item as $itemtype => $datas) {
			
            $name = $datas["name"];
            if ($_SESSION["glpiis_ids_visible"]
                   || empty($datas["name"])) {
                  $name = sprintf(__('%1$s (%2$s)'), $name, $datas["id"]);
            }
			if ($itemtype != 'PluginProcedimientosMarcador'){ // Los marcadores no se editan.
				$link = Toolbox::getItemTypeFormURL($itemtype);	// Enlace al formulario de la clase		
				$name = "<a href=\"".$link."?id=".$datas["id"]."\">".$name."</a>"; //Incluimos enlace en el nombre
			}			
 			echo "<tr class='tab_bg_1'>";
            if ($canedit) {
                  echo "<td width='10'>";
                  Html::showMassiveActionCheckBox(__CLASS__, $datas["IDD"]);
                  echo "</td>";
            }
			// flechas para ordenar elementos
			/*if (Session::haveRight('plugin_procedimientos', UPDATE)) {
				echo "<td width='40px' style='width: 40px; text-align: center;'><div style='width: 40px; display: inline-block;'>";
				echo "<a href='procedimiento.form.php?boton=abajo&procedimientos_id=".$datas["procedimientos_id"]."&line=".$datas["linea"]."&id=".$datas["IDD"]."'>
				<img src='".$_SESSION["glpiroot"]."/plugins/procedimientos/imagenes/abajo.png'></a>&nbsp;
				<a href='procedimiento.form.php?boton=arriba&procedimientos_id=".$datas["procedimientos_id"]."&line=".$datas["linea"]."&id=".$datas["IDD"]."'>
				<img src='".$_SESSION["glpiroot"]."/plugins/procedimientos/imagenes/arriba.png'></a></div>";
				echo "</td>";			
 			}*/
			echo "<td class='linea' id='".$datas["IDD"]."' class='center'>".
                      (isset($datas["linea"])? "".$datas["linea"]."" :"-")."</td>";	
			
			$elemento = nameItemtype ($itemtype);					  
			echo "<td id='elemento_".$datas["IDD"]."' class='center'>".
                      (isset($elemento)? "".$elemento."" :"-")."</td>";	
			
			echo "<td class='center'>"; // Tipo de accion
            if (isset($datas["plugin_procedimientos_tipoaccions_id"])) {
                  echo Dropdown::getDropdownName("glpi_plugin_procedimientos_tipoaccions", $datas['plugin_procedimientos_tipoaccions_id']);
            } else {
                  echo '-';
            }
            echo "</td>";					  
            echo "<td class='center".
                      (isset($datas['is_deleted']) && $datas['is_deleted'] ? " tab_bg_2_2'" : "'");
            echo ">".$name."</td>";

            
            echo"<td id='commentario_".$datas["IDD"]."' class='center'>".
                      (isset($datas["comment"])? "". html_entity_decode($datas["comment"]) ."" :"-")."</td>";
				if (Session::haveRight('plugin_procedimientos', UPDATE)) {	  
           echo '<td class="rowhandler control center">';
               echo '<div class="drag row" style="cursor:move;border:none !important;">';
               echo '<img src="../pics/drag.png" alt="#" title="' . __('Move') .'" width="16" height="16" />';
               echo '</div>';
               echo '</td>';	
				}
			echo "</tr>";

        }
      }
      echo "</table>";
	  
	  	  echo '</div>';
	  
	        echo Html::scriptBlock('redipsProcedures()');
	  
      if ($canedit) {
         $massiveactionparams['ontop'] = false;
         Html::showMassiveActions($massiveactionparams);
         Html::closeForm();
      }
      echo "<div class='msg2' style='display:none;'></div></div>";
	 }
	
	// Intercambiamos número de linea a dos elementos de procedimiento.
	static function intercambia($id1,$linea1,$id2,$linea2) {
		global $DB;
		
		$query_clear = "UPDATE `glpi_plugin_procedimientos_procedimientos_items` SET `line`= 99999 WHERE `id`=".$id2.";";
		$result_clear = $DB->query($query_clear);
		
		$query_change = "UPDATE `glpi_plugin_procedimientos_procedimientos_items` SET `line`=".$linea2." WHERE `id` =".$id1.";";
		$result_change = $DB->query($query_change);
		
		$query_change2= "UPDATE `glpi_plugin_procedimientos_procedimientos_items` SET `line`=".$linea1." WHERE `id` =".$id2.";";
		$result_change2 = $DB->query($query_change2);
		return;
	}  
        
        
    /**
    * Export in an array all the data of the current instanciated ITEM
    * @param boolean $remove_uuid remove the uuid key
    *
    * @return array the array with all data (with sub tables)
    */
   public function export($remove_uuid = false) {
      if (!$this->getID()) {
         return false;
      }

      $procedimiento_item = $this->fields;
      
      if (!empty($procedimiento_item['itemtype'])){
          
      $subitem="_".strtolower (explode("Procedimientos",$procedimiento_item['itemtype'])[1])."s";
      $item[$subitem] = [];
     $items_id=$procedimiento_item['items_id'];
     $itemtype=$procedimiento_item['itemtype'];
      // remove key and fk
      unset($procedimiento_item['id'],
            $procedimiento_item['plugin_procedimientos_procedimientos_id'],
            $procedimiento_item['items_id']/*,
            $procedimiento_item['itemtype']*/);

      if (is_subclass_of($itemtype, 'CommonDBTM')) {
         $procedimiento_item_obj = new $itemtype;
         if ($procedimiento_item_obj->getFromDB($items_id)) {
          $resultado=$procedimiento_item_obj->fields;
       
       
          unset($resultado['id'],
             $resultado['plugin_procedimientos_procedimientos_id'],
             $resultado['items_id']);
        
          $item[$subitem]=$resultado;
        
       if ($itemtype=="PluginProcedimientosAccion") {
              $item[$subitem]=$procedimiento_item_obj->export($remove_uuid, $item[$subitem]);
       }
       
       }
      
	  }

        $procedimiento_item[$subitem] =  $item[$subitem]; 
      
        
       }
      
      if (empty($procedimiento_item['uuid'])) {
                 
         $procedimiento_item['uuid']=plugin_procedimientos_getUuid();

      }
      

      return $procedimiento_item;
   }
   

      /**
    * Purge items into the db
    * @see PluginProcedimientosProcedimiento_Form::importJson
    *
    * @param  integer $items_id  id of the parent form
    * @param  array   $section the section data (match the sub_items tables)
    * @return integer the section's id
    */
   
  public static function purgar_no_encontrados($ID = 0, $items_importados = array()) {
   $item = new PluginProcedimientosProcedimiento_Item;

$params = [
"plugin_procedimientos_procedimientos_id" => $ID,
];	
   
   $items_originales = $item->find($params);				   
				
				              foreach ($items_originales as $items) {

			foreach ($items_importados as $id=>$value){
			$encontrado=FALSE;
			if ($items["uuid"] == $items_importados[$id]) {				
				$encontrado=TRUE;
				break;
			}								  
								 
      } 
				  			
			if ($encontrado==TRUE){
				//echo "<font color='green'>ENCONTRADO: ".$items["uuid"]."</font><BR><BR>";	
				} else {
				//echo "<font color='red'> NO ENCONTRADO: ".$items["uuid"]."</font><BR><BR>";	
				
				$item->deleteByCriteria(array('uuid' => $items["uuid"],'id' => $items["id"]));
				}	
							
            }
  }  
   
      /**
    * Import items into the db
    * @see PluginProcedimientosProcedimiento_Form::importJson
    *
    * @param  integer $items_id  id of the parent form
    * @param  array   $section the section data (match the sub_items tables)
    * @return integer the section's id
    */
   public static function import($items_id = 0, $section = array()) {
      $item = new PluginProcedimientosProcedimiento_Item;

      $section['plugin_procedimientos_procedimientos_id'] = $items_id;
   //   $section['_skip_checks']                = true;


      if (is_subclass_of($section['itemtype'], 'CommonDBTM')) {
         $procedimiento_item_obj = new $section['itemtype'];
         $subitem="_".strtolower (explode("Procedimientos",$section['itemtype'])[1])."s";
        
         if (($section['itemtype']!='PluginProcedimientosCondicion') and 
             ($section['itemtype']!='PluginProcedimientosSalto')) {    
                 
     /*   echo $subitem."   --    ".var_dump($section[$subitem])."<br>";
           echo $subitem."   <br>";*/
		   
            if ($sections_id = plugin_porcedimientos_getFromDBByField($procedimiento_item_obj, 'uuid', $section[$subitem]['uuid'])) {
         
        // add id key
                   
         $section[$subitem]['id'] = $sections_id;
		 
         // update section
       $procedimiento_item_obj->update($section[$subitem]);      
		
	   $section['items_id']=$sections_id;
		
       if ($section['itemtype']=='PluginProcedimientosAccion') {
            
            $procedimiento_item_obj->update_detalle($section);
        
		} 		 
		 
      } else {
         //create section
          $section['items_id'] = $procedimiento_item_obj->add($section[$subitem]);  
							
       if ($section['itemtype']=='PluginProcedimientosAccion') {
            
            $procedimiento_item_obj->import($section);
        	
		}   
			   
      }
   
  
  
      
      } else {

  $section[$subitem]['plugin_procedimientos_procedimientos_id']=$items_id;	  
	
		  if ($sections_id = plugin_porcedimientos_getFromDBByField($procedimiento_item_obj, 'uuid', $section[$subitem]['uuid'])) {
			  
			   $section[$subitem]['id'] = $sections_id;

		  $section['items_id'] = $procedimiento_item_obj->update($section[$subitem]);  
		  
		  } else {
			  
      //unset($section[$subitem]['uuid']);  
	  
        $section['items_id'] = $procedimiento_item_obj->add($section[$subitem]);
		
		  }	  
	   
      }   
      } 

	  //=========================================================
     
	 if ($sections_id = plugin_porcedimientos_getFromDBByField($item, 'uuid', $section['uuid'])) {
         // add id key
         $section['id'] = $sections_id;

		// echo var_dump($section);
		 
         // update section
        $sections_id = $item->update($section);
		
         //  unset($section['uuid']); 
          
      } else {
         //create section
         $sections_id = $item->add($item->prepareInputForAdd($section));
		 

		// var_dump($section);

		// echo "<br><br><br><br>";
		 
         
	  }
	  

	  
	   

   }
   
   
           /**
    * Prepare input datas for updating the form
    *
    * @param $input datas used to add the item
    *
    * @return the modified $input array
   **/
   public function prepareInputForAdd($input) {
      // Decode (if already encoded) and encode strings to avoid problems with quotes
        // generate a uniq id
      if (!isset($input['uuid'])
          || empty($input['uuid'])) {
         $input['uuid'] = plugin_procedimientos_getUuid();
      }

      return $input;
   }
   
        
}
?>