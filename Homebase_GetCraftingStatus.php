<?php 
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])){
    $id = protect($_POST['id']);
    //get the weapons still being worked on
    $query1 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete > NOW()") or die(mysql_error());
    //get the weapons to expire
    $query2 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete < NOW()") or die(mysql_error());
    $weapon_array = array();
   
    //return the active weapons
    if (mysql_num_rows($query1) > 0) {
        while ($weapon = mysql_fetch_assoc($query1)) {
            $entry_id = $weapon['entry_id'];
            $type = $weapon['type'];
            $duration = $weapon['duration'];
            $time_complete = $weapon['time_complete'];
            

            $this_weapon_array = array("entry_id" => $entry_id, "type" => $type, "duration" => $duration, "time_complete" => $time_complete);
            array_push($weapon_array, $this_weapon_array);
        }
        array_push($return_array, "Success");
        array_push($return_array, $weapon_array);
    } else {
        array_push($return_array, "Success");
        array_push($return_array, "none");
    }
    
    $completed_array = array();
    //expire any completed weapons and add them to the active_weapons for the same user
    if (mysql_num_rows($query2) > 0) {
        while ($weapon = mysql_fetch_assoc($query2)) {
            $entry_id = $weapon['entry_id'];
            $type = $weapon['type'];
            $duration = $weapon['duration'];
            $time_complete = strtotime($weapon['time_complete']);

            //remove the expired entry from the crafting database
            $delete1 = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$entry_id' AND id='$id'") or die(mysql_error());

            //this adds one to the correct weapon type on the active_weapons sheet
            if ($type == "knife"){
                $update1 = mysql_query("UPDATE homebase_sheet SET knife_for_pickup = knife_for_pickup+1 WHERE id='$id'") or die(mysql_error());
                array_push($completed_array, "knife");
            } elseif ($type == "club"){
                $update1 = mysql_query("UPDATE homebase_sheet SET club_for_pickup = club_for_pickup+1 WHERE id='$id'") or die(mysql_error());
                array_push($completed_array, "club");
            }elseif ($type == "ammo"){
                $update1 = mysql_query("UPDATE homebase_sheet SET ammo_for_pickup = ammo_for_pickup+1 WHERE id='$id'") or die(mysql_error());
                array_push($completed_array, "ammo");
            } elseif ($type == "gun"){
                $update1 = mysql_query("UPDATE homebase_sheet SET gun_for_pickup = gun_for_pickup+1 WHERE id='$id'") or die(mysql_error());
                array_push($completed_array, "gun");
            }
        }
        array_push($return_array, $completed_array);
    } else {
        array_push($return_array, "no completed weapons");
    }

    $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $jsonReturn;

} else {
    echo "id not set";
}

?>