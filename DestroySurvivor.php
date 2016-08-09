<?php
include("db_connect.php");
$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$survivor_id = isset($_POST['survivor_id']) ? protect($_POST['survivor_id']) : '';

if ($id <> ''){
    if ($survivor_id <> '') {
        //find the team member's team position'
        $surv_query = mysql_query("SELECT * FROM survivor_roster WHERE entry_id='$survivor_id' AND owner_id='$id'") or die(mysql_error());
        $row = mysql_fetch_assoc($surv_query);
        $team_pos = $row['team_position'];
        //update 1 survivor, with team position 0 to the old teammember's position
        $update_replacement = mysql_query("UPDATE survivor_roster SET team_position='$team_pos' WHERE owner_id='$id' AND team_position=0 LIMIT 1");

        //remove the dead survivor
        $delete = mysql_query("DELETE * FROM survivor_roster WHERE entry_id='$survivor_id' AND owner_id='$id' LIMIT 1") or die(mysql_error());

        if (mysql_affected_rows() > 0) {
            array_push($returnArray, "Success");
            array_push($returnArray, "Survivor record was removed");
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