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
		if ($team_pos >0){
        	//update 1 survivor, with team position 0 to the old teammember's position
        	$update_replacement = mysql_query("UPDATE survivor_roster SET team_position='$team_pos' WHERE owner_id='$id' AND entry_id!='$survivor_id' AND team_position=0 LIMIT 1"); //no OR DIE- we don't care if there's an error- continue...
		}

		//set the survivor to dead and off the team. retain the record for permenant death
		$surv_update = mysql_query("UPDATE survivor_roster SET team_position=0, isActive=0, dead=1 WHERE owner_id='$id' AND entry_id='$survivor_id'")or die(mysql_error());
        //remove the dead survivor
        //$delete = mysql_query("DELETE FROM survivor_roster WHERE entry_id='$survivor_id' AND owner_id='$id' LIMIT 1") or die(mysql_error());

        if (mysql_affected_rows() > 0) {
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