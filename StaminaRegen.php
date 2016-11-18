<?php
include("db_connect.php");

$returnArray = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$stam_regen = isset($_POST['stam_regen']) ? protect($_POST['stam_regen']) : '';

if ($id <> '') {
    if ($stam_regen <> '') {
        //Get all of the survivors that belong to the player
        $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id'") or die(mysql_error());
        $survivor_array = array();
        //Loop through them for survivors w/o full stamina
        while ($survivor_data = mysql_fetch_assoc($survivor_query)) {
            $max_stam = $survivor_data['base_stam'];
            $curr_stam = $survivor_data['curr_stam'];
            $entry_id = $survivor_data['entry_id'];
            //if the survivor is not full.
            if ($curr_stam < $max_stam) {
                if ($curr_stam+$stam_regen > $max_stam) {
                    $update = mysql_query("UPDATE survivor_roster SET curr_stam=base_stam WHERE entry_id='$entry_id' AND owner_id='$id'") or die(mysql_error());
                    array_push($survivor_array, $entry_id);
                } else {
                    $new_stam= $curr_stam+$stam_regen;
                    $update = mysql_query("UPDATE survivor_roster SET curr_stam='$new_stam' WHERE entry_id='$entry_id' AND owner_id='$id'") or die(mysql_error());
                    array_push($survivor_array, $entry_id);
                }
            }
        }
		
		$player_update = mysql_query("UPDATE player_sheet SET last_stamina_regen=NOW() WHERE id='$id'") or die(mysql_query());
		
		if (mysql_affected_rows()>0){
			array_push($returnArray, "Success");
        	array_push($returnArray, "Stamina successfully added to characters");
        	array_push($returnArray, $survivor_array);
		}else{
			array_push($returnArray, "Failed");
			array_push($returnArray, "failed to update offline stam regen timestamp");
		}
		
        
    } else {
        array_push($returnArray, "Failed");
        array_push($returnArray, "Stamina regen not sent");
    }
} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "Player ID not set");
}
$jsonReturn = json_encode($returnArray);
echo $jsonReturn;
?>