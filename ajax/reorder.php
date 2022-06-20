<?php
include ("../../../inc/includes.php");

		global $DB;
		
		if ((isset($_POST['container_id'])) and (isset($_POST['old_order'])) and (isset($_POST['new_order'])) and (isset($_POST['orden'])) and (isset($_POST['orden_salto']))) {
		
$procedimiento=$_POST['container_id'];
$old_order=$_POST['old_order'];
$new_order=$_POST['new_order'];
$orden=$_POST['orden'];
$orden_salto=$_POST['orden_salto'];

$items = explode("|", $orden);
$items_salto = explode("|", $orden_salto);

foreach($items as $item) {
    
	if (!empty($item)){
	$id=explode(",", $item)[0];
	$linea=intval(explode(",", $item)[1]);
		
		$query= "UPDATE `glpi_plugin_procedimientos_procedimientos_items` SET `line`=".$linea." WHERE `id` =".$id.";";
		//echo $query."<br>";
		$result_clear = $DB->query($query);
	}
}

	$where = "";
	$resultado="";
		
	for ($i = 0; $i < (count($items_salto)-1); $i++) {
		
		if ($i==0) {
			$where = $where." a.id = ".$items_salto[$i]." "; 
		}else {
			$where = $where." or  a.id = ".$items_salto[$i]." ";			
		}						
	
	}
	
	if (!empty($where)){  
	
	 $query="SELECT a.id, a.itemtype, 
	 if (a.itemtype='PluginProcedimientosCondicion', b.comment, c.comment) as comment,   
	 if (a.itemtype='PluginProcedimientosCondicion', b.id_1, c.goto_id) as id_1,  
	 if (a.itemtype='PluginProcedimientosCondicion', b.tag_id_1, null) as tag_id_1,  
	 if (a.itemtype='PluginProcedimientosCondicion', b.id_2, null) as id_2,  
	 if (a.itemtype='PluginProcedimientosCondicion', b.tag_id_2, null) as tag_id_2,  	 
	 if (a.itemtype='PluginProcedimientosCondicion', b.id_3, null) as id_3,  	
	 if (a.itemtype='PluginProcedimientosCondicion', b.tag_id_3, null) as tag_id_3,  		 
	 if (a.itemtype='PluginProcedimientosCondicion', b.id_4, null) as id_4,  	
	 if (a.itemtype='PluginProcedimientosCondicion', b.tag_id_4, null) as tag_id_4,  		 
	 if (a.itemtype='PluginProcedimientosCondicion', b.id_5, null) as id_5,  	
	 if (a.itemtype='PluginProcedimientosCondicion', b.tag_id_5, null) as tag_id_5,
	 if (a.itemtype='PluginProcedimientosCondicion', b.id, c.id) as items_id  	 
	 FROM `glpi_plugin_procedimientos_procedimientos_items` a 
  
   left join glpi_plugin_procedimientos_condicions b on b.plugin_procedimientos_procedimientos_id=".$procedimiento." and a.items_id=b.id and a.itemtype='PluginProcedimientosCondicion'
	
   left join glpi_plugin_procedimientos_saltos c on c.plugin_procedimientos_procedimientos_id=".$procedimiento." and a.items_id=c.id and a.itemtype='PluginProcedimientosSalto'
	
	where (	".$where. ")";
	
    //echo $query;
   

   //               echo "alert('gfhgh1');";
			  
    if ($result = $DB->query($query)) {
         if ($DB->numrows($result)) {      
		
	while ($line = $DB->fetchassoc($result)) {   			

	$line_id_1=0;
	$line_id_2=0;
	$line_id_3=0;
	$line_id_4=0;
	$line_id_5=0;
			
	if ((isset($line["id_1"])) and ($line["id_1"]>0)){ $select1=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$procedimiento."' and id=".$line['id_1'].") as id_1 "; } else { $select1=""; }
	if ((isset($line["id_2"])) and ($line["id_2"]>0)){ $select2=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$procedimiento."' and id=".$line['id_2'].") as id_2 "; } else { $select2=""; }
	if ((isset($line["id_3"])) and ($line["id_3"]>0)){ $select3=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$procedimiento."' and id=".$line['id_3'].") as id_3 "; } else { $select3=""; }
	if ((isset($line["id_4"])) and ($line["id_4"]>0)){ $select4=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$procedimiento."' and id=".$line['id_4'].") as id_4 "; } else { $select4=""; }
	if ((isset($line["id_5"])) and ($line["id_5"]>0)){ $select5=",(select line from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$procedimiento."' and id=".$line['id_5'].") as id_5 "; } else { $select5=""; }
	
	if ((empty($select1)) and (empty($select2)) and (empty($select3)) and (empty($select4)) and (empty($select5))) {
		if ($line['itemtype']=='PluginProcedimientosCondicion'){
				$comment = "Error, La condición no tiene asociada ninguna línea";
		} else {
			    $comment = "Error, El salto no tiene asociada ninguna línea";
		}
				$resultado=$resultado.'$("#commentario_'.$line["id"].'").html("<font color=\"red\">'.$comment.'</font>");';
				
	} else {			
	
	$query_linea="select id ".$select1.$select2.$select3.$select4.$select5." from glpi_plugin_procedimientos_procedimientos_items where plugin_procedimientos_procedimientos_id='".$procedimiento."' limit 1";  
		
		//echo $query_linea."<br><br><br>";
		
	$result_linea=$DB->query($query_linea);	
	
	$lineas = $DB->fetchassoc($result_linea);	
	
	$comment="<div class=\'condiciones\'>";
	
	if ($line['itemtype']=='PluginProcedimientosCondicion'){
		
	if ((isset($lineas["id_1"])) and ($lineas["id_1"]>0) and (isset($line["tag_id_1"])) and (!empty($line["tag_id_1"]))){ 
	
	$comment=$comment."Respuesta <strong>".$line['tag_id_1']."</strong>, ir a #linea: <strong><span id=\'linea_".$line['id_1']."\'>".$lineas['id_1']."</span></strong><BR>";  
	$line_id_1 = $lineas['id_1'];
	
	} else {

	if ((isset($line["tag_id_1"])) and (!empty($line["tag_id_1"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 1 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_1"])) and ($lineas["id_1"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 1 carece de TAG</font>, ir a #linea: <strong><span id=\'linea_".$line['id_1']."\'>".$lineas['id_1']."</span></strong><BR>";
	$line_id_1 = $lineas['id_1'];

	} } }
	
	if ((isset($lineas["id_2"])) and ($lineas["id_2"]>0) and (isset($line["tag_id_2"])) and (!empty($line["tag_id_2"]))){ 
	
	$comment=$comment."Respuesta <strong>".$line['tag_id_2']."</strong>, ir a #linea: <strong><span id=\'linea_".$line['id_2']."\'>".$lineas['id_2']."</span></strong><BR>";  
	$line_id_2 = $lineas['id_2'];
	
	} else {

	if ((isset($line["tag_id_2"])) and (!empty($line["tag_id_2"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 2 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_2"])) and ($lineas["id_2"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 2 carece de TAG</font>, ir a #linea: <strong><span id=\'linea_".$line['id_2']."\'>".$lineas['id_2']."</span></strong><BR>";
	$line_id_2 = $lineas['id_2'];

	} } }	
	
	if ((isset($lineas["id_3"])) and ($lineas["id_3"]>0) and (isset($line["tag_id_3"])) and (!empty($line["tag_id_3"]))){ 
	
	$comment=$comment."Respuesta <strong>".$line['tag_id_3']."</strong>, ir a #linea: <strong><span id=\'linea_".$line['id_3']."\'>".$lineas['id_3']."</span></strong><BR>";  
	$line_id_3 = $lineas['id_3'];
	
	} else {

	if ((isset($line["tag_id_3"])) and (!empty($line["tag_id_3"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 3 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_3"])) and ($lineas["id_3"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 3 carece de TAG</font>, ir a #linea: <strong><span id=\'linea_".$line['id_3']."\'>".$lineas['id_3']."</span></strong><BR>";
	$line_id_3 = $lineas['id_3'];

	} } }	
	
	if ((isset($lineas["id_4"])) and ($lineas["id_4"]>0) and (isset($line["tag_id_4"])) and (!empty($line["tag_id_4"]))){ 
	
	$comment=$comment."Respuesta <strong>".$line['tag_id_4']."</strong>, ir a #linea: <strong><span id=\'linea_".$line['id_4']."\'>".$lineas['id_4']."</span></strong><BR>";  
	$line_id_4 = $lineas['id_4'];
	
	} else {

	if ((isset($line["tag_id_4"])) and (!empty($line["tag_id_4"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 4 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_4"])) and ($lineas["id_4"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 4 carece de TAG</font>, ir a #linea: <strong><span id=\'linea_".$line['id_4']."\'>".$lineas['id_4']."</span></strong><BR>";
	$line_id_4 = $lineas['id_4'];

	} } }	
	
	if ((isset($lineas["id_5"])) and ($lineas["id_5"]>0) and (isset($line["tag_id_5"])) and (!empty($line["tag_id_5"]))){ 
	
	$comment=$comment."Respuesta <strong>".$line['tag_id_5']."</strong>, ir a #linea: <strong><span id=\'linea_".$line['id_5']."\'>".$lineas['id_5']."</span></strong><BR>";  
	$line_id_5 = $lineas['id_5'];
	
	} else {

	if ((isset($line["tag_id_5"])) and (!empty($line["tag_id_5"]))){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 5 no tiene asociada ninguna línea</font><BR>";

	} else {

	if ((isset($lineas["id_5"])) and ($lineas["id_5"]>0)){ 

	$comment=$comment. "<font color=\'red\'>Error, La condición 5 carece de TAG</font>, ir a #linea: <strong><span id=\'linea_".$line['id_5']."\'>".$lineas['id_5']."</span></strong><BR>";
	$line_id_5 = $lineas['id_5'];

	} } }	

	}	
	
	
	if ($line['itemtype']=='PluginProcedimientosSalto'){

	if ((isset($lineas["id_1"])) and ($lineas["id_1"]>0)){ 
	
	$comment=$comment."Ir a #linea: <strong><span id=\'linea_".$line['id_1']."\'>".$lineas['id_1']."</span></strong><BR>";
	$line_id_1 = $lineas['id_1'];
	} else {
	
	$comment=$comment."<font color=\'red\'>Error, El salto no tiene asociado ninguna línea</font>";
	
	}	

	}	
	
	$comment=$comment."</div>";
	
	if ($line['itemtype']=='PluginProcedimientosCondicion'){
	
				$update = "Update `glpi_plugin_procedimientos_condicions` 
			SET `comment`='".$comment."',
				`id_1`='".$line['id_1']."',
				`id_2`='".$line['id_2']."',
				`id_3`='".$line['id_3']."',				
				`id_4`='".$line['id_4']."',							
				`id_5`='".$line['id_5']."',
				`line_id_1`='".$line_id_1."',
				`line_id_2`='".$line_id_2."',
				`line_id_3`='".$line_id_3."',
				`line_id_4`='".$line_id_4."',
				`line_id_5`='".$line_id_5."'
			WHERE `id`='".$line['items_id']."';";			
			echo $update."<br><br><br><br>";
			$DB->query($update);	
			
			$resultado=$resultado.'$("#commentario_'.$line["id"].'").html("<font color=\"blue\">'.$comment.'</font>");';
			
	}
	
	if ($line['itemtype']=='PluginProcedimientosSalto'){
	
				$update = "Update `glpi_plugin_procedimientos_saltos` 
			SET `comment`='".$comment."',
				`goto`='".$line_id_1."',
				`goto_id`='".$line['id_1']."'
			WHERE `id`='".$line['items_id']."';";			
			//echo $update;
			$DB->query($update);	
			
			$resultado=$resultado.'$("#commentario_'.$line["id"].'").html("<font color=\"blue\">'.$comment.'</font>");';
			
	}	
	
	}
	
	}
		 }

	  }
	//  echo $resultado;;
	  echo "<script>";		
	 echo $resultado;
	  echo "</script>";

	}
	
		} else {  
		
		 die("Sorry. You can't access directly to this file");
		
		}