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

    //building data
    $bldg_query = mysql_query("SELECT * FROM baited_buildings WHERE owner_id='$id'") or die(mysql_error());
    $bldg_data_array = array();
    if (mysql_num_rows($bldg_query)>0) {
        while($bldg = mysql_fetch_assoc($bldg_query)){
            array_push($bldg_data_array, $bldg);
        }
    }else{
        $bldg_data_array = null;
    }
		
    array_push ($return_array, "Success");
    array_push($return_array, $zombie_status); //boolean indicating if the client needs to go to zombie mode.
    array_push($return_array, $player_data); //send the player record
	array_push($return_array, $now_data); //send server NOW time for setting client time offset
    array_push($return_array, $bldg_data_array); //add any zombie baited buildings
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "no charachter found");
}

$json_data = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_data;

?>