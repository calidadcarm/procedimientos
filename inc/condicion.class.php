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
include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");

// Class of the defined type
class PluginProcedimientosCondicion extends CommonDBTM {
     
   // From CommonDBTM
   public $table            = 'glpi_plugin_procedimientos_condicions';
   public $type             = 'PluginProcedimientosCondicion';

   static $rightname = "plugin_procedimientos";
  
   static function getTypeName($nb=0) {
		return _n('Condiciones','Condiciones',$nb);
   }
      
    function defineTabs($options=array()) {
      global $LANG;

       $ong = array();
	   $this->addDefaultFormTab($ong);
       $this->addStandardTab('Log', $ong, $options);
      return $ong;
   }    
   
   function showForm($ID, $options=array()) {
    global $CFG_GLPI, $DB;
     
	
    $this->initForm($ID, $options);
    $this->showFormHeader($options);
	
	// Id del Valor
	if ($ID>0){ // Condición de un procedimiento.
		$procedimientos_id = $this->fields['plugin_procedimientos_procedimientos_id'];
		$lines = getLineasProcedimiento($procedimientos_id);
		$lines_id = getLineasProcedimiento_id($procedimientos_id);
		echo "<tr class='tab_bg_1'>";	

		echo '<tr><td colspan="4" class=""></td></tr>';

		echo '<tr class="headerRow">
		<th colspan="4" style="text-align:center; padding-left:10px;" class="">';
		echo "<div style='white-space: nowrap; font-size: 1.3em;'>
			 <i class='fas fa-question fa-fw'
					title='".__('Pregunta')."'></i>
					Pregunta
			 </div>
		</th></tr>";

		echo '<tr><td colspan="4" class=""></td></tr>';

		echo "<td colspan='4'>";
		echo "<textarea cols='40' style='width: 99%;' rows='4' name='name'>".$this->fields["name"]."</textarea>";
		echo "</td>";
        echo "</TR>";
		$linea_yes = array_search($this->fields['way_yes'], $lines);
		if ($linea_yes == NULL){
			$linea_yes = 0;
		}
		$linea_no = array_search($this->fields['way_no'], $lines);		
		if ($linea_no == NULL){
			$linea_no = 0;
		}		
			
		if (isset($lines_id[$this->fields['id_1']])){
			$id_1=$this->fields['id_1'];
		} else {
			$id_1 = 0;
		}
		
		if (isset($lines_id[$this->fields['id_2']])){
			$id_2=$this->fields['id_2'];
		} else {
			$id_2 = 0;
		}

		if (isset($lines_id[$this->fields['id_3']])){
			$id_3=$this->fields['id_3'];
		} else {
			$id_3 = 0;
		}

		if (isset($lines_id[$this->fields['id_4']])){
			$id_4=$this->fields['id_4'];
		} else {
			$id_4 = 0;
		}

		if (isset($lines_id[$this->fields['id_5']])){
			$id_5=$this->fields["id_5"];
		} else {
			$id_5 = 0;
		}		
		
		echo '<tr><td colspan="4" class=""></td></tr>';

		echo '<tr class="headerRow">
		<th colspan="4" style="text-align:center; padding-left:10px;" class="">';
		echo "<div style='white-space: nowrap; font-size: 1.3em;'>
			 <i class='fas fa-link fa-fw'
					title='".__('Respuestas')."'></i>
					Respuestas
			 </div>
		</th></tr>";

		echo '<tr><td colspan="4" class=""></td></tr>';
		
		// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	
		echo "<tr style='display:none;' class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_0",['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('way_yes', $lines, array('value'=> $linea_yes, 'width'=>'60px'));
        echo "</td></tr>";	
		 
		echo "<tr style='display:none;' class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_1", ['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('way_no', $lines, array('value'=> $linea_no, 'width'=>'60px'));
        echo "</td></tr>";	
		// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	
		
		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_id_1", ['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('id_1', $lines_id, array('value'=> $id_1, 'width'=>'60px'));
        echo "</td></tr>";		
		
		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_id_2", ['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('id_2', $lines_id, array('value'=> $id_2, 'width'=>'60px'));
        echo "</td></tr>";	

		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_id_3", ['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('id_3', $lines_id, array('value'=> $id_3, 'width'=>'60px'));
        echo "</td></tr>";	

		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_id_4", ['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('id_4', $lines_id, array('value'=> $id_4, 'width'=>'60px'));
        echo "</td></tr>";	

		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($this,"tag_id_5", ['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='left' width='10%'>";
				Dropdown::showFromArray('id_5', $lines_id, array('value'=> $id_5, 'width'=>'60px'));
        echo "</td></tr>";			
		
		echo '<tr><td colspan="4" class=""></td></tr>';

		echo "<input type='hidden' name='plugin_procedimientos_procedimientos_id' value='".$procedimientos_id."'>";	 
		$this->showFormButtons($options);

		return true;
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
   
  
function lineas($post){
	global $DB;
	
	if ((isset($post["id_1"])) and ($post["id_1"]>0)){ $select1=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' and id=".$post['id_1'].") as id_1 "; } else { $select1=""; $post["line_id_1"]=0; }
	if ((isset($post["id_2"])) and ($post["id_2"]>0)){ $select2=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' and id=".$post['id_2'].") as id_2 "; } else { $select2=""; $post["line_id_2"]=0; }
	if ((isset($post["id_3"])) and ($post["id_3"]>0)){ $select3=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' and id=".$post['id_3'].") as id_3 "; } else { $select3=""; $post["line_id_3"]=0; }
	if ((isset($post["id_4"])) and ($post["id_4"]>0)){ $select4=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' and id=".$post['id_4'].") as id_4 "; } else { $select4=""; $post["line_id_4"]=0; }
	if ((isset($post["id_5"])) and ($post["id_5"]>0)){ $select5=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' and id=".$post['id_5'].") as id_5 "; } else { $select5=""; $post["line_id_5"]=0; }
	
	if ((empty($select1)) and (empty($select2)) and (empty($select3)) and (empty($select4)) and (empty($select5))) {
		
	$post["comment"] = "<font color=\'red\'>Error, La condición no tiene asociada ninguna línea</font>";						
				
	} else {			
	
	$query="select id ".$select1.$select2.$select3.$select4.$select5." from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' limit 1";  
		
//		echo $query;
		
	$result=$DB->query($query);	
	
	$lineas = $DB->fetch_assoc($result);	
	
	$comment="<div class=\'condiciones\'>";
	
	if ((isset($lineas["id_1"])) and ($lineas["id_1"]>0) and (isset($post["tag_id_1"])) and (!empty($post["tag_id_1"]))){ 
	
	$comment=$comment."<font color=\'#197a6d\'>Respuesta <strong>".$post['tag_id_1']."</strong>, ir a #linea: <strong><span id=\'linea_".$post['id_1']."\'>".$lineas['id_1']."</span></strong></font><BR>";  
	$post["line_id_1"] = $lineas['id_1'];
	
	} else {

	if ((isset($post["tag_id_1"])) and (!empty($post["tag_id_1"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 1 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_1"])) and ($lineas["id_1"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 1 carece de TAG</font><font color=\'#197a6d\'>, ir a #linea: <strong><span id=\'linea_".$post['id_1']."\'>".$lineas['id_1']."</span></strong></font><BR>";

	}	
	}
	}		
	
	if ((isset($lineas["id_2"])) and ($lineas["id_2"]>0) and (isset($post["tag_id_2"])) and (!empty($post["tag_id_2"]))){ 
	
	$comment=$comment."<font color=\'#197a6d\'>Respuesta <strong>".$post['tag_id_2']."</strong>, ir a #linea: <strong><span id=\'linea_".$post['id_2']."\'>".$lineas['id_2']."</span></strong></font><BR>";  
	$post["line_id_2"] = $lineas['id_2'];
	
	} else {

	if ((isset($post["tag_id_2"])) and (!empty($post["tag_id_2"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 2 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_2"])) and ($lineas["id_2"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 2 carece de TAG</font><font color=\'#197a6d\'>, ir a #linea: <strong><span id=\'linea_".$post['id_2']."\'>".$lineas['id_2']."</span></strong></font><BR>";

	}	
	}
	}


	if ((isset($lineas["id_3"])) and ($lineas["id_3"]>0) and (isset($post["tag_id_3"])) and (!empty($post["tag_id_3"]))){ 
	
	$comment=$comment."<font color=\'#197a6d\'>Respuesta <strong>".$post['tag_id_3']."</strong>, ir a #linea: <strong><span id=\'linea_".$post['id_3']."\'>".$lineas['id_3']."</span></strong></font><BR>";  
	$post["line_id_3"] = $lineas['id_3'];
	
	} else {

	if ((isset($post["tag_id_3"])) and (!empty($post["tag_id_3"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 3 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_3"])) and ($lineas["id_3"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 3 carece de TAG</font><font color=\'#197a6d\'>, ir a #linea: <strong><span id=\'linea_".$post['id_3']."\'>".$lineas['id_3']."</span></strong></font><BR>";

	}	
	}
	}
	
		
	if ((isset($lineas["id_4"])) and ($lineas["id_4"]>0) and (isset($post["tag_id_4"])) and (!empty($post["tag_id_4"]))){ 
	
	$comment=$comment."<font color=\'#197a6d\'>Respuesta <strong>".$post['tag_id_4']."</strong>, ir a #linea: <strong><span id=\'linea_".$post['id_4']."\'>".$lineas['id_4']."</span></strong></font><BR>";  
	$post["line_id_4"] = $lineas['id_4'];
	
	} else {

	if ((isset($post["tag_id_4"])) and (!empty($post["tag_id_4"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 4 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_4"])) and ($lineas["id_4"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 4 carece de TAG</font><font color=\'#197a6d\'>, ir a #linea: <strong><span id=\'linea_".$post['id_4']."\'>".$lineas['id_4']."</span></strong></font><BR>";

	}	
	}
	}
		
	if ((isset($lineas["id_5"])) and ($lineas["id_5"]>0) and (isset($post["tag_id_5"])) and (!empty($post["tag_id_5"]))){ 
	
	$comment=$comment."<font color=\'#197a6d\'>Respuesta <strong>".$post['tag_id_5']."</strong>, ir a #linea: <strong><span id=\'linea_".$post['id_5']."\'>".$lineas['id_5']."</span></strong></font><BR>";  
	$post["line_id_5"] = $lineas['id_5'];
	
	} else {

	if ((isset($post["tag_id_5"])) and (!empty($post["tag_id_5"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 5 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_5"])) and ($lineas["id_5"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 5 carece de TAG</font><font color=\'#197a6d\'>, ir a #linea: <strong><span id=\'linea_".$post['id_5']."\'>".$lineas['id_5']."</span></strong></font><BR>";

	}	
	}
	}		
	
	$comment=$comment."</div>";
	
	$post["comment"] = $comment;
	
	}

	return $post;
	
		
}

 
         /**
    * 
    * Consistencia en la condicion con importaciones con id que no existen
    * 
   **/
   public function buscar_relaciones($ID, $input) {

  global $DB;
  

      /*   $query = "SELECT `id`,(
		 SELECT `id`,
                   FROM `glpi_plugin_procedimientos_procedimientos_items`
                   WHERE `line` = '".$input["line_id_1"]."' and plugin_procedimientos_procedimientos_id = ".$ID.")
                   FROM `glpi_plugin_procedimientos_procedimientos_items`
                   WHERE `id` = '".$input["id_1"]."' and plugin_procedimientos_procedimientos_id = ".$ID;
         $result = $DB->query($query);
		 
		 echo $query;
		 
		 exit();

         if ($DB->numrows($result) > 0) {
            $ok = true;
            while ($data = $DB->fetch_assoc($result)) {
  
     $item = new PluginProcedimientosCondicion;

$params = [
"plugin_procedimientos_procedimientos_id" => $ID,
];	

   $items_originales = $item->find($params);	
  
		$input["id_1"];
		

      return $input;
   }  }  */
   
   }
   
   
 
}
?>