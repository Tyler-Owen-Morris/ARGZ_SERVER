<?php
include("db_connect.php");

$returnArray = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$survivor_id = isset($_POST['survivor_id']) ? protect($_POST['survivor_id']) : '';
$weapon_id = isset($_POST['weapon_id']) ? protect($_POST['weapon_id']) : '';
$zombie_killed = isset($_POST['zombie_kill']) ? protect($_POST['zombie_kill']) : '';
$building_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';

if ($id <> '') {
    if ($survivor_id <> '' || $building_id <> '') {
        if ($weapon_id <> '') {
            if($weapon_id != 0) {
                //subtract stamina cost from survivor's current stamina
                $weapon_query = $mysqli->query("SELECT * FROM active_weapons WHERE owner_id='$id' AND weapon_id='$weapon_id'") or die($mysqli->error());
                $row = $weapon_query->fetch_assoc;
                $stam_cost = $row['stam_cost'];
                //this query allows for negative stamina numbers
                $stamina_update1 = $mysqli->query("UPDATE survivor_roster SET curr_stam=curr_stam-$stam_cost WHERE entry_id='$survivor_id' AND owner_id='$id'") or die($mysqli->error());

                /* //This was the old way of making sure stamina didn't go negative. We are now working with possible negative stamina ^^^^^^^
                $stamina_update = mysql_query("UPDATE survivor_roster SET curr_stam=curr_stam-$stam_cost WHERE owner_id='$id' AND entry_id='$survivor_id' AND curr_stam>$stam_cost") or die(mysql_error());
                if(mysql_affected_rows() == 0){
                    $stamina_update = mysql_query("UPDATE survivor_roster SET curr_stam=0 WHERE entry_id='$survivor_id' and owner_id='$id'") or die(mysql_error());
                }
                */

                //subtract durability from the weapon
                $durability = $row['durability'];
                if ($durability-1 <= 0) {
                    //if the weapon is out of durability on this swing, remove it from the DB, it's destoyed
                    $delete = $mysqli->query("DELETE FROM active_weapons WHERE owner_id='$id' AND weapon_id='$weapon_id' LIMIT 1") or die($mysqli->error());
                } else {
                    $durability_update = $mysqli->query("UPDATE active_weapons SET durability=durability-1 WHERE weapon_id='$weapon_id' AND owner_id='$id'") or die($mysqli->error());
                }

                //if it's a gun, deal with the ammo reduction
                $type = $row['type'];
                if ($type == "gun") {
                    $ammo_update = $mysqli->query("UPDATE player_sheet SET ammo=ammo-1 WHERE id='$id' AND ammo > 0") or die($mysqli->error());
                    //the damage returned by the client will adjust for no-ammo swings.
                }

                array_push($returnArray, "Success");
                array_push($returnArray, "Player & weapon have been adjusted on the server");
            } else {
                //unarmed swing, no weapon to reduce durability on.

                $survivor_update2 = $mysqli->query("UPDATE survivor_roster SET curr_stam=curr_stam-5 WHERE owner_id='$id' AND entry_id='$survivor_id'") or die($mysqli->error());

                /*  //this query does not allow stamina to become negative. New math requires negative stamina numbers
                $survivor_update = mysql_query("UPDATE survivor_roster SET curr_stam=curr_stam-5 WHERE owner_id='$id' AND entry_id='$survivor_id' AND curr_stam>5") or die(mysql_error());
                if(mysql_affected_rows()==0) {
                    $survivor_update2 = mysql_query ("UPDATE survivor_roster SET curr_stam=0 WHERE owner_id='$id' and entry_id='$survivor_id'") or die(mysql_error());
                }
                */
                if ($mysqli->affected_rows > 0){
                    array_push($returnArray, "Success");
                    array_push($returnArray, "Player has executed an unarmed attack.");
                } else {
                    array_push($returnArray, "Failed");
                    array_push($returnArray, "unable to execute attack");
                }
            }

            if ($zombie_killed == 1) {
                $kill_update = $mysqli->query("UPDATE player_sheet SET zombies_killed=zombies_killed+1 WHERE id='$id'") or die($mysqli->error());
                if ($mysqli->affected_rows) {
                    array_push($returnArray, "player sheet updated successfully");
                } else {
                    array_push($returnArray, "did not update the player sheet successfully");
                }
                $bldg_update = $mysqli->query("UPDATE cleared_buildings SET zombies=zombies-1 WHERE id='$id' AND bldg_name='$building_name'") or die($mysqli->error());
                if ($mysqli->affected_rows) {
                    array_push($returnArray, "cleared building updated successfully");
                } else {
                    array_push($returnArray, "did not update the buildings successfully");
                }
            }

        }else{
            array_push($returnArray, "Failed");
            array_push($returnArray, "weapon ID not set");
        }
    }else{
        array_push($returnArray, "Failed");
        array_push($returnArray, "Survivor ID not set");
    }

} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "Player ID not set");
}
$jsonreturn = json_encode($returnArray);
echo $jsonreturn;
?>