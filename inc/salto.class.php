<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Octubre 2016

   ----------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
        die("Sorry. You can't access directly to this file");
}
include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");

// Class of the defined type
class PluginProcedimientosSalto extends CommonDBTM {
     
   // From CommonDBTM
   public $table            = 'glpi_plugin_procedimientos_saltos';
   public $type             = 'PluginProcedimientosSalto';

   static $rightname = "plugin_procedimientos";
  
   static function getTypeName($nb=0) {
		return _n('Salto','Salto',$nb);
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
	if ($ID>0){ // Salto de un procedimiento.
		$procedimientos_id = $this->fields['plugin_procedimientos_procedimientos_id'];
		$lines = getLineasProcedimiento($procedimientos_id);
		$lines_id = getLineasProcedimiento_id($procedimientos_id);
		echo "<tr class='tab_bg_1'>";	
		echo "<td>Descripci&oacute;n:</td>";
		echo "<td class='left'><textarea cols='50' rows='3' name='name'>".$this->fields["name"]."</textarea>";
		echo "</td>";
        echo "</TR>";

		$goto = array_search($this->fields['goto'], $lines);
		if ($goto == NULL){
			$goto = 0;
		}	
		
		if (isset($lines_id[$this->fields['goto_id']])){
			$goto_id=$this->fields['goto_id'];
		} else {
			$goto_id = 0;
		}
		
		// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	
		echo "<tr style='display:none;' class='tab_bg_1'>";			 
		echo "<td class='left' widht='30px'>Ir a #linea:</td><td class='left' widht='30px'>";
		Dropdown::showFromArray('goto', $lines, array('value'=> $goto));
        echo "</td></tr>";
		// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	
		
		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' widht='30px'>Ir a #linea:</td><td class='left' widht='30px'>";
				Dropdown::showFromArray('goto_id', $lines_id, array('value'=> $goto_id, 'width'=>'60px'));
        echo "</td></tr>";	
	
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
	
	if ((isset($post["goto_id"])) and ($post["goto_id"]>0)){ $query="select line AS goto_id from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$post['plugin_procedimientos_procedimientos_id']."' and id=".$post['goto_id']; } else { $select=""; }
	
	if (empty($query)) {
		
				$post['comment'] = "<font color=\'red\'>Error, El salto no tiene asociado ninguna línea</font>";
				
	} else {			
		
	//echo $query;
		
	$result=$DB->query($query);	
	
	$lineas = $DB->fetch_assoc($result);	
	
	$comment="<div class=\'condiciones\'>";
	if ((isset($lineas["goto_id"])) and ($lineas["goto_id"]>0)){ $comment=$comment."Ir a #linea: <strong><span id=\'linea_".$post['goto_id']."\'>".$lineas['goto_id']."</span></strong><BR>";  }	
	$comment=$comment."</div>";
	$post['comment']=$comment;	
	$post['goto']=$lineas['goto_id'];
	}

	return $post;	
		
}

   
            /**
    * 
    * Consistencia en la condicion con importaciones con id que no existen
    * 
   **/
   public function buscar_relaciones($ID, $input) {
      // Decode (if already encoded) and encode strings to avoid problems with quotes
        // generate a uniq id


      return $input;
   }
}
?>