<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';

if ($id <> '') {
    //player data
    $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
    $player = mysql_fetch_assoc($player_query);
    $player_data_array = array("first_name"=>$player['first_name'], "last_name"=>$player['last_name'], "char_created_DateTime" => $player['char_created_DateTime'], "homebase_lat" => $player['homebase_lat'], "homebase_lon" => $player['homebase_lon'], "homebase_set_time" => $player['homebase_set_time'], supply => $player['supply'], "water" => $player['water'], "food" => $player['food'], "ammo" => $player['ammo'], "equipped_weapon_id" => $player['equipped_weapon_id'], "curr_stamina" => $player['curr_stamina'], "max_stamina" => $player['max_stamina'] );

    //survivor data
    $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id = '$id' ORDER BY team_position DESC, onMission ASC") or die(mysql_error());
    $survivor_data_array = array();
    while ($row = mysql_fetch_assoc($survivor_query)) {
        array_push($survivor_data_array, array("entry_id" => $row['entry_id'], "owner_id" => $row['owner_id'], "name" => $row['name'], "base_stam" => $row['base_stam'], "curr_stam" => $row['curr_stam'], "base_attack" => $row['base_attack'], "weapon_equipped" => $row['weapon_equipped'], "isActive" => $row['isActive'], "onMission"=>$row['onMission'], "start_time" => $row['start_time'], "team_pos" => $row['team_position'], "pic_url" => $row['profile_pic_url']));
    }

    //weapon data
    $weapon_data = mysql_query("SELECT * FROM active_weapons WHERE owner_id='$id'") or die(mysql_error());
    $weapon_data_array = array();
    if (mysql_num_rows($weapon_data) > 0) {
        while ($weapon = mysql_fetch_assoc($weapon_data)) {
            array_push($weapon_data_array, array("weapon_id" => $weapon['weapon_id'], "owner_id" => $weapon['owner_id'], "equipped_id" => $weapon['equipped_id'], "type" => $weapon['type'], "name" => $weapon['name'], "stam_cost" => $weapon['stam_cost'] ,"base_dmg" => $weapon['base_dmg'], "modifier" => $weapon['modifier'], "durability" => $weapon['durability']));
        }
    }else{
        $weapon_data_array = null;
    }
    //cleared building data
    $active_cleartime = 'now()';
    date_sub($active_cleartime, date_interval_create_from_date_string("20 hours"));
    $bldg_data = mysql_query("SELECT * FROM cleared_buildings WHERE id = '$id' AND time_cleared>$active_cleartime");
    $bldg_data_array = array();
    if (mysql_num_rows($bldg_data) > 0) {
        while ($row = mysql_fetch_assoc($bldg_data)) {
            $entry = array("id" => $row['id'], "bldg_name" => $row['bldg_name'], "bldg_id" => $row['bldg_id'], "active" => $row['active'], "time_cleared" => $row['time_cleared'], "entry_id"=>$row['entry_id']);
            array_push($bldg_data_array, $entry); 
        }
    } else {
        $bldg_data_array = null;
    }

    //outpost data
    $active_outpost_data = mysql_query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time > now()") or die(mysql_error());
    $active_outpost_array = array();
    if (mysql_num_rows($active_outpost_data) > 0){
        while($outpost = mysql_fetch_assoc($active_outpost_data)) {
            array_push($active_outpost_array, array("outpost_id" => $outpost['outpost_id'], "name" =>$outpost['name'], "outpost_lat" => $outpost['outpost_lat'], "outpost_lng"=>$outpost['outpost_lng'], "expire_time"=>$outpost['expire_time'], "capacity"=>$outpost['capacity']));
        }
    }else{
        $active_outpost_array = null;
    }

    //mission data
    $mission_data = mysql_query("SELECT * FROM missions_table WHERE owner_id='$id' ORDER BY time_complete DESC") or die(mysql_error());
    $mission_data_array = array();
    if (mysql_num_rows($mission_data) > 0) {
        while($mission = mysql_fetch_assoc($mission_data)) {
            $this_mission_array = array("mission_id"=>$mission['mission_id'], "building_name"=>$mission['building_name'], "building_id"=>$mission['building_id'], "survivor1_id"=>$mission['survivor1_id'], "survivor1_curr_stam"=>$mission['survivor1_curr_stam'], "survivor1_dead"=>$mission['survivor1_dead'], "survivor2_id"=>$mission['survivor2_id'], "survivor2_curr_stam"=>$mission['survivor2_curr_stam'], "survivor2_dead"=>$mission['survivor2_dead'], "survivor3_id"=>$mission['survivor3_id'], "survivor3_curr_stam"=>$mission['survivor3_curr_stam'], "survivor3_dead"=>$mission['survivor3_dead'], "survivor4_id"=>$mission['survivor4_id'], "survivor4_curr_stam"=>$mission['survivor4_curr_stam'], "survivor4_dead"=>$mission['survivor4_dead'], "survivor5_id"=>$mission['survivor5_id'], "survivor5_curr_stam"=>$mission['survivor5_curr_stam'], "survivor5_dead"=>$mission['survivor5_dead'], "supply_found"=>$mission['supply_found'], "water_found"=>$mission['water_found'], "food_found"=>$mission['food_found'], "time_complete"=>$mission['time_complete'], "duration"=>$mission['duration'], "ammo_used"=>$mission['ammo_used']);
            array_push($mission_data_array, $this_mission_array);
        }
    } else {
        $mission_data_array = null;
    }

    //assemble the array
    array_push($return_array, "Success");
    array_push($return_array, $player_data_array);
    array_push($return_array, $survivor_data_array);
    array_push($return_array, $weapon_data_array);
    array_push($return_array, $bldg_data_array);
    array_push($return_array, $active_outpost_array);
    array_push($return_array, $mission_data_array);
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not sent");
}
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;

?>