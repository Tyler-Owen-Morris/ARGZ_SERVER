<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$survivor_id = isset($_POST['survivor_id']) ? protect($_POST['survivor_id']) : '';
$weapon_id = isset($_POST['weapon_id']) ? protect($_POST['weapon_id']) : '';

if ($id <> '') {
    if ($survivor_id <> '') {
        if ($weapon_id <> '') {
            $weapon_update = mysql_query("UPDATE active_weapons SET equipped_id=0 WHERE owner_id='$id' AND weapon_id='$weapon_id'") or die(mysql_error());
            $survivor_update = mysql_query("UPDATE survivor_roster SET weapon_equipped=0 WHERE owner_id='$id' AND entry_id='$survivor_id'") or die(mysql_error());            

            if (mysql_affected_rows($weapon_update) > 0 && mysql_affected_rows($survivor_update) > 0) {
                array_push($return_array, "Success");
                array_push($return_array, "Weapon and survivor records updated");
            }else {
                array_push ($return_array, "Failed");
                array_push ($return_array, "Records failed to update");
            }

        }else {
            array_push ($return_array, "Failed");
            array_push ($return_array, "weapon ID not set");
        }
    }else {
        array_push ($return_array, "Failed");
        array_push ($return_array, "survivor ID not set");
    }
} else {
    array_push ($return_array, "Failed");
    array_push ($return_array, "player ID not set");
}

$jsonReturn =  json_encode($return_array);
echo $jsonReturn;



?>