<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';

if($id <> '') {
    //client has verified the player's range to their home- we just need to get all survivors and set the curr_stam = max_stam
    $survivor_update = $mysqli->query("UPDATE survivor_roster SET curr_stam=base_stam WHERE owner_id='$id'") or die($mysqli->error());

    if ($survivor_update->affected_rows > 0) {
        array_push($return_array, "Success");
        array_push($return_array, "All survivors stamina set to maximum");
    }

}else {
    array_push ($return_array, "Failed");
    array_push ($return_array, "player ID not set");
}

$jsonReturn =  json_encode($return_array);
echo $jsonReturn;



?>