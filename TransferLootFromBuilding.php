<?php
include("db_connect.php");
$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';

if ($id<>'' || $bldg_name<>'' || $bldg_id<>''){
    //find the building entry
	$bldg_query = $mysqli->query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_name='$bldg_name' LIMIT 1") or die($mysqli->error());
	
	if ($bldg_query->num_rows > 0) {
		//get the 
		$bldg_data = $bldg_query->fetch_assoc();
		$bldg_supply = $bldg_data['supply'];
		$bldg_food = $bldg_data['food'];
		$bldg_water = $bldg_data['water'];
		
		$player_update = $mysqli->query("UPDATE player_sheet SET supply=supply+$bldg_supply, food=food+$bldg_food, water=water+$bldg_water WHERE id='$id'") or die($mysqli->error());
		
		if ($mysqli->affected_rows>0){
			//set the building to empty
			$bldg_update = $mysqli->query("UPDATE cleared_buildings SET supply=0, food=0, water=0, last_looted=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die($mysqli->error());
			if ($mysqli->affected_rows >0){
				array_push($return_array, "Success");
				array_push($return_array, "successfully transfered all building contents to player");
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "Unable to update the buildling record to be empty");
			}
			
		} else{
			array_push($return_array, "Failed");
			array_push($return_array, "unable to update player record");
		}
		
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "unable to locate building record");
	}
	
} else {
    
    array_push($return_array, "Failed");
    array_push($return_array, "user id, bldg name, bldg ID not set");
}
array_push($return_array, $rec_unequips);
$json_return = json_encode($return_array);
echo $json_return;

// TransferLootFromBuilding.php
?>