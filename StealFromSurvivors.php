<?php
    include("db_connect.php");

    $return_array = array();

    $food = isset($_POST['food']) ? protect($_POST['food']) : '';
    $water = isset($_POST['water']) ? protect($_POST['water']) : '';
    $wood = isset($_POST['wood']) ? protect($_POST['wood']) : '';
    $metal = isset($_POST['metal']) ?  protect($_POST['metal']) : '';

    if ($food <> '' || $water <> '' || $wood <> '' || $metal <> '' ) {
		$player_update = mysql_query("UPDATE player_sheet SET food=food+$food, water=water+$water, wood=wood+$wood, metal=metal+$metal WHERE id='$id'") or die(mysql_error());
		
		if (mysql_affected_rows()){
			array_push($return_array, "Success");
			array_push($return_array, "Resources successfully added to player sheet");
		}else{
			array_push($return_array, "Failed");
			array_push($return_array, "unable to update player record");
		}
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
?>