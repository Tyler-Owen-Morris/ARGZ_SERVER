<?php
//PlaceGear.php
include("db_connect.php");
$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
$type = isset($_POST['type']) ? protect($_POST['type']) : '';


if ($id <> ''){
    if ($bldg_name<>''||$bldg_id<>''||$type<>'') {
		if ($type == "trap"){
			$bldg_update = mysql_query("UPDATE cleared_buildings SET has_trap=1, last_looted_supply=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
			if (mysql_affected_rows()>0){
				$player_update = mysql_query("UPDATE player_sheet SET trap=trap-1 WHERE id='$id' AND trap>0") or die(mysql_error());
				if (mysql_affected_rows()>0){
					array_push($return_array, "Success");
					array_push($return_array, "player placed a zombie trap successfully");
				}else{
					array_push($return_array, "Failed");
					array_push($return_array, "failed to update player record");
				}
			}
		}else if ($type=="barrel"){
			$bldg_update = mysql_query("UPDATE cleared_buildings SET has_barrel=1, last_looted_water=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
			if (mysql_affected_rows()>0){
				$player_update = mysql_query("UPDATE player_sheet SET barrel=barrel-1 WHERE id='$id' AND barrel>0") or die(mysql_error());
				if (mysql_affected_rows()>0){
					array_push($return_array, "Success");
					array_push($return_array, "player placed a rain barrel successully");
				}else{
					array_push($return_array, "Failed");
					array_push($return_array, "failed to update player record");
				}
			}
		}elseif($type=="greenhouse"){
			$bldg_update = mysql_query("UPDATE cleared_buildings SET has_greenhouse=1, last_looted_food=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
			if (mysql_affected_rows()>0){
				$player_update = mysql_query("UPDATE player_sheet SET greenhouse=greenhouse-1 WHERE id='$id' AND greenhouse>0") or die(mysql_error());
				if (mysql_affected_rows()>0){
					array_push($return_array, "Success");
					array_push($return_array, "player successfully placed a greenhouse");
				}else{
					array_push($return_array, "Failed");
					array_push($return_array, "failed to update player record");
				}
			}
		}else{
			array_push($return_array, "Failed");
			array_push($return_array, "no matching type found");
		}
		$final_query = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id'") or die(mysql_error());
		$final_array = mysql_fetch_assoc($final_query);
		array_push($return_array, $final_array);
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "incomplete send");
	}
    
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}
$jsonreturn = json_encode($return_array);
echo $jsonreturn;
//PlaceGear.php
?>