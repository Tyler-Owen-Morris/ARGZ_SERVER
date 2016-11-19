<?php
include("db_connect.php");
$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$survivor_id = isset($_POST['survivor_id']) ? protect($_POST['survivor_id']) : '';

if ($id <> ''){
    if ($survivor_id <> '') {
        //find the team member's team position'
        $surv_query = $mysqli->query("SELECT * FROM survivor_roster WHERE entry_id='$survivor_id' AND owner_id='$id'") or die($mysqli->error());
        $row = $surv_query->fetch_assoc();
        $team_pos = $row['team_position'];
        //update 1 survivor, with team position 0 to the old teammember's position
        $update_replacement = $mysqli->query("UPDATE survivor_roster SET team_position='$team_pos' WHERE owner_id='$id' AND team_position=0 LIMIT 1");

        //remove the dead survivor
        $delete = $mysqli->query("DELETE FROM survivor_roster WHERE entry_id='$survivor_id' AND owner_id='$id' LIMIT 1") or die($mysqli->error());

        if ($mysqli->affected_rows > 0) {
            array_push($return_array, "Success");
            array_push($return_array, "Survivor record was removed");
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "Failed to locate survivor record");
        }
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "Survivor ID not set");
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}
$jsonreturn = json_encode($return_array);
echo $jsonreturn;

?>