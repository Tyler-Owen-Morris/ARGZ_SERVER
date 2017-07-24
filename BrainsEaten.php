<?php
//BrainsEaten.php
include("db_connect.php");
$return_array = array();

if ($id <> ''){
    
	$sql_update = mysql_query("UPDATE player_sheet SET brains = brains+1 where id='$id' LIMIT 1") or die(mysql_error());
	if(mysql_affected_rows()){
		//if successful- get the updated player information, and load it into the return array.
		array_push($return_array, "Success");
		$player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id' LIMIT 1") or die(mysql_error());
		$player_data = mysql_fetch_assoc($player_query);
		array_push($return_array, $player_data);

	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "unable to update player sheet with new brain acquisition");
	}
    
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}
$jsonreturn = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $jsonreturn;
//BrainsEaten.php
?>