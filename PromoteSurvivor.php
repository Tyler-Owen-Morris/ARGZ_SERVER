<?php
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])) {
    if(isset($_POST['survivor_id'])) {
        $id = protect($_POST['id']);
        $survivor_id = protect($_POST['survivor_id']);
        //find the existing survivor at position 4, and everyone below that
       
        $update_pos = $mysqli->query("UPDATE survivor_roster SET team_position=team_position-1 WHERE owner_id='$id' AND team_position < 5 AND team_position > 0") or die($mysqli->query());
        
        $promotion_query = $mysqli->query("UPDATE survivor_roster SET team_position=4 WHERE entry_id='$survivor_id' AND owner_id='$id'") or die($mysqli->error());
        
        array_push($return_array, "Success");
        array_push($return_array, "Survivor positions successfully updated");
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "Survivor ID not set");   
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Weapon ID not set");
}
$json_return = json_encode($return_array);
echo $json_return;
//  PromoteSurvivor.php
?>