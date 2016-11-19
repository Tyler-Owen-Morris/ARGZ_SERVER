<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$building_id = isset($_POST['building_id']) ? protect($_POST['building_id']) : '';
$building_name = isset($_POST['building_name']) ? protect($_POST['building_name']) : '';

$survivor1_id = isset($_POST['survivor1_id']) ? protect($_POST['survivor1_id']) : '';
$survivor1_curr_stam = isset($_POST['survivor1_curr_stam']) ? protect($_POST['survivor1_curr_stam']) : '';
$survivor1_dead = isset($_POST['survivor1_dead']) ? protect($_POST['survivor1_dead']) : '';

$survivor2_id = isset($_POST['survivor2_id']) ? protect($_POST['survivor2_id']) : '';
$survivor2_curr_stam = isset($_POST['survivor2_curr_stam']) ? protect($_POST['survivor2_curr_stam']) : '';
$survivor2_dead = isset($_POST['survivor2_dead']) ? protect($_POST['survivor2_dead']) : '';

$survivor3_id = isset($_POST['survivor3_id']) ? protect($_POST['survivor3_id']) : '';
$survivor3_curr_stam = isset($_POST['survivor3_curr_stam']) ? protect($_POST['survivor3_curr_stam']) : '';
$survivor3_dead = isset($_POST['survivor3_dead']) ? protect($_POST['survivor3_dead']) : '';

$survivor4_id = isset($_POST['survivor4_id']) ? protect($_POST['survivor4_id']) : '';
$survivor4_curr_stam = isset($_POST['survivor4_curr_stam']) ? protect($_POST['survivor4_curr_stam']) : '';
$survivor4_dead = isset($_POST['survivor4_dead']) ? protect($_POST['survivor4_dead']) : '';

$survivor5_id = isset($_POST['survivor5_id']) ? protect($_POST['survivor5_id']) : '';
$survivor5_curr_stam = isset($_POST['survivor5_curr_stam']) ? protect($_POST['survivor5_curr_stam']) : '';
$survivor5_dead = isset($_POST['survivor5_dead']) ? protect($_POST['survivor5_dead']) : '';

$supply_found = isset($_POST['supply_found']) ? protect($_POST['supply_found']) : '';
$water_found = isset($_POST['water_found']) ? protect($_POST['water_found']) : '';
$food_found = isset($_POST['food_found']) ? protect($_POST['food_found']) : '';
$duration = isset($_POST['duration']) ? protect($_POST['duration']) : '';
$ammo_used = isset($_POST['ammo_used']) ? protect($_POST['ammo_used']) : '';

if ($id <> '') {
    if ($survivor1_id <> '' || $survivor2_id <> '' || $survivor3_id <> '' || $survivor4_id <> '' || $survivor5_id <> '') {
        if ($survivor1_curr_stam <> '' || $survivor2_curr_stam <> '' || $survivor3_curr_stam <> '' || $survivor4_curr_stam <> '' || $survivor5_curr_stam <> '') {
            if ($survivor1_dead <> '' || $survivor2_dead <> '' || $survivor3_dead <> '' || $survivor4_dead <> '' || $survivor5_dead <> '') {
                if ($supply_found <> '' || $water_found <> '' || $food_found <> '' || $duration <> '' || $ammo_used <> '') {
                    //get the completed time interval string
                    $now = 'now()';
                    $interval_string = "interval $duration minute";

                    $mission_insert = $mysqli->query("INSERT INTO missions_table (
                        owner_id, building_id, building_name,
                        survivor1_id, survivor1_curr_stam, survivor1_dead,
                        survivor2_id, survivor2_curr_stam, survivor2_dead,
                        survivor3_id, survivor3_curr_stam, survivor3_dead,
                        survivor4_id, survivor4_curr_stam, survivor4_dead,
                        survivor5_id, survivor5_curr_stam, survivor5_dead,
                        supply_found, water_found, food_found,
                        time_complete, duration, ammo_used
                    ) VALUES (
                        '$id', '$building_id', '$building_name',
                        '$survivor1_id', '$survivor1_curr_stam', '$survivor1_dead', 
                        '$survivor2_id', '$survivor2_curr_stam', '$survivor2_dead',
                        '$survivor3_id', '$survivor3_curr_stam', '$survivor3_dead',
                        '$survivor4_id', '$survivor4_curr_stam', '$survivor4_dead',
                        '$survivor5_id', '$survivor5_curr_stam', '$survivor5_dead',
                        '$supply_found', '$water_found', '$food_found',
                        date_add($now, $interval_string), '$duration', '$ammo_used')") or die($mysqli->error());

                    //update the survivor records to set the "on_mission" boolean to true
                    $surv1_update = $mysqli->query("UPDATE survivor_roster SET onMission=1 AND dead='$survivor1_dead' WHERE owner_id='$id' AND entry_id='$survivor1_id'") or die($mysqli->error());
                    $surv2_update = $mysqli->query("UPDATE survivor_roster SET onMission=1 AND dead='$survivor2_dead' WHERE owner_id='$id' AND entry_id='$survivor2_id'") or die($mysqli->error());
                    $surv3_update = $mysqli->query("UPDATE survivor_roster SET onMission=1 AND dead='$survivor3_dead' WHERE owner_id='$id' AND entry_id='$survivor3_id'") or die($mysqli->error());
                    $surv4_update = $mysqli->query("UPDATE survivor_roster SET onMission=1 AND dead='$survivor4_dead' WHERE owner_id='$id' AND entry_id='$survivor4_id'") or die($mysqli->error());
                    $surv5_update = $mysqli->query("UPDATE survivor_roster SET onMission=1 AND dead='$survivor5_dead' WHERE owner_id='$id' AND entry_id='$survivor5_id'") or die($mysqli->error());

                    //attempt to update the building entry
                    $now = 'now()';
                    $interval_string1 = "interval $duration minute";
                    $building_update = $mysqli->query("UPDATE cleared_buildings SET active=0, time_cleared=date_add($now, $interval_string1), supply=supply-$supply_found, food=food-$food_found, water=water-$water_found WHERE id='$id' AND bldg_id='$building_id'") or die($mysqli->error());
                    if($mysqli->affected_rows > 0) {
                        array_push($return_array, "Success");
                        array_push($return_array, "Mission added, and building set to inactive.");
                    } else {
                        //insert the entry
                        $building_insert = $mysqli->query("INSERT INTO cleared_buildings (id, bldg_name, bldg_id, active, time_cleared) VALUES ('$id', '$building_name', '$building_id', 0, date_add($now, $interval_string1))") or die($mysqli->error());

                        if ($mysqli->affected_rows > 0) {
                            array_push($return_array, "Success");
                            array_push($return_array, "Mission added, and building set to inactive.");
                        } else {
                            array_push($return_array, "Failed");
                            array_push($return_array, "failed to update the cleared building");
                        }
                    }

                    // if(mysql_affected_rows() > 0) {
                    //     array_push($return_array, "Success");
                    //     array_push($return_array, "mission has been added to the database");
                    // } else {
                    //     array_push($return_array, "Failed");
                    //     array_push($return_array, "Mission failed to insert into the table");
                    // }
                }else{
                    array_push($return_array, "Failed");
                    array_push($return_array, "one or more of the earned stat results is not set");
                }
            }else{
                array_push($return_array, "Failed");
                array_push($return_array, "one or more survivor death result not set");
            }
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "one or more survivor stamina results not set");
        }
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "One or more survivor ID's not set");
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "User ID not set");
}
$json_return = json_encode($return_array);
echo $json_return;

?>