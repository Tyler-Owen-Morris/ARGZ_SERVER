<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$mission_id = isset($_POST['mission_id']) ? protect($_POST['mission_id']) : '';

if ($id <> '') {
    if ($mission_id <> '') {
        //load in the mission data to be executed
        $mission_query = mysql_query("SELECT * FROM missions_table WHERE mission_id='$mission_id' AND owner_id='$id'") or die(mysql_error());

        if (mysql_num_rows($mission_query) > 0) {
            $row = mysql_fetch_assoc($mission_query);
            
            //survivor1 updates
            $survivor1_id = $row['survivor1_id'];
            $survivor1_curr_stam = $row['survivor1_curr_stam'];
            $survivor1_dead = $row['survivor1_dead'];

            if($survivor1_dead == 1) {
                //kill this survivor and move on
                $surv1_delete = mysql_query("DELETE FROM survivor_roster WHERE entry_id='$survivor1_id' AND owner_id='$id'") or die(mysql_error());
            } else {
                //update the survivor record with the new current stamina
                if ($survivor1_curr_stam <= 0) {
                    $survivor1_curr_stam = 0;
                }
                $surv1_update = mysql_query("UPDATE survivor_roster SET curr_stam='$survivor1_curr_stam', onMission=0 WHERE owner_id='$id' AND entry_id='$survivor1_id'") or die(mysql_error());
            }
            //survivor2 updates
            $survivor2_id = $row['survivor2_id'];
            $survivor2_curr_stam = $row['survivor2_curr_stam'];
            $survivor2_dead = $row['survivor2_dead'];

            if($survivor2_dead == 1) {
                //kill this survivor and move on
                $surv2_delete = mysql_query("DELETE FROM survivor_roster WHERE entry_id='$survivor2_id' AND owner_id='$id'") or die(mysql_error());
            } else {
                //update the survivor record with the new current stamina
                if ($survivor2_curr_stam <= 0) {
                    $survivor2_curr_stam = 0;
                }
                $surv2_update = mysql_query("UPDATE survivor_roster SET curr_stam='$survivor2_curr_stam', onMission=0 WHERE owner_id='$id' AND entry_id='$survivor2_id'") or die(mysql_error());
            }
            //survivor3 updates
            $survivor3_id = $row['survivor3_id'];
            $survivor3_curr_stam = $row['survivor3_curr_stam'];
            $survivor3_dead = $row['survivor3_dead'];

            if($survivor3_dead == 1) {
                //kill this survivor and move on
                $surv3_delete = mysql_query("DELETE FROM survivor_roster WHERE entry_id='$survivor3_id' AND owner_id='$id'") or die(mysql_error());
            } else {
                //update the survivor record with the new current stamina
                if ($survivor3_curr_stam <= 0) {
                    $survivor3_curr_stam = 0;
                }
                $surv3_update = mysql_query("UPDATE survivor_roster SET curr_stam='$survivor3_curr_stam', onMission=0 WHERE owner_id='$id' AND entry_id='$survivor3_id'") or die(mysql_error());
            }
            //survivor4 updates
            $survivor4_id = $row['survivor4_id'];
            $survivor4_curr_stam = $row['survivor4_curr_stam'];
            $survivor4_dead = $row['survivor4_dead'];

            if($survivor4_dead == 1) {
                //kill this survivor and move on
                $surv4_delete = mysql_query("DELETE FROM survivor_roster WHERE entry_id='$survivor4_id' AND owner_id='$id'") or die(mysql_error());
            } else {
                //update the survivor record with the new current stamina
                if ($survivor4_curr_stam <= 0) {
                    $survivor4_curr_stam = 0;
                }
                $surv4_update = mysql_query("UPDATE survivor_roster SET curr_stam='$survivor4_curr_stam', onMission=0 WHERE owner_id='$id' AND entry_id='$survivor4_id'") or die(mysql_error());
            }
            //survivor5 updates
            $survivor5_id = $row['survivor5_id'];
            $survivor5_curr_stam = $row['survivor5_curr_stam'];
            $survivor5_dead = $row['survivor5_dead'];

            if($survivor5_dead == 1) {
                //kill this survivor and move on
                $surv5_delete = mysql_query("DELETE FROM survivor_roster WHERE entry_id='$survivor5_id' AND owner_id='$id'") or die(mysql_error());
            } else {
                //update the survivor record with the new current stamina
                if ($survivor5_curr_stam <= 0) {
                    $survivor5_curr_stam = 0;
                }
                $surv5_update = mysql_query("UPDATE survivor_roster SET curr_stam='$survivor5_curr_stam', onMission=0 WHERE owner_id='$id' AND entry_id='$survivor5_id'") or die(mysql_error());
            }

            //player stat updates
            $supply = $row['supply_found'];
            $water = $row['water_found'];
            $food = $row['food_found'];
            $ammo_used = $row['ammo_used'];

            $player_update = mysql_query("UPDATE player_sheet SET supply=supply+$supply, water=water+$water, food=food+$food, ammo=ammo-$ammo_used WHERE id='$id'")or die(mysql_error());

            //remove the mission from the table
            $mission_delete = mysql_query("DELETE FROM missions_table WHERE mission_id='$mission_id'") or die(mysql_error());

            if (mysql_affected_rows() > 0) {
                array_push($return_array, "Success");
                array_push($return_array, "Mission updated to player data and removed");
            } else {
                array_push($return_array, "Failed");
                array_push($return_array, "mission failed to erase");
            }
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "could not locate the mission on the table");
        }
    }else{
        array_push($return_array, "Failed");
        array_push($return_array, "mission id not set");
    }
}else{
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;
?>