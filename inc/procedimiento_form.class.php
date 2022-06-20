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

class PluginProcedimientosProcedimiento_Form extends CommonDBRelation {

   // From CommonDBRelation
   static public $itemtype_1 = 'PluginProcedimientosProcedimiento';
   static public $items_id_1 = 'plugin_procedimientos_procedimientos_id';
    
   static public $itemtype_2 = 'PluginFormcreatorForm';
   static public $items_id_2 = 'plugin_formcreator_forms_id';
   
   static $rightname = "plugin_procedimientos";
   
   static function cleanForGroup(CommonDBTM $form) {
      $temp = new self();
      $temp->deleteByCriteria(
         array('plugin_formcreator_forms_id' => $form->getField('id'))
      );
   }
   
   static function cleanForItem(CommonDBTM $item) {
      $temp = new self();
	  if ($item->getType()== 'PluginFormcreatorForm'){
		 $temp->deleteByCriteria(
				array('plugin_formcreator_forms_id' => $item->getField('id')));
	  } else if ($item->getType()== 'PluginProcedimientosProcedimiento') {
		  $temp->deleteByCriteria(
				array('plugin_procedimientos_procedimientos_id' => $item->getField('id')));
	  }
   }
   
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
		if ($item->getType()=='PluginProcedimientosProcedimiento') {
			if ($_SESSION['glpishow_count_on_tabs']) {
                  return self::createTabEntry(_n('Pedidos Catalogo', 'Pedidos Catalogo', Session::getPluralNumber()), self::countForProcedimiento($item));
            }
            return _n('Pedidos Catalogo','Pedidos Catalogo',2);
		}
	}


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {  
      if ($item->getType()=='PluginProcedimientosProcedimiento') {        
        self::showForProcedimiento($item);
      } 
      return true;
   }
   
   static function countForProcedimiento(PluginProcedimientosProcedimiento $item) {
      
      return countElementsInTable('glpi_plugin_procedimientos_procedimientos_forms',
                                            ['plugin_procedimientos_procedimientos_id' => $item->getField('id')]);
   }

   // *******************************************************************************************
   //  [INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
   // *******************************************************************************************

/*    function addItem($values) {

      $this->add(array('plugin_procedimientos_procedimientos_id' =>$values["plugin_procedimientos_procedimientos_id"],
                        'plugin_formcreator_forms_id'=>$values["plugin_formcreator_forms_id"]));
    
   }  */
   function addItem($values) {
      global $DB;

      $form = new PluginFormcreatorForm();
      $form->getFromDB($values['plugin_formcreator_forms_id']); 

      $target = new PluginFormcreatorTargetTicket();
      $target->getFromDB($values['plugin_formcreator_targettickets_id']);       

      $params = [
      // "plugin_procedimientos_procedimientos_id" => $values['plugin_procedimientos_procedimientos_id'],
         "plugin_formcreator_forms_id" => $values['plugin_formcreator_forms_id'],
         "plugin_formcreator_targettickets_id" => $values['plugin_formcreator_targettickets_id']
         ];
   
      $relation = $this->find($params);

      if (empty($relation)) {
      
         $sql = "UPDATE `glpi_plugin_formcreator_targettickets` SET `plugin_procedimientos_procedimientos_id` = '" . $values['plugin_procedimientos_procedimientos_id'] . "' WHERE (`id` = '".$values['plugin_formcreator_targettickets_id']."')";
         $DB->query($sql);

         $this->add(array( 'plugin_procedimientos_procedimientos_id' =>$values["plugin_procedimientos_procedimientos_id"],
         'plugin_formcreator_forms_id'             =>$values["plugin_formcreator_forms_id"],
         'plugin_formcreator_targettickets_id'     =>$values["plugin_formcreator_targettickets_id"]));

         $params = [
            "header" 	=> sprintf(__("<H3>Detalles de la relación creada:</H3>","procedimiento")),
            "message"   => sprintf(__("<strong>Pedido de catálogo:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/form.form.php?id=".$values["plugin_formcreator_forms_id"]."'> %s </a></font><br><br><strong>Destino:</strong> <br><br><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/targetticket.form.php?id=".$values["plugin_formcreator_targettickets_id"]."'> %s </a></font><br>","procedimiento"),$form->fields['name'] ,$target->fields['name']),
            "footer" 	=> sprintf(__("","procedimiento"),$target->fields['name'])
            ];	
         
         Session::addMessageAfterRedirect(PluginProcedimientosProcedimiento_Form::plugin_procedimientos_get_message($params, "s" , $color = '#076301'), false, INFO); 	

      } else {

         $item = current($relation);
			
         if ((isset($item['plugin_procedimientos_procedimientos_id'])) and ($item['plugin_procedimientos_procedimientos_id']>0)){

            $procedure  = new PluginProcedimientosProcedimiento();
            $procedure->getFromDB($item['plugin_procedimientos_procedimientos_id']);             
            //Toolbox::logInFile("procedimientos", " ma: " . print_r($relation, TRUE) . "\r\n\r\n");  
            
            $params = [
               "header" 	=> sprintf(__("El procedimiento <font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/procedimientos/front/procedimiento.form.php?id=".$item['plugin_procedimientos_procedimientos_id']."'> %s </a></font> ya está asociado al siguiente:","procedimiento"),$procedure->fields['name']),
               "message"   => sprintf(__("<strong>Pedido de catálogo:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/form.form.php?id=".$item['plugin_formcreator_forms_id']."'> %s </a></font><br><br><strong>Destino:</strong> <br><br><font color = '#7c0068'><a target='_blank' href='".$_SESSION["glpiroot"]."/plugins/formcreator/front/targetticket.form.php?id=".$item['plugin_formcreator_targettickets_id']."'> %s </a></font><br>","procedimiento"),$form->fields['name'] ,$target->fields['name']),
               "footer" 	=> sprintf(__("!La relación solicitada no se ha dado de alta!","procedimiento"))
               ];	
            
            Session::addMessageAfterRedirect($this->plugin_procedimientos_get_message($params, "!"), false, ERROR); 

         }
      }


   }  

