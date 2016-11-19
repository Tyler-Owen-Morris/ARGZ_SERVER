<?php
include("db_connect.php");
$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$survivor_id = isset($_POST['survivor_id']) ? protect($_POST['survivor_id']) : '';

if ($id <> ''){
    if ($survivor_id <> '') {
        $update = $mysqli->query("UPDATE survivor_roster SET curr_stam=base_stam WHERE entry_id='$survivor_id' AND owner_id='$id' LIMIT 1") or die($mysqli->error());

        if ($mysqli->affected_rows > 0) {
            array_push($returnArray, "Success");
            array_push($returnArray, "Survivor record was restored");
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "Failed to locate survivor record");
        }
    } else {
        array_push($returnArray, "Failed");
        array_push($returnArray, "Survivor ID not set");
    }
} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "Player ID not set");
}
$jsonreturn = json_encode($return_array);
echo $jsonreturn;

?>