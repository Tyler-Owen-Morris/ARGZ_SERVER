<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';

if (id <> '') {
    //get the missions by player ID, sort by date_complete descending
    $mission_query = mysql_query("SELECT * FROM missions_table WHERE owner_id='$id' ORDER BY time_complete DESC") or die(mysql_error());

    $mission_array = array();
    while($mission = mysql_fetch_assoc($mission_query)) {
        $this_mission_array = array("mission_id"=>$mission['mission_id'], "building_name"=>$mission['building_name'], "building_id"=>$mission['building_id'], "survivor1_id"=>$mission['survivor1_id'], "survivor1_curr_stam"=>$mission['survivor1_curr_stam'], "survivor1_dead"=>$mission['survivor1_dead'], "survivor2_id"=>$mission['survivor2_id'], "survivor2_curr_stam"=>$mission['survivor2_curr_stam'], "survivor2_dead"=>$mission['survivor2_dead'], "survivor3_id"=>$mission['survivor3_id'], "survivor3_curr_stam"=>$mission['survivor3_curr_stam'], "survivor3_dead"=>$mission['survivor3_dead'], "survivor4_id"=>$mission['survivor4_id'], "survivor4_curr_stam"=>$mission['survivor4_curr_stam'], "survivor4_dead"=>$mission['survivor4_dead'], "survivor5_id"=>$mission['survivor5_id'], "survivor5_curr_stam"=>$mission['survivor5_curr_stam'], "survivor5_dead"=>$mission['survivor5_dead'], "supply_found"=>$mission['supply_found'], "water_found"=>$mission['water_found'], "food_found"=>$mission['food_found'], "time_complete"=>$mission['time_complete'], "duration"=>$mission['duration'], "ammo_used"=>$mission['ammo_used']);
        array_push($mission_array, $this_mission_array);
    }
    
    if(mysql_num_rows($mission_query) > 0) {
        array_push($return_array, "Success");
        array_push($return_array, $mission_array);
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "Database returned no entries");
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;

?>