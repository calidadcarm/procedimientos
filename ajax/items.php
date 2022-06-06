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

include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");

Session::checkRight("plugin_procedimientos", CREATE);

//print_r($_POST);
if (isset($_POST['type']) && !empty($_POST['type'])
    && isset($_POST['right'])) {
   $display = false;
   $rand    = mt_rand();
   $prefix = '';
   $suffix = '';
   if (isset($_POST['prefix']) && !empty($_POST['prefix'])) {
      $prefix = $_POST['prefix'].'[';
      $suffix = ']';
   } else {
      $_POST['prefix'] = '';
   }

   echo "<table class='tab_format'><tr>";
   echo "<input type='hidden' name='procedimientos_id' value='".$_POST['procedimientos_id']."'>";
   
  
   switch ($_POST['type']) {
      case 'PluginProcedimientosMarcador' :
         echo "<td>";
        $addrand=PluginProcedimientosMarcador::dropdown(array('right' => $_POST['right'],
                              'name'  => $prefix.'plugin_procedimientos_marcadors_id'.$suffix));
         echo '<input type="hidden" name="uuid" value="">';
         echo "</td>";
         $display = true;
		 	 
         break;

      case 'PluginProcedimientosAccion' :	
         echo "<td class='left' width='170px'>";
		$addrand = Dropdown::show('PluginProcedimientosTipoaccion', array('name' => '_typeaccion', 'width' => '150px'));	
        $params  = array('typeaccion'  => '__VALUE__',
                         'right' => 'plugin_procedimientos');

         Ajax::updateItemOnSelectEvent("dropdown__typeaccion".$addrand,"itemsaccion$rand",
                                       $CFG_GLPI["root_doc"]."/plugins/procedimientos/ajax/items_accion.php", $params);


         echo "</td>";
         echo "<td><span id='itemsaccion$rand'></span>";
         echo '<input type="hidden" name="uuid" value="">';
         echo "</td>";	 
         $display = true;
         break;

      case 'PluginProcedimientosCondicion' :
		 
		$lines = getLineasProcedimiento($_POST['procedimientos_id']);
		$lines_id = getLineasProcedimiento_id($_POST['procedimientos_id']);
		$cond = new PluginProcedimientosCondicion();

		echo "<td colspan='4'>Pregunta:<BR>";
		echo "<textarea cols='40' style='width: 99%;' rows='3' name='name'></textarea>";
		echo "</td>";
         		 
		echo "<tr class='tab_bg_1'><td colspan='4'>Respuestas:";
		echo "</tr>";
		
	// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	
		echo "<tr style='display:none;' class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond,'tag_0',array('size' => '124'))." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
				Dropdown::showFromArray('way_yes', $lines, array('width'=>'60px'));
        echo "</td></tr>";
		 
		echo "<tr style='display:none;'  class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond, 'tag_1',array('size' => '124'))." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
				Dropdown::showFromArray('way_no', $lines, array('width'=>'60px'));
                echo '<input type="hidden" name="uuid" value="">';
        echo "</td>";
	// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	

	echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond,"tag_id_1",['option' => 'style="width:99%"', 'size' => "124", 'value'=> 'Si'])." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
			$addrand = 	Dropdown::showFromArray('id_1', $lines_id, array('width'=>'60px'));
        echo "</td></tr>";		
		
		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond,"tag_id_2", ['option' => 'style="width:99%"', 'size' => "124", 'value'=> 'No'])." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
			$addrand_2 = Dropdown::showFromArray('id_2', $lines_id, array( 'width'=>'60px', 'class'=>'required'));
        echo "</td></tr>";	

		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond,"tag_id_3",['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
				$addrand_3 = Dropdown::showFromArray('id_3', $lines_id, array( 'width'=>'60px'));
        echo "</td></tr>";	

		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond,"tag_id_4",['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
				$addrand_4 = Dropdown::showFromArray('id_4', $lines_id, array( 'width'=>'60px'));
        echo "</td></tr>";	

		echo "<tr class='tab_bg_1'>";			 
		echo "<td class='left' colspan='3'>";
				Html::autocompletionTextField($cond,"tag_id_5",['option' => 'style="width:99%"', 'size' => "124"])." -> ir a #linea: </td>";
				echo "<td class='center' width='90px'>";
				$addrand_5 = Dropdown::showFromArray('id_5', $lines_id, array( 'width'=>'60px'));
        echo "</td></tr>";			
		
         $display = true;
         break;
		 
      case 'PluginProcedimientosSalto' :
		 
		 $lines = getLineasProcedimiento($_POST['procedimientos_id']);
		 $lines_id = getLineasProcedimiento_id($_POST['procedimientos_id']);
		 
		 echo "<td>Descripci&oacute;n:</td>";
		 echo "<td><textarea id='comentario_salto' cols='50' rows='3' name='name'></textarea>";
		 echo "</td>";
// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	         		 
		 echo "<td style='display:none;'>ir a #linea:</td><td style='display:none;'>";
		 Dropdown::showFromArray('goto', $lines);
                 echo '<input type="hidden" name="uuid" value="">';
         echo "</td>";		 
// ========== jmz18g - INFORGES ========== OCULTO PORQUE ASOCIA LAS CONDICIONES AL CAMPO LINE NO AL ID DEL ITEM ==============	
		 echo "<td>ir a #linea:</td><td>";
		$addrand = Dropdown::showFromArray('goto_id', $lines_id);
         echo "</td>";		 
		 		 
         $display = true;
         break;
		 
      case 'PluginProcedimientosProcedimiento' :
         echo "<td>";
		 
   		 $params = array('right'  => $_POST['right'],
						 'rand' => $rand,
						 'used' => array($_POST['procedimientos_id']),
						 'name' => $prefix.'plugin_procedimientos_procedimientos_id'.$suffix);
						 
		 $addrand = PluginProcedimientosProcedimiento::dropdownProcedimientos('PluginProcedimientosProcedimiento', $params);
			 
                 echo '<input type="hidden" name="uuid" value="">';
	   	 echo "</td>";
	     $display = true;
		 break;
		 
      case 'PluginProcedimientosLink' :	
         echo "<td>";
        $addrand = PluginProcedimientosLink::dropdown(array('right' => $_POST['right'],
                              'name'  => $prefix.'plugin_procedimientos_links_id'.$suffix));
         echo '<input type="hidden" name="uuid" value="">';
         echo "</td>";
         $display = true;
         break;	 
   }

   if ($display && (!isset($_POST['nobutton']) || !$_POST['nobutton'])) {
	   
      echo "<td colspan='4' align='center'><input type='submit' id='alta_item' disabled name='additem' value=\""._sx('button','Add')."\"
                   class='submit'></td>";

				      switch ($_POST['type']) {
      case 'PluginProcedimientosAccion' :
				   
		 echo "<script type='text/javascript'>
				 $('#dropdown__typeaccion".$addrand."').change(function(){ $('#alta_item').prop( 'disabled', true ); });  
		       </script>";	
	  break;			   

      case 'PluginProcedimientosMarcador' :
				   
		 echo "<script type='text/javascript'>
				 $('#dropdown_plugin_procedimientos_marcadors_id".$addrand."').change(function(){  if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  
		       </script>";	
	  break;	
	  
	   case 'PluginProcedimientosCondicion' :
	  
	  /*		 echo "<script type='text/javascript'>

			 $(document).ready(function () { 

			 $('select').change(function(){ requerido(); });  
				 				 			
			 function requerido(){
	 x=0;
	 if ($('#dropdown_id_1".$addrand."').val()>0){ x++; };
	 if ($('#dropdown_id_2".$addrand_2."').val()>0) { x++; };
	 if ($('#dropdown_id_3".$addrand_3."').val()>0) { x++; };
	 if ($('#dropdown_id_4".$addrand_4."').val()>0) { x++; };
	 if ($('#dropdown_id_5".$addrand_5."').val()>0) { x++; };
	if (x>1){ $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true ); }		
		
	}	

}); 

			  </script>";	*/
			  
	  		 echo "<script type='text/javascript'>

			 $(document).ready(function () { 

 $('#alta_item').prop( 'disabled', false );	

}); 

			  </script>";	
			  
			 
	  
	   break;	


      case 'PluginProcedimientosLink' :
				   
		 echo "<script type='text/javascript'>
				 $('#dropdown_plugin_procedimientos_links_id".$addrand."').change(function(){  if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  
		       </script>";	
	  break;
	  
    

      case 'PluginProcedimientosSalto' :				   
				   
		 echo "<script type='text/javascript'>
		 
		 $('#comentario_salto').on('input change keyup', function () {
				if (this.value.length) {
					$('#alta_item').prop( 'disabled', false );
				} else {
					$('#alta_item').prop( 'disabled', true );
				}
		  });
		 
				 $('#dropdown_goto_id".$addrand."').change(function(){  if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  
		       </script>";	
	  break;
	  	
      case 'PluginProcedimientosProcedimiento' :
				   
		 echo "<script type='text/javascript'>
				 $('#dropdown_plugin_procedimientos_procedimientos_id".$addrand."').change(function(){  if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  
		       </script>";	
	  break;
	  			
		  
					  }
			
   } else {
      // For table w3c
      echo "<td>&nbsp;</td>";
   }
   echo "</tr></table>";
}
?>