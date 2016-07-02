<?php
include("db_connect.php");

$return_array = array();

if (isset($_POST['id'])) {
    if (isset($_POST['entry_id'])){
        $id = protect($_POST['id']);
        $entry_id = protect($_POST['entry_id']);
        $duration = protect($_POST['duration']);
        $type = protect($_POST['type']);
    
        $query1 = mysql_query("SELECT * FROM weapon_crafting WHERE time_started < DATE_SUB(NOW(), INTERVAL $duration MINUTE) AND id='$id' AND entry_id='$entry_id'") or die(mysql_error());
        if (mysql_num_rows($query1) > 0) {
            //entry must be deleted, and added to the homebase_sheet
            $delete1 = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$entry_id' AND id='$id'") or die(mysql_error());
            echo "deleted weapon: ".$entry_id." </br>";
            
            //this function looks for and starts idle weapons belonging to the same owner
            StartTheNextWeapon($id);
            
            //this adds one to the correct weapon type on the homebase_sheet
            if ($type == "knife"){
                $update1 = mysql_query("UPDATE homebase_sheet SET knife_for_pickup = knife_for_pickup+1 WHERE id='$id'") or die(mysql_error());
            } elseif ($type == "club"){
                $update1 = mysql_query("UPDATE homebase_sheet SET club_for_pickup = club_for_pickup+1 WHERE id='$id'") or die(mysql_error());
            }elseif ($type == "ammo"){
                $update1 = mysql_query("UPDATE homebase_sheet SET ammo_for_pickup = ammo_for_pickup+1 WHERE id='$id'") or die(mysql_error());
            } elseif ($type == "gun"){
                $update1 = mysql_query("UPDATE homebase_sheet SET gun_for_pickup = gun_for_pickup+1 WHERE id='$id'") or die(mysql_error());
            }

            array_push($return_array, "Success");
            array_push($return_array, "weapon was expired- deleted- and new weapon started");
            $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $jsonReturn;
        }
    }else{
    array_push($return_array, "Failed");
    array_push($return_array, "entry_ID not set");
    $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $jsonReturn;
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "ID not set");
    $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $jsonReturn;
}

function StartTheNextWeapon ($userid) {
    $weapon_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$userid' AND active='0' ORDER BY time_started") or die(mysql_error());

    if (mysql_num_rows($weapon_query) > 0 ){
        // turn only 1 to active.
        $to_active = 1;
        while ($weapon = mysql_fetch_assoc($weapon_query)){
            if ($to_active > 0 ){
                $to_active--;
                $wep_id = $weapon['entry_id'];
                $wep_update = mysql_query("UPDATE weapon_crafting SET active='1', time_started=NOW() WHERE entry_id='$wep_id'") or die(mysql_error());
                echo "started next weapon ".$wep_id." should be active";
            }else{
                continue;
            }
        }
    } else {
        echo "last weapon expired for user: ".$userid."";
    }
}



?>