/**
    * Get search function for the class
    *
    * @return array of search option
   **/
   
  function rawSearchOptions() {

	$tab = [];

	$tab = array_merge($tab, parent::rawSearchOptions());

	$tab[] = [
      'id'              => '1001',
      'table'           => $this->getTable(),
      'field'           => 'id',
      'name'            => __('ID','ID'),
      'datatype'        => 'text',
      'massiveaction'   => false,
      ]; 

   $tab[] = [
      'id'              => '1002',
      'table'           => 'glpi_plugin_procedimientos_procedimientos',
      'field'           => 'plugin_procedimientos_procedimientos_id',
      'name'            => __('Procedimiento','Procedimientos'),
      'datatype'        => 'dropdown',
      'massiveaction'   => false,
      ];   

	$tab[] = [
      'id'              => '1003',
      'table'           => 'glpi_plugin_formcreator_forms',
      'field'           => 'plugin_formcreator_forms_id',
      'name'            => __('Formulario','Formularios'),
      'datatype'        => 'dropdown',
      'massiveaction'   => false,
      ];
	
	$tab[] = [
      'id'              => '1004',
      'table'           => 'glpi_plugin_formcreator_targettickets',
      'field'           => 'plugin_formcreator_targettickets_id',
      'name'            => __('Destino','Destinos'),
      'datatype'        => 'dropdown',
      'joinparams'      => ['beforejoin' => ['table'        => 'glpi_plugin_formcreator_forms',
                                             'condition'    => 'entities_id = 1',
                                             'joinparams'   => ['jointype'  => 'child'
                                                                ]
                                            ]
                           ],
      'massiveaction'   => false
      ];	

	/*	$sopt[1105]['table']     = 'glpi_taskcategories';
        $sopt[1105]['field']     = 'name';		 
        $sopt[1105]['name']      = 'Tipo Tarea SD';	
		$sopt[1105]['datatype']      = 'dropdown';
		$sopt[1105]['massiveaction'] = false;
		$sopt[1105]['joinparams']    = array('beforejoin' =>
                                           array('table' => 'glpi_plugin_procedimientos_tareas',
                                                 'joinparams'
                                                         => array('jointype'  => 'child',
                                                                  'condition' => '')));   */   
		
	return $tab;

	}  

 

   /**
    * @param $output_type     (default 'Search::HTML_OUTPUT')
    * @param $mass_id         id of the form to check all (default '')
    */
    static function commonListHeader($output_type = Search::HTML_OUTPUT, $mass_id = '') {

      // New Line for Header Items Line
      echo Search::showNewLine($output_type);
      // $show_sort if
      $header_num                      = 1;

      $items                           = [];
      $items[(empty($mass_id) ? '&nbsp' : Html::getCheckAllAsCheckbox($mass_id))] = '';
      $items[__('ID')]            = "id";
      $items[__('Formulario')]    = "plugin_formcreator_forms_id";
      $items[__('Destino')]       = "plugin_formcreator_targettickets_id";

      foreach ($items as $key => $val) {
         $issort = 0;
         $link   = "";
         echo Search::showHeaderItem($output_type, $key, $header_num, $link);
      }

      // End Line for column headers
      echo Search::showEndLine($output_type);
   }

   /**
    * Display a line for an object
    *
    * @since 0.85 (befor in each object with differents parameters)
    *
    * @param $id                 Integer  ID of the object
    * @param $options            array    of options
    *      output_type            : Default output type (see Search class / default Search::HTML_OUTPUT)
    *      row_num                : row num used for display
    *      type_for_massiveaction : itemtype for massive action
    *      id_for_massaction      : default 0 means no massive action
    *      followups              : only for Tickets : show followup columns
    */
    static function showShort($id, $options = []) {
      global $CFG_GLPI, $DB;

      $p['output_type']            = Search::HTML_OUTPUT;
      $p['row_num']                = 0;
      $p['type_for_massiveaction'] = 0;
      $p['id_for_massiveaction']   = 0;

      if (count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      $rand = mt_rand();

      // Prints a job in short form
      // Should be called in a <table>-segment
      // Print links or not in case of user view
      // Make new job object and fill it from database, if success, print it
      $item        = new static();
      $form        = new PluginFormcreatorForm();
      $target      = new PluginFormcreatorTargetTicket();

      $candelete   = static::canDelete();
      $canupdate   = Session::haveRight(static::$rightname, UPDATE);
      $align       = "class='center";
      $align_desc  = "class='left";

      $align      .= "'";
      $align_desc .= "'";

      if ($item->getFromDB($id)) {
         $item_num = 1;
         
         echo Search::showNewLine($p['output_type'], $p['row_num']%2);

         $check_col = '';
         if (($candelete || $canupdate)
             && ($p['output_type'] == Search::HTML_OUTPUT)
             && $p['id_for_massiveaction']) {

            $check_col = Html::getMassiveActionCheckBox($p['type_for_massiveaction'],
                                                        $p['id_for_massiveaction']);
         }
         echo Search::showItem($p['output_type'], $check_col, $item_num, $p['row_num'], $align);

         $id_col = $item->fields["id"];
         echo Search::showItem($p['output_type'], $id_col, $item_num, $p['row_num'], $align);

         // First column
         $form->getFromDB($item->fields["plugin_formcreator_forms_id"]);   
         $first_col = "<span class='b'>".$form->getName();"</span>&nbsp;";

         // Add link
         if ($item->canViewItem()) {

            $first_col = "<a id='".$form->getType().$item->fields["plugin_formcreator_forms_id"]."$rand' href=\"".
                              $form->getLinkURL()."\">$first_col</a>";
         }

         if ($p['output_type'] == Search::HTML_OUTPUT) {
            $first_col = sprintf(__('%1$s %2$s'), $first_col,
                                    Html::showToolTip(Toolbox::unclean_cross_side_scripting_deep(
                                       html_entity_decode($form->fields['content'], ENT_QUOTES, "UTF-8")
                                       ),
                                       ['display' => false,
                                        'applyto' => $form->getType().
                                         $form->fields["id"].
                                         $rand]));
         }

         echo Search::showItem($p['output_type'], $first_col, $item_num, $p['row_num'],
                               $align_desc."width='400' ");							   

         // Second column
         $target->getFromDB($item->fields["plugin_formcreator_targettickets_id"]);   
         $second_col = "<span class='b'>".$target->getName();"</span>&nbsp;";                  

         // Add link
         if ($item->canViewItem()) {

            $second_col = "<a id='".$target->getType().$item->fields["plugin_formcreator_targettickets_id"]."$rand' href=\"".
                              $target->getLinkURL()."\">$second_col</a>";
         }

         if ($p['output_type'] == Search::HTML_OUTPUT) {
            $second_col = sprintf(__('%1$s %2$s'), $second_col,
                                    Html::showToolTip(Toolbox::unclean_cross_side_scripting_deep(
                                       html_entity_decode($target->fields['content'], ENT_QUOTES, "UTF-8")
                                       ),
                                       ['display' => false,
                                        'applyto' => $target->getType().
                                         $item->fields["plugin_formcreator_targettickets_id"].
                                         $rand]));
         }

         echo Search::showItem($p['output_type'], $second_col, $item_num, $p['row_num'],
         $align_desc."width='400' bgcolor='def9e1'");	


         // Finish Line
         echo Search::showEndLine($p['output_type']);
      } else {
         echo "<tr class='tab_bg_2'>";
         echo "<td colspan='4' ><i>".__('No item in progress.')."</i></td></tr>";
      }
   }      

   // Monta el formato de los mensajes
   static function plugin_procedimientos_get_message($content, $icon, $color = "red") {

      $image = $_SESSION["glpiroot"].'/plugins/procedimientos/imagenes/';
      
      switch ($icon) {
      
         case '!' :

            $image .= '/system-attention-icon.png';

         break;

         case 's' :

            $image .= '/save.png';

         break;

         case 'd' :

            $image .= '/erase.png';

         break;         
         
      }
      
      $tabla='<table border="0">
                     <tr>
                     <td align="right"><img style="vertical-align:middle;" alt="" src="'.$image.'"></td>
                     <td class="left">
                     <strong><font color="'.$color.'"> '. $content['header'] .' </font></strong>	
                     </td>
                     </tr>		
                     <tr>				
                     <td colspan="2" class="center">
                     --------------------------------------------------------------------<br>
                     <strong>'.$content['message'].'</strong>
                     --------------------------------------------------------------------<br>
                     </td>
                     </tr>	
                     <tr>				
                     <td colspan="2" class="center">
                     
                     <strong><font color="'.$color.'">' . $content['footer'] . '</font></strong>
                     --------------------------------------------------------------------<br>
                     </td>
                     </tr>			  
                     
                  </table>
                  </strong>';

      return $tabla;

   }


   static function boton ($dropdown_id) {
         
      //Toolbox::logInFile("procedimientos", " input: " . $dropdown_id . "\r\n\r\n"); 
  
        echo Html::scriptBlock("
        
        $(function() {
        
        var x;
        x=$(document);
        x.ready(inicio);
        
        function inicio(){
        
         var dropdown;
         dropdown=$('#dropdown_plugin_formcreator_forms_id".$dropdown_id."');
         dropdown.change(animate);

         $('#form_in').fadeIn('slow');   
         animate();

         var show_item;
         //show_item=$('#target_in".$dropdown_id."');
         show_item=$('#show_items_id".$dropdown_id."');
         show_item.change(animate);
        
         }
        
        function animate(){
        
        var value = $(this).find('select').val();
        //alert(value);
        var boton = $('input[name=addform]');
        if ((!value) || (value==0)) {
        
        boton.fadeTo( 500 , 0, function() {
        // Animation complete.
        boton.css({ 'display' : 'none', });
        });
        
        /* boton.fadeIn( 500, function() {
        // Animation complete
        });*/
        
        } else {
        
        boton.fadeTo( 500 , 1, function() {
        // Animation complete.
        boton.css({ 'display' : 'inline', });
        });
        
        }
        }
        });
        
        ");
        
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
         echo "<form name='procedimientoform_form$rand' id='procedimientoform_form$rand' method='post'
               action='".Toolbox::getItemTypeFormURL("PluginProcedimientosProcedimiento_Form")."'>";
      if ($canedit) { 
         echo "<div class='firstbloc'>";

         echo "<table class='tab_cadre_fixe'>";
        		
         echo "<tr class='tab_bg_1'><td class='center'>";
        
         echo "<table id='form_in' style='display:none;'  class='tab_cadrehov' width='100%'><thead><tr class='tab_bg_2'>";

         echo "<th>";
         
         echo __('Añadir el pedido de catálogo al que mostrar el procedimiento');
         
         echo "</th></tr></thead>";
         
         echo "<body><tr><td align='center'>";
         
         
               echo "<div class='fa-label'>
                  <i class='fas fa-filter fa-fw'
                     title='".__('Pedidos de Catálogo')."'></i>";
                  
                     $rand = PluginFormcreatorForm::dropdown(
                        [
                           'name'         => 'plugin_formcreator_forms_id', 
                           'entity_sons'  => true,
                           'entity'       => intval($procedimiento->fields['entities_id']),
                           'width'        => '600px'
                        ]
                     );         
                     $identificador = "dropdown_plugin_formcreator_forms_id".$rand;
                     
                     echo "<script>	
                              $('#".$identificador."').on('change', function(event) { 
                              $('#target_in".$rand."').fadeIn('slow'); 
                              $('#show_items_id".$rand."').load('".$CFG_GLPI["root_doc"]."/ajax/dropdownAllItems.php'
                              ,{ idtable:'PluginFormcreatorTargetTicket',
                                 name:'plugin_formcreator_targettickets_id',
                                 entity_restrict:-1,
                                 showItemSpecificity:'',
                                 condition: { 'plugin_formcreator_forms_id' : $(this).val() } 
                                 }
                              );
                              });
                           </script>";   					
                  
                  echo "</div>";				
         echo "</td>";
         echo "</tr>";
         echo "</body></table>";

   echo "<table id='target_in".$rand."' style='display:none;'  class='tab_cadrehov' width='100%'><thead><tr class='tab_bg_2'>";

	echo "<th>";
        
   echo __('Añadir destino al que mostrar el procedimiento.');
      
	echo "</th></tr></thead>";
	
    echo "<body><tr><td align='center'>";
	
	
          echo "<div class='fa-label'>
            <i class='fas fa-filter fa-fw'
               title='".__('Pedidos de Catálogo')."'></i>";
			   
               echo "<span id='show_items_id".$rand."'></span>"; 					
			   
            echo "</div>";				
	echo "</td>";
	echo "</tr>";
	echo "</body></table>";

 

         self::boton($rand);
         
      echo "</td><td class='center'>";
         echo "<input type='submit' name='addform' value=\""._sx('button', 'Add')."\" class='submit'>";
         echo "<input type='hidden' name='".PluginProcedimientosProcedimiento_Form::$items_id_1."' value='$instID'>";
         echo "</td></tr>";
         echo "</table>";	 
         echo "</div>";


         
        
         
      }
		 Html::closeForm();	

      $query     = "SELECT *, `id` AS IDD
                    FROM " . getTableForItemType(__CLASS__);
      $query .= " WHERE plugin_procedimientos_procedimientos_id =".$instID;
				 
		$result_linked = $DB->query($query);				 
				 
		$numrows=$DB->numrows($result_linked);				 

      echo '<div class="spaced">';
      $massContainerId = 'mass' . __CLASS__ . $rand;
      if ($canedit && $numrows) {
         Html::openMassiveActionsForm($massContainerId);
         $massiveactionparams = [
            'num_displayed' => min($_SESSION['glpilist_limit'], $numrows),
            'container'     => $massContainerId,
         ];
         
         Html::showMassiveActions($massiveactionparams);
      }

      echo '<table class="tab_cadre_fixehov">';
      echo '<tr class="noHover">';
      echo '<th colspan="12">Pedidos de Catálogo</th>';
      echo '</tr>';
      if ($numrows) {
         self::commonListHeader(Search::HTML_OUTPUT, $massContainerId);
         Session::initNavigateListItems(
            PluginProcedimientosProcedimiento::class,
            //TRANS : %1$s is the itemtype name,
            //        %2$s is the name of the item (used for headings of a list)
            sprintf(__('%1$s = %2$s'), $procedimiento::getTypeName(1), $procedimiento->fields['name'])
         );

         $i = 0;
      
               if ($numrows>0) {
                  while ($data = $DB->fetchassoc($result_linked)) {
                     Session::addToNavigateListItems(PluginProcedimientosProcedimiento_Form::class, $data['id']);
                     self::showShort(
                        $data['id'],
                        [
                           'row_num'               => $i,
                           'type_for_massiveaction' => __CLASS__,
                           'id_for_massiveaction'   => $data['IDD']
                        ]
                     );
					}
			   }
	  
            self::commonListHeader(Search::HTML_OUTPUT, $massContainerId);
      }
      echo '</table>';

      if ($canedit && $numrows) {
         $massiveactionparams['ontop'] = false;         
         Html::showMassiveActions($massiveactionparams);
         Html::closeForm();
      }
      echo '</div>';

   } 

	/**
	* Muestra los elementos de un procedimientos.
    **/
	
 /*  static function showForProcedimiento(PluginProcedimientosProcedimiento $procedimiento) {
      global $DB, $CFG_GLPI;
	  
      $instID = $procedimiento->fields['id'];
	
      if (!$procedimiento->can($instID, READ)) {
         return false;
      }
      $canedit = $procedimiento->can($instID, UPDATE);

      $rand   = mt_rand();
         echo "<form name='procedimientoform_form$rand' id='procedimientoform_form$rand' method='post'
               action='".Toolbox::getItemTypeFormURL("PluginProcedimientosProcedimiento_Form")."'>";
      if ($canedit) {
         echo "<div class='firstbloc'>";


         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_2'><th colspan='2'>A&ntilde;adir pedidos de catalogo a los que mostrar procedimientos</th></tr>";

         echo "<tr class='tab_bg_1'><td class='center'>";
         
		 PluginFormcreatorForm::dropdown(array('name'      => 'plugin_formcreator_forms_id', 'entity_sons'=>true,
                               'entity'    => $procedimiento->fields["entities_id"]));		
         echo "</td><td class='center'>";
         echo "<input type='submit' name='addform' value=\""._sx('button', 'Add')."\" class='submit'>";
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
      $query     = "SELECT `glpi_plugin_formcreator_forms`.*,
                           `glpi_plugin_procedimientos_procedimientos_forms`.`id` AS IDD, ";


      $query .= "`glpi_entities`.`id` AS entity
                  FROM `glpi_plugin_procedimientos_procedimientos_forms`, `glpi_plugin_formcreator_forms`, `glpi_entities` ";
      $query .= "WHERE plugin_procedimientos_procedimientos_id=".$instID." and `glpi_plugin_formcreator_forms`.`id` = `glpi_plugin_procedimientos_procedimientos_forms`.`plugin_formcreator_forms_id`
				 GROUP BY `glpi_plugin_formcreator_forms`.id ORDER BY `glpi_plugin_formcreator_forms`.name";
      if ($result_linked = $DB->query($query)) {
               if ($DB->numrows($result_linked)) {
                  while ($data = $DB->fetchassoc($result_linked)) {
                     $linkname = $data["name"];
                     if ($_SESSION["glpiis_ids_visible"]
                         || empty($data["name"])) {
                        $linkname = sprintf(__('%1$s (%2$s)'), $linkname, $data["id"]);
                     }

                     $link = '../../../plugins/formcreator/front/form.form.php';
                     $name = "<a href=\"".$link."?id=".$data["id"]."\">".$linkname."</a>";

                     echo "<tr class='tab_bg_1'>";

                     if ($canedit) {
                        echo "<td width='10' style='padding-top: 0'>";
                        echo "<button type='submit'  value='".$data["id"]."' name='elimina' style='border:0; background-color: Transparent;' 
						onclick=\"return confirm('¿Seguro que deseas quitarle a este pedido este procedimientos?');\">
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
//	  echo "</form>";

   }
   */


   // *******************************************************************************************
   //  [FINAL] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
   // *******************************************************************************************


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

  // *******************************************************************************************
  //  [INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
  // ******************************************************************************************* 
  // Este código se basa en el pedido de catálogo NO en su destino.
  /*    

   public function export($remove_uuid = false) {
      if (!$this->getID()) {
         return false;
      }

      $procedimiento_forms = $this->fields;
      $forms_id=$procedimiento_forms['plugin_formcreator_forms_id'];
      // remove key and fk
      unset($procedimiento_forms['id'],
            $procedimiento_forms['plugin_procedimientos_procedimientos_id']);
      
         $procedimiento_form_obj = new PluginFormcreatorForm;
         if ($procedimiento_form_obj->getFromDB($forms_id)) {
             
           $exportacion_form=$procedimiento_form_obj->export($remove_uuid);
           //$procedimiento_forms["PluginFormcreatorForm"] = $exportacion_form;
           $procedimiento_forms["uuid"] = $exportacion_form["uuid"];
       } 
   
  return $procedimiento_forms;

  
         } */

  // Este código se basa en el destino del pedido de catálogo.
         public function export($remove_uuid = false) {
            if (!$this->getID()) {
               return false;
            }
      
            $procedimiento_forms = $this->fields;
            $forms_id         = $procedimiento_forms['plugin_formcreator_forms_id'];
            $targettickets_id = $procedimiento_forms['plugin_formcreator_targettickets_id'];
            // remove key and fk
            unset($procedimiento_forms['id'],
                  $procedimiento_forms['plugin_procedimientos_procedimientos_id']);
            
               $procedimiento_form_obj = new PluginFormcreatorTargetTicket;
               if ($procedimiento_form_obj->getFromDB($targettickets_id)) {
                   
                 $exportacion_form=$procedimiento_form_obj->export($remove_uuid);
                 //$procedimiento_forms["PluginFormcreatorForm"] = $exportacion_form;
                 $procedimiento_forms["uuid"] = $exportacion_form["uuid"];
             } 
         
        return $procedimiento_forms;
      
        
               }         
         
  // *******************************************************************************************
  //  [FINAL] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR 
  // *******************************************************************************************                   
         
     /**
    * Process import of json file(s) sended by the submit of self::showImportForm
    * @param  array  $params GET/POST data who need to contains the filename(s) in _json_file key
    */
   public function importJson($params = array()) {
      // parse json file(s)
     if (isset($params['_json_file'])){
      foreach ($params['_json_file'] as $filename) {
         if (!$json = file_get_contents(GLPI_TMP_DIR."/".$filename)) {
            Session::addMessageAfterRedirect(__("<STRONG><FONT color='red'>Procedimiento no importable,</FONT> <FONT color='#a42090'>el archivo está vacío</font><FONT color='red'>.</FONT></STRONG>"));
            continue;
         }
         if (!$forms_toimport = json_decode($json, true)) {
            Session::addMessageAfterRedirect(__("<STRONG><FONT color='red'>Procedimiento no importable,</FONT> <FONT color='#a42090'>el archivo parece corrupto</font><FONT color='red'>.</FONT></STRONG>"));
            continue;
         }
         if (!isset($forms_toimport['procedimiento'])) {
            Session::addMessageAfterRedirect(__("<STRONG><FONT color='red'>Procedimiento no importable,</FONT> <FONT color='#a42090'>el archivo parece corrupto</font><FONT color='red'>.</FONT></STRONG>"));
            continue;
         }
      
     
                  foreach ($forms_toimport['procedimiento'] as $importar_procedimiento) {                                        
       

            foreach ($importar_procedimiento as $id=>$value){
                          
                            if (is_array($importar_procedimiento[$id])) {                            
                            
                            foreach ($importar_procedimiento[$id] as $id2=>$value){
   
                            if (is_array($importar_procedimiento[$id][$id2])) { 
                                    
                            foreach ($importar_procedimiento[$id][$id2] as $id3=>$value){

                            if (is_array($importar_procedimiento[$id][$id2][$id3])) {
                                              
                            foreach ($importar_procedimiento[$id][$id2][$id3] as $id4=>$value){
                                                  
                            if (is_array($importar_procedimiento[$id][$id2][$id3][$id4])) {
                                                          
                            foreach ($importar_procedimiento[$id][$id2][$id3][$id4] as $id5=>$value){
                            
                            if (is_array($importar_procedimiento[$id][$id2][$id3][$id4][$id5])) {
                             
                            foreach ($importar_procedimiento[$id][$id2][$id3][$id4][$id5] as $id6=>$value){
								
							if (is_array($importar_procedimiento[$id][$id2][$id3][$id4][$id5][$id6])) {

							foreach ($importar_procedimiento[$id][$id2][$id3][$id4][$id5][$id6] as $id7=>$value){				
							 //                      echo " 7<br>";							
							 $importar_procedimiento[$id][$id2][$id3][$id4][$id5][$id6][$id7]=self::reemplazar($importar_procedimiento[$id][$id2][$id3][$id4][$id5][$id6][$id7], "'", "\'");                                             
							} } else {							
                             //                      echo " 6<br>"; 
                            $importar_procedimiento[$id][$id2][$id3][$id4][$id5][$id6]=self::reemplazar($importar_procedimiento[$id][$id2][$id3][$id4][$id5][$id6], "'", "\'");                                                                                                                                       
                            } } } else {                            
                            //                       echo " 5<br>"; 
                            $importar_procedimiento[$id][$id2][$id3][$id4][$id5]=self::reemplazar($importar_procedimiento[$id][$id2][$id3][$id4][$id5], "'", "\'");                                                                                                            
                            } } } else {                        
                            //                       echo "4<br>"; 
                            $importar_procedimiento[$id][$id2][$id3][$id4]=self::reemplazar($importar_procedimiento[$id][$id2][$id3][$id4], "'", "\'");                                                                 
                            } } }else {
                            //                       echo "3<br>";    
                            $importar_procedimiento[$id][$id2][$id3]=self::reemplazar($importar_procedimiento[$id][$id2][$id3], "'", "\'");       
                             } } } else {
                               //                     echo "2<br>";   
                             $importar_procedimiento[$id][$id2]=self::reemplazar($importar_procedimiento[$id][$id2], "'", "\'");   
                             
                             } } } else{
                             //                        echo "1<br>";
                          $importar_procedimiento[$id]=self::reemplazar($importar_procedimiento[$id], "'", "\'");
                          
                           }
                          
           } 
                  
                                      
 //var_dump($importar_procedimiento);


         $resultado =  self::import($importar_procedimiento);
         
         
         
                       if ( $resultado=="ko") {   
    
    Session::addMessageAfterRedirect(sprintf(__("<FONT color='red'>El <STRONG>Procedimiento</STRONG>: </font><FONT color='#a42090'><STRONG> %s </STRONG></font><FONT color='red'> ya existe en su <STRONG>GLPI</STRONG>.</font>", "procedimiento"),
                                                  $importar_procedimiento['name']));   
                 
   
            } else {
                
 Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='#0a7c07'>Procedimiento:</font><FONT color='#a42090'><STRONG><a href='".$_SESSION["glpiroot"]."/plugins/procedimientos/front/procedimiento.form.php?id=".$resultado."'> %s</a> </STRONG></font><FONT color='#0a7c07'>, importado satisfactoriamente desde: </font><FONT color='#a42090'><STRONG>%s</STRONG></font><FONT color='#0a7c07'>.</font></STRONG>", "procedimiento"),
                                                  $importar_procedimiento['name'],$filename));      
                
            }   
            
                  }


        
    
      
      
            }
     }
   }
   
   /**
    * Import a procedures into the db
    * @see PluginProcedimientoForm::importJson
    *
    * @param  array   $form the form data (match the form table)
    * @return integer the form's id
    */
   public static function import($procedure = array()) {
      
	    global $DB;
	  
	  $procedure_obj = new PluginProcedimientosProcedimiento;
      $formcreator_obj = new PluginFormcreatorForm;

     //[INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR  
     // Este código se basa en el destino del pedido de catálogo.
      $formcreator_target = new PluginFormcreatorTargetTicket;
     //[FINAL] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR       

      $group_obj  = new PluginProcedimientosProcedimiento_Group; 
      $glpi_Grupo_obj  = new Group;
	   $recurrente_obj  = new PluginProcedimientosProcedimiento_TicketRecurrent;	
	   $glpi_recurrentes_obj  = new TicketRecurrent;		  
	//  $documents_obj  = new Document_Item; 
      
      $entity   = new Entity;

      $formulario = new self;
      
    // retrieve foreign keys
      if (!isset($procedure['_entity'])
          || !$procedure['entities_id']
                  = plugin_porcedimientos_getFromDBByField($entity,
                                                        'completename',
                                                        $procedure['_entity'])) {
         $procedure['entities_id'] = $_SESSION['glpiactive_entity'];
      }
      
      // retrieve procedure by its uuid
      if ($procedures_id = plugin_porcedimientos_getFromDBByField($procedure_obj,
                                                          'uuid',
                                                          $procedure['uuid'])) {
       
          //Si existe el procedimiento en nuestro GLPI abortamos importacion                     
                              
                           //     return "ko";
                     
         // add id key
        $procedure['id'] = $procedures_id;

         // update existing procedure
        $procedure_obj->update($procedure);
		
	             Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='green'>ACTUALIZAR PROCEDIMIENTO:</font><br><a href='".$_SESSION["glpiroot"]."/plugins/procedimientos/front/procedimiento.form.php?id=".$procedures_id."'> %s</a><br>", "procedimiento"),
                                                  $procedure['name']));		
		
		
	                    $query = "SELECT uuid, itemtype, items_id
                   FROM `glpi_plugin_procedimientos_procedimientos_items`
                   WHERE  plugin_procedimientos_procedimientos_id='".$procedures_id."'";
         $result = $DB->query($query);
		 $items_originales = $DB->fetchassoc($result);
		 
      
      } else {
         
        // create new procedure
         $procedures_id = $procedure_obj->add($procedure);    

	             Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='green'>NUEVO PROCEDIMIENTO:</font><br><a href='".$_SESSION["glpiroot"]."/plugins/procedimientos/front/procedimiento.form.php?id=".$procedures_id."'> %s</a><br>", "procedimiento"),
                                                  $procedure['name']));													  
	  
	  }
	   
    //============================== import procedure's items ===================================         
         
            
      if ($procedures_id
          && isset($procedure['_items'])) {

         foreach ($procedure['_items'] as $item) {
		  $items_importados[] = $item["uuid"];		  
		  } 		  
			  
		PluginProcedimientosProcedimiento_Item::purgar_no_encontrados($procedures_id, $items_importados);	
			  
         foreach ($procedure['_items'] as $item) {
          PluginProcedimientosProcedimiento_Item::import($procedures_id, $item);	  
		  } 		

      }
	  

    //[INICIO] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR   
    //Este código se basa en el pedido de catálogo NO en su destino.           
    //============================== import procedure's forms ===================================
    /*           
      if ($procedures_id
          && isset($procedure['_forms'])) {
                   
				         $where="";
				   
              foreach ($procedure['_forms'] as $form) {
             $form['plugin_procedimientos_procedimientos_id'] = $procedures_id;
             
                  // retrieve procedure by its uuid
      if ($formcreator_id = plugin_porcedimientos_getFromDBByField($formcreator_obj,
                                                          'uuid',
                                                          $form['uuid'])) {
  
      $form['plugin_formcreator_forms_id']=$formcreator_id;
      $formulario->addItem($form);
       $where=$where." and plugin_formcreator_forms_id <> ".$form['plugin_formcreator_forms_id'];
      } else {
         
           Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>Plugin FormCreator no encontrado uuid:</font><br><FONT color='#a42090'> %s </font><br>", "procedimiento"),
                                                  $form['uuid']));  
      }                        
		 
		 }    
         
		 
		   $query = "DELETE FROM `glpi_plugin_procedimientos_procedimientos_forms` WHERE plugin_procedimientos_procedimientos_id = ".$procedures_id." ".$where;
	
      //	echo $query;
	
	  	   $DB->queryOrDie($query, "0.5 delete pedidos de catalogo no incluidos en el procedimiento importado");   
		 	 
      }      
   */  

      // Este código se basa en el destino del pedido de catálogo.
         if ($procedures_id
         && isset($procedure['_forms'])) {
                  
                    $where="";
              
             foreach ($procedure['_forms'] as $form) {
            $form['plugin_procedimientos_procedimientos_id'] = $procedures_id;
            
                 // retrieve procedure by its uuid
     if ($targettickets_id = plugin_porcedimientos_getFromDBByField($formcreator_target,
                                                         'uuid',
                                                         $form['uuid'])) {
 
      $formcreator_target->getFromDB($targettickets_id);
      $form['plugin_formcreator_targettickets_id'] = $targettickets_id;                                                    
      $form['plugin_formcreator_forms_id']         = $formcreator_target->fields["plugin_formcreator_forms_id"];
     
      $formulario->addItem($form);
      $where=$where." and ( plugin_formcreator_forms_id <> ".$form['plugin_formcreator_forms_id'];
      $where=$where." and plugin_formcreator_targettickets_id <> ".$form['plugin_formcreator_targettickets_id'] . " ) ";
     } else {
        
      Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>Plugin FormCreator no encontrado uuid:</font><br><FONT color='#a42090'> %s </font><br>", "procedimiento"),
                                                 $form['uuid']));  
     }                        
      
      }    
        
      
         $query = "DELETE FROM `glpi_plugin_procedimientos_procedimientos_forms` WHERE plugin_procedimientos_procedimientos_id = ".$procedures_id." ".$where;
  
         //	echo $query;
  
         $DB->queryOrDie($query, "0.5 delete pedidos de catalogo no incluidos en el procedimiento importado");   
      
      
      
     }       

    //[FINAL] [CRI] JMZ18G ASOCIAR AL PLUGIN EL DESTINO DEL TICKET DE FORMCREATOR      
   
    //============================== import procedure's groups ===================================   
               
      if ($procedures_id
          && isset($procedure['_groups'])) {
                 /*  var_dump($procedure['_groups']);
				   exit();*/
				   
				      $where="";
				   
              foreach ($procedure['_groups'] as $group) {
             $group['plugin_procedimientos_procedimientos_id'] = $procedures_id;
             
$params = [
"id" => $group['groups_id'],
"name" => $group['name'],
];			 
			 	      $grupo = $glpi_Grupo_obj->find($params);
			 
			  if (count($grupo)>0) { // si existe en los grupos de GLPI
			 
	/*  if ($glpi_groups_id = plugin_porcedimientos_getFromDBByField($glpi_Grupo_obj,
                                                          'id',
                                                          $group['groups_id'])) { // si existe en los grupos de GLPI*/
														  
$params = [
"plugin_procedimientos_procedimientos_id" => $procedures_id,
"groups_id" => $group['groups_id'],
];															  
														  
      $groups = $group_obj->find($params);										 
											 
      if (count($groups) == 0) {
		  
		  $group['plugin_procedimientos_procedimientos_id'] = $procedures_id;
		  $group_obj->add($group);  
		  
      } else {
		  		  
		 //  Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>El Grupo</font>: <FONT color='#a42090'> %s</font>, ESTA ENLAZADO. <br> Nombre: <FONT color='#a42090'> %s</font>.</STRONG><br>", "procedimiento"),
         //                                         $group['groups_id'], $group['name']));  										  
												  
      }	

	 $where=$where." and groups_id <> ".$group['groups_id'];

	  } ELSE {

$params = [
"name" => $group['name'],
];	

			 	      $grupo_nombre = $glpi_Grupo_obj->find($params);
				  
					  
	  if (count($grupo_nombre)>0) { // si existe un grupo con el nombre indicado en GLPI
	  	  
		foreach($grupo_nombre as $datagrp) {
			$grupo_original=$group['groups_id'];
			$group['groups_id'] = $datagrp['id'];
			 
		}
	  
	  		  $group['plugin_procedimientos_procedimientos_id'] = $procedures_id;
		 
		  $group_obj->add($group);
	  
	  		  Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>El Grupo enlazado </font>: <FONT color='#a42090'> %s</font>. <br> No coincide con la ID: <FONT color='#a42090'> %s</font> <br> Nueva ID: <FONT color='#a42090'> %s</font>.</STRONG><br>", "procedimiento"),
                                                  $group['name'] , $grupo_original, $group['groups_id'])); 

												   $where=$where." and groups_id <> ".$group['groups_id'];
												  
	  } else {
		  
						//echo "el groups_id NO existe EN glpi: ".$group['groups_id'];
						//exit();
		   
		   Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>El Grupo</font>: <FONT color='#a42090'> %s</font>, No existe en GLPI. <br> Nombre: <FONT color='#a42090'> %s</font>.</STRONG><br>", "procedimiento"),
                                                  $group['groups_id'], $group['name']));													  
						
		  
	  } }	

		  }   

