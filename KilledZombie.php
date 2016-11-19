<?php
include("db_connect.php");

$return_array = array();

$zombie_id = isset($_POST['zombie_id']) ? protect($_POST['zombie_id']) : '';

if($zombie_id <> '') {
    $zombie_update = $mysqli->query("UPDATE player_sheet SET isZombie=2 WHERE id='$zombie_id' AND isZombie=1") or die($mysqli->error());
    if ($zombie_update->affected_rows > 0) {
        $player_record = $mysqli->query("SELECT * FROM player_sheet WHERE id='$zombie_id'") or die($mysqli->error());
        $zombie_player_array = $player_record->fetch_assoc();
        $player_reward = $mysqli->query("UPDATE player_sheet SET food=food+20, water=water+20, supply=supply+50 WHERE id='$id'") or die($mysqli->error());
        array_push($return_array, "Success");
        array_push($return_array, "reward");
        array_push($return_array, $zombie_player_array);
    } else {
        array_push($return_array, "Success");
        array_push($return_array, "no reward");
    }
} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "Zombie ID not set");
}
$jsonReturn = json_encode($return_array);
echo $jsonReturn;
?>