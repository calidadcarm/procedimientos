<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */

include ('../../../inc/includes.php');


Html::header(PluginProcedimientosProcedimiento::getTypeName(2), '', "plugins","procedimiento");

$condicion= new PluginProcedimientosCondicion();
$condicion->checkGlobal(READ);

if ($condicion->canView()) {
  Search::show("PluginProcedimientosCondicion");
} else {   
   Html::displayRightError();
}
Html::footer();

?>