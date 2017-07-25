<?php
include("db_connect.php");

$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
//$supply_earned = isset($_POST['supply']) ? protect($_POST['supply']) : '';
//$water_earned = isset($_POST['water']) ? protect($_POST['water']) : '';
//$food_earned = isset($_POST['food']) ? protect($_POST['food']) : '';
$survivor_found = isset($_POST['survivor_found']) ? protect($_POST['survivor_found']) : '';

$gear_string = "";

if ($id <> '') {
    if ($bldg_id <> '') {
		//initilize the resources to be transfered
		$bldg_supply = 0;
		$bldg_food = 0;
		$bldg_water = 0;
		
        //find the matching building entry
        $existing_query = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_id='$bldg_id'") or die(mysql_error());

        if (mysql_num_rows($existing_query) > 0) {
            //store the supplies and loot timer
            $bldg_data = mysql_fetch_assoc($existing_query);
			$supply_last_looted = $bldg_data['last_looted_supply'];
            $bldg_wood = $bldg_data['wood'];
			$bldg_metal = $bldg_data['metal'];
            $bldg_food = $bldg_data['food'];
            $bldg_water = $bldg_data['water'];
			$has_trap = $bldg_data['has_trap'];
			$has_barrel = $bldg_data['has_barrel'];
			$has_greenhouse = $bldg_data['has_greenhouse'];
			
			//if there was gear in the building already- roll to destoy it.
			if ($has_trap=="1"){
				$odds=15;
				$roll = range(0, 100);
				if ($roll <= $odds){
					$has_trap=0;
					$gear_string+="trap destroyed:";
				}
			}
			if($has_barrel=="1"){
				$odds=15;
				$roll = range(0, 100);
				if ($roll <= $odds){
					$has_barrel=0;
					$gear_string+="barrel destroyed:";
				}
			}
			if($has_greenhouse=="1"){
				$odds=15;
				$roll = range(0, 100);
				if ($roll <= $odds){
					$has_greenhouse=0;
					$gear_string+="greenhouse destroyed:";
				}
			}
			
			//update the building entry
            $bldg_update = mysql_query("UPDATE cleared_buildings SET active=0, time_cleared=NOW(), food=0, water=0, wood=0, metal=0, has_trap='$has_trap', has_barrel='$has_barrel', has_greenhouse='$has_greenhouse', zombies=-1 WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
			//resources are set to 0 for all clears. fabricated resources are calculated on the fly.
			
			if (mysql_affected_rows()>0){
				
				//if the building has never been looted before- auto-transfer resources.
				$unlooted_time = "2000-01-01 00:01:00"; //new entries store this as their un-looted time
				$loot_string = "";
				if ($supply_last_looted==$unlooted_time){
					$player_update = mysql_query("UPDATE player_sheet SET wood=wood+$bldg_wood, metal=metal+$bldg_metal, food=food+$bldg_food, water=water+$bldg_water WHERE id='$id'") or die(mysql_error());
					$bldg_update2 = mysql_query("UPDATE cleared_buildings SET last_looted_supply=NOW(), last_looted_food=NOW(), last_looted_water=NOW() WHERE id='$id' AND bldg_name='$bldg_name'")or die(mysql_error());
					if (mysql_affected_rows()>0){
						$loot_string = "";
					} else {
						array_push($return_array, "Failed");
						array_push($return_array, "unable to update the loot timer");
						die(json_encode($return_array, JSON_NUMERIC_CHECK));
					}
					
					if (mysql_affected_rows()>0){
						$loot_string ="Player has been awarded all building resources";
					}else{
						array_push($return_array, "Failed");
						array_push($return_array, "Unable to update the player resources");
						die(json_encode($return_array, JSON_NUMERIC_CHECK));
					}
				} else {
					$loot_string = "Player has looted this building previously, auto-loot ignored";
				}
				array_push($return_array, "Success");
				array_push($return_array, $loot_string);
				
			}else{
				array_push($return_array, "Failed");
            	array_push($return_array, "unable to update the building entry");
            	die(json_encode($return_array, JSON_NUMERIC_CHECK));
			}
			
            
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "No pre-existing entry found... so you didnt enter a building that you're now exiting?");
            die(json_encode($return_array, JSON_NUMERIC_CHECK));
        }

		/*
        //update the player sheet with the added stats
        $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id' LIMIT 1") or die(mysql_error());
        $player_data = mysql_fetch_assoc($player_query);
        $new_supply = $player_data['supply'] + $supply_earned;
        $new_food = $player_data['food'] + $food_earned;
        $new_water = $player_data['water'] + $water_earned;

        $update2 = mysql_query("UPDATE player_sheet SET supply=$new_supply, food=$new_food, water=$new_water WHERE id = '$id'") or die(mysql_error());
        
        if (mysql_affected_rows()) {
            
        } else  {
            array_push($return_array, "Failed");
            array_push($return_array, "Unable to update player sheet with new resources");
            die(json_encode($return_array, JSON_NUMERIC_CHECK));
        }
		*/

        //if there's a survivor found- then create them on the table
        if ($survivor_found > 0) {
			
		$new_survivor_array = array();
		for ( $i=0; $i < $survivor_found; $i++){
			//pull data from a radom static survivor
			$static_survivor_query = mysql_query("SELECT * FROM static_survivors ORDER BY RAND() LIMIT 1") or die(mysql_error());
			$survivor_row = mysql_fetch_assoc($static_survivor_query);
			$survivor_name = $survivor_row['name'];
			$survivor_stam = $survivor_row['base_stam'];
			$survivor_attack = $survivor_row['base_attack'];
			$survivor_pic_url = $survivor_row['profile_pic_url'];

			//find the lowest team position survivor, or use 0 - "not on team" 
			$survivor_pos_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' ORDER BY team_position ASC LIMIT 1") or die(mysql_error());
			$survivor_pos_row = mysql_fetch_assoc($survivor_pos_query);
			$lowest_team_pos = $survivor_pos_row['team_position'];
			$team_pos = 0;
			if ($lowest_team_pos-1 <= 0) {
				$team_pos = 0;
			} else {
				$team_pos = $lowest_team_pos-1;
			}

			//create the new survivor record
			$insert_survivor = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, weapon_equipped, isActive, start_time, team_position, profile_pic_url) VALUES ('$id', '$survivor_name', '$survivor_stam', '$survivor_stam', '$survivor_attack', '0', 1, NOW(), '$team_pos', '$survivor_pic_url')") or die(mysql_error());
			//construct the array of the survivor data to add to the return.
			$insert_id=mysql_insert_id();
			$query_survivor = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' AND entry_id='$insert_id' LIMIT 1") or die(mysql_error());
			$new_survivor_data = mysql_fetch_assoc($query_survivor);
			array_push($new_survivor_array, $new_survivor_data);
		}			
			
            array_push($return_array, $survivor_found);
            array_push($return_array, $new_survivor_array);
        } else {
            array_push($return_array, 0);
			array_push($return_array, "");
        }
		array_push($return_array, $gear_string);
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "Building ID not set");
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "player ID not set");
}
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;
//NewBuildingCleared1.php
?>