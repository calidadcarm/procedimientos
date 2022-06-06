<?php
include ('../../../inc/includes.php');

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('procedimientos') || !$plugin->isActivated('procedimientos')) {
   Html::displayNotFoundError();
}

$procedimiento = new PluginProcedimientosProcedimiento;
$export_array = ['procedimiento' => []];
foreach ($_GET['plugin_procedimientos_procedimientos_id'] as $id) {
    //echo $procedimiento->getTypeName($id);
   $procedimiento->getFromDB($id);
   $export_array['procedimiento'][] = $procedimiento->export();
}

$export_json = json_encode($export_array, JSON_UNESCAPED_UNICODE
                                        | JSON_UNESCAPED_SLASHES
                                        | JSON_NUMERIC_CHECK
                                        | ($_SESSION['glpi_use_mode'] == Session::DEBUG_MODE
                                             ? JSON_PRETTY_PRINT
                                             : 0));

header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
header('Pragma: private');
header('Cache-control: private, must-revalidate');
header("Content-disposition: attachment; filename=\"export_procedimientos_".date("Ymd_Hi").".json\"");
header("Content-type: application/json");

echo $export_json;