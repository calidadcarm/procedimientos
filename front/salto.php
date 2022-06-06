<?php
/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.2.1
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Octubre 2016

   ----------------------------------------------------------
 */

include ('../../../inc/includes.php');


Html::header(PluginProcedimientosProcedimiento::getTypeName(2), '', "plugins","procedimiento");

$salto= new PluginProcedimientosSalto();
$salto->checkGlobal(READ);

if ($salto->canView()) {
  Search::show("PluginProcedimientosSalto");
} else {   
   Html::displayRightError();
}
Html::footer();

?>