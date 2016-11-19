<?php
//LootByType.php
include("db_connect.php");
$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
$type = isset($_POST['type']) ? protect($_POST['type']) : '';
$qty = isset($_POST['qty']) ? protect($_POST['qty']) : '';


if ($id <> ''){
    if ($bldg_name<>''||$bldg_id<>''||$type<>'') {
		if ($type == "supply"){
			$player_update = $mysqli->query("UPDATE player_sheet SET supply=supply+$qty WHERE id='$id'") or die($mysqli->error());
			if ($player_update->affected_rows >0){
				$bldg_update = $mysqli->query("UPDATE cleared_buildings SET last_looted_supply=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die($mysqli->error());
				if ($bldg_update->affected_rows >0){
					array_push($return_array, "Success");
					array_push($return_array, "Player and Building records updated successfully-- supply");
				}else {
					array_push($return_array, "Failed");
					array_push($return_array, "unable to update building record. Player updated with supply though");
				}
			} else {
				array_push($return_array, "Failed");
				array_push($return_array, "unabled to update player sheet");
			}
		}else if ($type=="food"){
			$player_update = $mysqli->query("UPDATE player_sheet SET food=food+$qty WHERE id='$id'") or die($mysqli->error());
			if ($player_update->affected_row >0){
				$bldg_update = $mysqli->query("UPDATE cleared_buildings SET last_looted_food=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die($mysqli->error());
				if ($bldg_update->affected_rows >0){
					array_push($return_array, "Success");
					array_push($return_array, "Player and Building records updated successfully-- food");
				}else {
					array_push($return_array, "Failed");
					array_push($return_array, "unable to update building record. Player updated with food though");
				}
			} else {
				array_push($return_array, "Failed");
				array_push($return_array, "unabled to update player sheet");
			}
		}elseif($type=="water"){
			$player_update = $mysqli->query("UPDATE player_sheet SET water=water+$qty WHERE id='$id'") or die($mysqli->error());
			if ($player_update->affected_rows >0){
				$bldg_update = $mysqli->query("UPDATE cleared_buildings SET last_looted_water=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die($mysqli->error());
				if ($bldg_update->affected_rows >0){
					array_push($return_array, "Success");
					array_push($return_array, "Player and Building records updated successfully");
				}else {
					array_push($return_array, "Failed");
					array_push($return_array, "unable to update building record. Player updated with water though");
				}
			} else {
				array_push($return_array, "Failed");
				array_push($return_array, "unabled to update player sheet");
			}
		}else{
			array_push($return_array, "Failed");
			array_push($return_array, "no matching type found");
		}
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
//LootByType.php
?>