$query = "DELETE FROM `glpi_plugin_procedimientos_procedimientos_groups` WHERE plugin_procedimientos_procedimientos_id = ".$procedures_id." ".$where;
	
	//echo $query;
	
	  $DB->queryOrDie($query, "0.5 delete grupos no incluidos en el procedimiento importado");          
             
         }   
		 
    //============================== import procedure's Tickets Recurrentes ===================================   
               
      if ($procedures_id
          && isset($procedure['_tickets_recurrent'])) {
                   $where="";
              foreach ($procedure['_tickets_recurrent'] as $recurrente) {
             $recurrente['plugin_procedimientos_procedimientos_id'] = $procedures_id;
      
$params = [
"id" => $recurrente['ticketrecurrents_id'],
"name" => $recurrente['name'],
];	

	  			 	      $ticket_recurrente = $glpi_recurrentes_obj->find($params);

			  if (count($ticket_recurrente) > 0) { // si existe en los grupos de GLPI
	  
			 
	 /* if ($glpi_ticketrecurrents_id = plugin_porcedimientos_getFromDBByField($glpi_recurrentes_obj,
                                                          'id',
                                                          $recurrente['ticketrecurrents_id'])) { */ // si existe en los grupos de GLPI
															  
$params = [
"plugin_procedimientos_procedimientos_id" => $procedures_id,
"ticketrecurrents_id" => $recurrente['ticketrecurrents_id'],
];
															  
      $recurrentes = $recurrente_obj->find($params);										 
											 
      if (count($recurrentes) == 0) {
     		  $recurrente['plugin_procedimientos_procedimientos_id'] = $procedures_id;
              $recurrente_obj->add($recurrente); 
      } else {   	
												  
		//   Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>Ticket Recurrente </font>: <FONT color='#a42090'> %s</font>, ESTA ENLAZADO. <br> Nombre: <FONT color='#a42090'> %s</font>.</STRONG><br>", "procedimiento"),
        //                                        $recurrente['ticketrecurrents_id'], $recurrente['name']));												  
												  
      }	

	  		  $where=$where." and ticketrecurrents_id <> ".$recurrente['ticketrecurrents_id'];

	  } ELSE {   
	  
$params = [
"name" => $recurrente['name'],
];
	  
	   $recurrente_nombre = $glpi_recurrentes_obj->find($params);
			
					  
	  if (count($recurrente_nombre)>0) { // si existe un grupo con el nombre indicado en GLPI
	  	  
		foreach($recurrente_nombre as $datarecurrente) {
			$recurrente_original=$recurrente['ticketrecurrents_id'];
			$recurrente['ticketrecurrents_id'] = $datarecurrente['id'];
			 
		}
	  
	  		  $recurrente['plugin_procedimientos_procedimientos_id'] = $procedures_id;
		 
		  $recurrente_obj->add($recurrente);
	  
	  		  Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>El Grupo enlazado </font>: <FONT color='#a42090'> %s</font>. <br> No coincide con la ID: <FONT color='#a42090'> %s</font> <br> Nueva ID: <FONT color='#a42090'> %s</font>.</STRONG><br>", "procedimiento"),
                                                  $recurrente['name'] , $recurrente_original, $recurrente['ticketrecurrents_id'])); 

		  $where=$where." and ticketrecurrents_id <> ".$recurrente['ticketrecurrents_id'];
												  
	  } else {
	  
	  
		  
						//echo "el ticketrecurrents_id NO existe EN glpi: ".$recurrente['ticketrecurrents_id'];
						//exit();

		   Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>Ticket Recurrente</font>: <FONT color='#a42090'> %s</font>, No existe en GLPI. <br> Nombre: <FONT color='#a42090'> %s</font>.</STRONG><br>", "procedimiento"),
                                                  $recurrente['ticketrecurrents_id'], $recurrente['name']));						
						
		  
	  }	}	
		  
		  }

