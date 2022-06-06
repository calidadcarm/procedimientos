<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Noviembre 2016

   ----------------------------------------------------------
 */

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

include_once (GLPI_ROOT . "/plugins/procedimientos/inc/function.procedimientos.php");

Session::checkRight("plugin_procedimientos", CREATE);

if (isset($_POST['typeaccion']) && !empty($_POST['typeaccion'])
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
   switch ($_POST['typeaccion']) {
      case '1' : // Tarea
         echo "<td width='80%'>";
         $params = array('right'  => $_POST['right'],
						 'rand' => $rand, 
						 'addicon' => true,
                         'name' => $prefix.'plugin_procedimientos_accions_id'.$suffix,
						 'condition' => ['plugin_procedimientos_tipoaccions_id' => 1]);
						 //'condition' => '`plugin_procedimientos_tipoaccions_id` = 1');
		 PluginProcedimientosAccion::dropdownAcciones('PluginProcedimientosAccion', $params);		 
         echo "<script type='text/javascript'> $('#dropdown_plugin_procedimientos_accions_id".$rand."').change(function(){ if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  </script>";
		 echo "</td>";
		 
		 
		 
         $display = true;
         break;

      case '2' :	// Escalado
         echo "<td width='80%'>";
         $params = array('right'  => $_POST['right'],
						 'rand' => $rand, 
						 'addicon' => true,
                         'name' => $prefix.'plugin_procedimientos_accions_id'.$suffix,
						 'condition' => ['plugin_procedimientos_tipoaccions_id' => 2]);
						 //'condition' => '`plugin_procedimientos_tipoaccions_id` =2');
		 PluginProcedimientosAccion::dropdownAcciones('PluginProcedimientosAccion', $params);
		 echo "<script type='text/javascript'> $('#dropdown_plugin_procedimientos_accions_id".$rand."').change(function(){ if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  </script>";
         echo "</td>";
         $display = true;
         break;

      case '3' : // Modificación Ticket
		 
         echo "<td width='80%'>";
         $params = array('right'  => $_POST['right'],
						 'rand' => $rand, 
						 'addicon' => true,
                         'name' => $prefix.'plugin_procedimientos_accions_id'.$suffix,
						 'condition' => ['plugin_procedimientos_tipoaccions_id' => 3]);
						 //'condition' => '`plugin_procedimientos_tipoaccions_id` = 3');
		 PluginProcedimientosAccion::dropdownAcciones('PluginProcedimientosAccion', $params);
		 echo "<script type='text/javascript'> $('#dropdown_plugin_procedimientos_accions_id".$rand."').change(function(){ if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  </script>";
         echo "</td>";
         $display = true;
         break;
		 
      case '4' : // Seguimiento
		 
         echo "<td width='80%'>";
         $params = array('right'  => $_POST['right'],
						 'rand' => $rand, 
						 'addicon' => true,
                         'name' => $prefix.'plugin_procedimientos_accions_id'.$suffix,
						 'condition' => ['plugin_procedimientos_tipoaccions_id' => 4]);
						// 'condition' => '`plugin_procedimientos_tipoaccions_id` = 4');
		 PluginProcedimientosAccion::dropdownAcciones('PluginProcedimientosAccion', $params);
		 echo "<script type='text/javascript'> $('#dropdown_plugin_procedimientos_accions_id".$rand."').change(function(){ if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  </script>";
         echo "</td>";
         $display = true;
         break;
		 
      case '5' : // Validación
         echo "<td width='80%'> ";
         $params = array('right'  => $_POST['right'],
						 'rand' => $rand, 
						 'addicon' => true,
                         'name' => $prefix.'plugin_procedimientos_accions_id'.$suffix,
						 'condition' => ['plugin_procedimientos_tipoaccions_id' => 5]);
						// 'condition' => '`plugin_procedimientos_tipoaccions_id` = 5');
		 PluginProcedimientosAccion::dropdownAcciones('PluginProcedimientosAccion', $params);
		 echo "<script type='text/javascript'> $('#dropdown_plugin_procedimientos_accions_id".$rand."').change(function(){ if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  </script>";
         echo "</td>";
         $display = true;
         break;

         // [INICIO] [CRI] - JMZ18G - 06/05/2022 Añadir accion Eliminar Técnicos
         case '6' : // Eliminar Técnicos
            echo "<td width='80%'> ";
            $params = array('right'  => $_POST['right'],
                      'rand' => $rand, 
                      'addicon' => true,                      
                      'name' => $prefix.'plugin_procedimientos_accions_id'.$suffix,
                      'condition' => ['plugin_procedimientos_tipoaccions_id' => 6]);
                     // 'condition' => '`plugin_procedimientos_tipoaccions_id` = 6');
          PluginProcedimientosAccion::dropdownAcciones('PluginProcedimientosAccion', $params);
          echo "<script type='text/javascript'> $('#dropdown_plugin_procedimientos_accions_id".$rand."').change(function(){ if ($(this).val()>0) {  $('#alta_item').prop( 'disabled', false ); } else { $('#alta_item').prop( 'disabled', true );   }  });  </script>";
            echo "</td>";
            $display = true;
            break;         
         // [FINAL] [CRI] - JMZ18G - 06/05/2022 Añadir accion Eliminar Técnicos
   }
   echo "</tr></table>";
}
?>