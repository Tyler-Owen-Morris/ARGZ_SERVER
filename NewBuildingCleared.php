<?php
include("db_connect.php");

$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
$supply_earned = isset($_POST['supply']) ? protect($_POST['supply']) : '';
$water_earned = isset($_POST['water']) ? protect($_POST['water']) : '';
$food_earned = isset($_POST['food']) ? protect($_POST['food']) : '';
$survivor_found = isset($_POST['survivor_found']) ? protect($_POST['survivor_found']) : '';

if ($id <> '') {
    if ($bldg_id <> '') {
        //find the matching building entry
        $existing_query = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());

        if (mysql_num_rows($existing_query) > 0) {
            //update the old query
            $bldg_data = mysql_fetch_assoc($existing_query);
            $bldg_supply = $bldg_data['supply'] - $supply_earned;
            $bldg_food = $bldg_data['food'] - $food_earned;
            $bldg_water = $bldg_data['water'] - $water_earned;

            $bldg_update = mysql_query("UPDATE cleared_buildings SET active=0, time_cleared=NOW(), supply=$bldg_supply, food=$bldg_food, water=$bldg_water, zombies=-1 WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
            array_push($return_array, "Success");
            array_push($return_array, "Building entry updated on the server");
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "No pre-existing entry found... so you didnt enter a building that you're now exiting?");
            die(json_encode($return_array, JSON_NUMERIC_CHECK));
        }

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

        //if there's a survivor found- then create them on the table
        if ($survivor_found == 1) {
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
            $new_survivor_array = array("entry_id"=>$insert_id, "owner_id"=>$id, "name"=>$survivor_name, "base_stam"=>$survivor_stam, "base_attack"=>$survivor_attack);
            array_push($return_array, 1);
            array_push($return_array, $new_survivor_array);
        } else {
            array_push($return_array, 0);
        }
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