$query = "DELETE FROM `glpi_plugin_procedimientos_procedimientos_ticketrecurrents` WHERE plugin_procedimientos_procedimientos_id = ".$procedures_id." ".$where;
	
//	echo $query;
	
	  	  $DB->queryOrDie($query, "0.5 delete ticketrecurrents no incluidos en el procedimiento importado");  		  
             
         } 		 
  

    //============================== import procedure's documents ===================================   
               
   /*   if ($procedures_id
          && isset($procedure['_documents_items'])) {
                   
              foreach ($procedure['_documents_items'] as $documents) {
          
                  if ($documents_id = plugin_porcedimientos_getFromDBByField($documents_obj,
                                                          'documents_id',
                                                          $documents['documents_id'])) {    
       $documents['items_id'] = $procedures_id;             
       $documents_obj->add($documents);
     
             }   else {
         
           Session::addMessageAfterRedirect(sprintf(__("<STRONG><FONT color='red'>Documento no encontrado con ID:</font><FONT color='#a42090'> %s </font></STRONG><br>", "procedimiento"),
                                                  $documents['documents_id']));  
      } 
       
      }             
             
         }   */      
  

 $update="UPDATE glpi_plugin_procedimientos_condicions a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.line_id_1 = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.id_1 =  IFNULL(b.id,0),
