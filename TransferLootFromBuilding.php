<?php
include("db_connect.php");
$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';

if ($id<>'' || $bldg_name<>'' || $bldg_id<>''){
    //find the building entry
	$bldg_query = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_name='$bldg_name' LIMIT 1") or die(mysql_error());
	
	if (mysql_num_rows($bldg_query) > 0) {
		//get the 
		$bldg_data = mysql_fetch_assoc($bldg_query);
		$bldg_supply = $bldg_data['supply'];
		$bldg_food = $bldg_data['food'];
		$bldg_water = $bldg_data['water'];
		
		$player_update = mysql_query("UPDATE player_sheet SET supply=supply+$bldg_supply, food=food+$bldg_food, water=water+$bldg_water WHERE id='$id'") or die(mysql_error());
		
		if (mysql_affected_rows()>0){
			//set the building to empty
			$bldg_update = mysql_query("UPDATE cleared_buildings SET supply=0, food=0, water=0, last_looted=NOW() WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
			if (mysql_affected_rows()>0){
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