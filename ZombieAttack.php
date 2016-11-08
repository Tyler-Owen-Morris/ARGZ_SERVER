<?php
include("db_connect.php");

$returnArray = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$survivor_id = isset($_POST['survivor_id']) ? protect($_POST['survivor_id']) : '';
$damage = isset($_POST['dmg']) ? protect($_POST['dmg']) : '';

if ($id <> '') {
    if ($survivor_id <> '') {
        if ($damage <> '') {
            //update the player stamina 
            $survivor_update = mysql_query("UPDATE survivor_roster SET curr_stam=curr_stam-$damage WHERE entry_id='$survivor_id' AND owner_id='$id'") or die(mysql_error());
            if (mysql_affected_rows() == 0){
                array_push($returnArray, "Failed");
                array_push($returnArray, "Failed to update survivor roster");
                //$survivor_update = mysql_query("UPDATE survivor_roster SET curr_stam=0 WHERE entry_id='$survivor_id' and owner_id='$id'") or die(mysql_error());
            } else {
                array_push($returnArray, "Success");
                array_push($returnArray, "successfully updated the server records");
            }

        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "Zombie dmg not set");
        }
    }else{
        array_push($returnArray, "Failed");
        array_push($returnArray, "survivor ID not set");
    }
} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "Player ID not set");
}
$jsonreturn = json_encode($returnArray);
echo $jsonreturn;

?>