a.line_id_1 = IFNULL(b.line,0)
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 
  
 $update="UPDATE glpi_plugin_procedimientos_condicions a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.line_id_2 = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.id_2 =  IFNULL(b.id,0),
a.line_id_2 = IFNULL(b.line,0)
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 

 $update="UPDATE glpi_plugin_procedimientos_condicions a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.line_id_3 = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.id_3 =  IFNULL(b.id,0),
a.line_id_3 = IFNULL(b.line,0)
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 

 $update="UPDATE glpi_plugin_procedimientos_condicions a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.line_id_4 = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.id_4 =  IFNULL(b.id,0),
a.line_id_4 = IFNULL(b.line,0)
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 

 $update="UPDATE glpi_plugin_procedimientos_condicions a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.line_id_5 = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.id_5 =  IFNULL(b.id,0),
a.line_id_5 = IFNULL(b.line,0)
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 

/*  
$update="UPDATE glpi_plugin_procedimientos_condicions a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.line_id_1 = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
LEFT join glpi_plugin_procedimientos_procedimientos_items c
on a.line_id_2 = c.line and c.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
LEFT join glpi_plugin_procedimientos_procedimientos_items d
on a.line_id_3 = d.line and d.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
LEFT join glpi_plugin_procedimientos_procedimientos_items e
on a.line_id_4 = e.line and e.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
LEFT join glpi_plugin_procedimientos_procedimientos_items f
on a.line_id_5 = f.line and f.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.id_1 =  IFNULL(b.id,0),
a.line_id_1 = IFNULL(b.line,0), 
a.id_2      = IFNULL(c.id,0), 
a.line_id_2 = IFNULL(c.line,0), 
a.id_3      = IFNULL(d.id,0), 
a.line_id_3 = IFNULL(d.line,0), 
a.id_4      = IFNULL(e.id,0),  
a.line_id_4 = IFNULL(e.line,0), 
a.id_5      = IFNULL(f.id,0), 
a.line_id_5 = IFNULL(f.line,0)
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 	
*/

