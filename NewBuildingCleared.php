<?php 
include("db_connect.php");

?> 
<?php
$return_array = array();
if(isset($_POST['id'])){
    $id = protect($_POST['id']);
    if(isset($_POST['bldg_name'])){
        $bldg_name = protect($_POST['bldg_name']);
        $bldg_id = protect($_POST['bldg_id']);

        $supply_earned = protect($_POST['supply']);
        $water_earned = protect($_POST['water']);
        $food_earned = protect($_POST['food']);
        $survivor_found = protect($_POST['survivor_found']);
        
        //This is a lookup to find matching currently clear entries
        $query1 = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_id ='$bldg_id' AND active=0")or die(mysql_error());
            
        if(mysql_num_rows($query1) > 0) {
            //if there is an entry matching user and building, that is already in a deactivated state
            array_push($return_array, "Failed");
            array_push($return_array, "building is already cleared");
            
        } else {
            //this finds the active entry to be deactivated in the update
            $query2 = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_id ='$bldg_id' AND bldg_name ='$bldg_name' AND active=1")or die(mysql_error());
            
            if(mysql_num_rows($query2) > 0 && mysql_num_rows($query2) < 2) {
                //if there's already an entry for this player that has become "active" by the chron script, deactivate it.
                $update1 = mysql_query("UPDATE cleared_buildings SET active=0, time_cleared=NOW() WHERE id='$id' AND bldg_id='$bldg_id'");
                array_push($return_array, "Success");
                array_push($return_array, "Building successfully added to database");
            } else {
                //otherwise- create an entire new user based on the post data.
                $insert1 = mysql_query("INSERT INTO cleared_buildings (id, bldg_name, bldg_id, active, time_cleared) VALUES ('$id', '$bldg_name', '$bldg_id', 0, NOW())")or die(mysql_error());
                array_push($return_array, "Success");
                array_push($return_array, "Building successfully added to database");
            }
        }

        //update the player_sheet with the new inventory numbers
        $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id' LIMIT 1") or die(mysql_error());
        $player_data = mysql_fetch_assoc($player_query);
        $new_supply = $player_data['supply'] + $supply_earned;
        $new_food = $player_data['food'] + $food_earned;
        $new_water = $player_data['water'] + $water_earned;

        $update2 = mysql_query("UPDATE player_sheet SET supply=$new_supply, food=$new_food, water=$new_water WHERE id = '$id'") or die(mysql_error());

        //if there was a survivor found, pull randomly from the static table, and create the survivor on the players survivor_sheet
        if ($survivor_found == 1) {
            $static_survivor_query = mysql_query("SELECT * FROM static_survivors ORDER BY RAND() LIMIT 1") or die(mysql_error());
            $survivor_row = mysql_fetch_assoc($static_survivor_query);
            $survivor_name = $survivor_row['name'];
            $survivor_stam = $survivor_row['base_stam'];
            $survivor_attack = $survivor_row['base_attack'];
            
            //get the lowest survivor position # from the player
            $survivor_pos_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' ORDER BY team_position ASC LIMIT 1") or die(mysql_error());
            $survivor_pos_row = mysql_fetch_assoc($survivor_pos_query);
            $lowest_team_pos = $survivor_pos_row['team_position'];
            $team_pos = 0;
            if ($lowest_team_pos-1 < 0) {
                $team_pos = 0;
            } else {
                $team_pos = $lowest_team_pos-1;
            }

            $insert_survivor = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, weapon_equipped, isActive, start_time, team_position) VALUES ('$id', '$survivor_name', '$survivor_stam', '$survivor_stam', '$survivor_attack', '0', 1, NOW(), '$team_pos')") or die(mysql_error());
            $inst_surv_row = mysql_fetch_assoc($insert_survivor);
            $new_survivor_array = array("entry_id"=>$inst_surv_row['entry_id'], "owner_id"=>$inst_surv_row['owner_id'], "name"=>$inst_surv_row['name'], "base_stam"=>$inst_surv_row['base_stam'], "curr_stam"=>$inst_surv_row['curr_stam'], "base_attack"=>$inst_surv_row['base_attack']);
            array_push($return_array, 1);
            array_push($return_array, $new_survivor_array);
        } else {
            array_push($return_array, 0);
        }
        $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
        echo $json_return;
    } else {
        echo "building name not passed";
    }
} else {
    echo "id not set";
}
// NewBuildingCleared.php
?>