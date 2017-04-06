<?php
include("db_connect.php");
$return_array = array();
$player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
$player_data = mysql_fetch_assoc($player_query);
$zombie_status = $player_data['isZombie'];

if (mysql_affected_rows() > 0) {
	//this is where we send "now" to the client for calculating an offset
	$now_query = mysql_query("SELECT NOW()") or die(mysql_error());
	$now_data= mysql_fetch_assoc($now_query);
		
    array_push ($return_array, "Success");
    array_push($return_array, $zombie_status); //boolean indicating if the client needs to go to zombie mode.
    array_push($return_array, $player_data); //send the player record
	array_push($return_array, $now_data); //send server NOW time for setting client time offset
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "no charachter found");
}

$json_data = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_data;

?>