$update="UPDATE glpi_plugin_procedimientos_saltos a
LEFT join glpi_plugin_procedimientos_procedimientos_items b
on a.goto = b.line and b.plugin_procedimientos_procedimientos_id = a.plugin_procedimientos_procedimientos_id
SET a.goto_id =  IFNULL(b.id,0),
a.goto =  IFNULL(b.line,0),
a.comment =  if (b.line IS NULL ,\"<font color=\'red\'>Error, El salto no tiene aosciada ninguna linea</font>\"  ,CONCAT(\"Ir a #linea: \", b.line))
Where a.plugin_procedimientos_procedimientos_id='".$procedures_id."'";

$DB->query($update); 	
 
   
		// echo $update;
		 
	//	 exit();
          return $procedures_id;
      


 /*   // Save all question conditions stored in memory
      PluginFormcreatorQuestion_Condition::import(0, array(), false);

      // import form's validators
      if ($procedures_id
          && isset($procedure['_validators'])) {
         foreach ($procedure['_validators'] as $validator) {
            PluginFormcreatorForm_Validator::import($procedures_id, $validator);
         }
      }

      // import form's targets
      if ($procedures_id
          && isset($procedure['_targets'])) {
         foreach ($procedure['_targets'] as $target) {
            PluginFormcreatorTarget::import($procedures_id, $target);
         }
      }*/

  

   }  
   
  /**
    * Display an html form to upload a json with forms data
    */
   public function showImportForm() {
      global $CFG_GLPI;

      echo "<form name='form' method='post' action='".
            PluginProcedimientosProcedimiento_Form::getFormURL().
            "?import_send=1' enctype=\"multipart/form-data\">";

      echo "<div class='spaced' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe' id='mainformtable'>";
      echo "<tr class='headerRow'>";
      echo "<th>";
      echo __("Importar Procedimientos");
      echo "</th>";
      echo "</tr>";
      echo "<tr>";
      echo "<td>";
      echo Html::file(array('name' => 'json_file'));
      echo "</td>";
      echo "</tr>";
      echo "<td class='center'>";
      echo Html::submit(_x('button', 'Send'), array('name' => 'import_send'));
      echo "</td>";
      echo "</tr>";
      echo "<tr>";
      echo "</table>";
      echo "</div>";

      Html::closeForm();
   }  

public function elemento_array($cadena, $busco, $reemplazo){
    
    
}   
   
   
public function reemplazar($cadena, $busco, $reemplazo){
	  
    if (is_array($cadena)) {  
    
        echo "es array y corto<br>";
        var_dump($cadena);
        exit();
    
        
    }
	$buscar=explode("*",$busco);

	$reemplazar=explode("*",$reemplazo);

	$formulario=str_ireplace($buscar,$reemplazar,$cadena);
	
	return $formulario;  

           
           
           
           }
   

}
?>