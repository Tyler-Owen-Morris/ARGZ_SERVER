<?php
include("db_connect.php");
$return_array = array();
$player_query = $mysqli->query("SELECT * FROM player_sheet WHERE id='$id'") or die($mysqli->error());
$player_data = $player_query->fetch_assoc();
$zombie_status = $player_data['isZombie'];

if ($mysqli->affected_rows > 0) {
    array_push ($return_array, "Success");
    array_push($return_array, $zombie_status);
    array_push($return_array, $player_data);
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "no charachter found");
}

$json_data = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_data;

?>