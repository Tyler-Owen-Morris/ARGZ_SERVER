<?php
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])) {
    $id = protect($_POST['id']);

    //first we check to be sure there is a homebase created for the user.
    $query1 = mysql_query ("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());
    //grab any expired weapons
    $weaponquery1 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete<NOW()") or die(mysql_error());

    //expire any completed weapons and add them to the homebase_sheet for the same user
    if (mysql_num_rows($weaponquery1) > 0) {
        while ($weapon = mysql_fetch_assoc($weaponquery1)) {
            $entry_id = $weapon['entry_id'];
            $type = $weapon['type'];
            $duration = $weapon['duration'];
            $time_complete = strtotime($weapon['time_complete']);

            //remove the expired entry from the crafting database
            $delete1 = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$entry_id' AND id='$id'") or die(mysql_error());

            //this adds one to the correct weapon type on the homebase_sheet
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
    } 
    //otherwise continue to doing the drop/pickup

    if (mysql_num_rows($query1) > 0) {
        //check that there is only one entry
        if (mysql_num_rows($query1) == 1) {
            //success: now we need to remove all supply from the user_sheet and add that number to the homebase_sheet
            $query2 = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
            $row1 = mysql_fetch_assoc($query1);
            $row2 = mysql_fetch_assoc($query2);
            $transfered_supply = $row2['supply'];
            $new_supply = $row1['supply'] + $transfered_supply;

            if ($transfered_supply >= 0) {
                //create the insert queries to update both tables
                $update = mysql_query("UPDATE homebase_sheet SET supply ='$new_supply' WHERE id='$id'") or die(mysql_error()); 
                $update = mysql_query("UPDATE player_sheet SET supply = 0 WHERE id='$id'");

                //need to check for completed weapons, expire them from the weapon_crafting sheet, and add them to the homebase
                
                //grab the weapons completed
                $knives = $row1['knife_for_pickup'];
                $clubs = $row1['club_for_pickup'];
                $ammo = $row1['ammo_for_pickup'];
                $guns = $row1['gun_for_pickup'];
                $survivors = $row1['active_survivor_for_pickup'];
                $pickup_array = array("knife_for_pickup" => $knives, "club_for_pickup" => $clubs, "ammo_for_pickup" => $ammo, "gun_for_pickup" => $guns, "active_survivor_for_pickup" => $survivors);
                
                //if there's ammo to transfer, add it to the player sheet
                if ($ammo > 0 ){
                    $update = mysql_query("UPDATE player_sheet SET ammo = ammo + $ammo WHERE id='$id'") or die(mysql_error());
                }
                //if any of the other weapons are greater than 0 create that weapon from static class and instantiate on active_weapons on the
                if ($knives > 0 ) {
                    $knife_lookup = mysql_query("SELECT * FROM static_weapon_classes WHERE type='knife'") or die(mysql_error());
                    $knife_data = mysql_fetch_assoc($knife_lookup);
                    $knife_name = $knife_data['name'];
                    $knife_type = $knife_data['type'];
                    $knife_base_dmg = $knife_data['base_dmg'];
                    $knife_top_dmg = $knife_data['top_dmg'];
                    $knife_bot_dmg = $knife_data['bot_dmg'];
                    $knife_dur = $knife_data['durability'];

                    for ($i=0; $i < $knives; $i++) {
                        $insert = mysql_query("INSERT INTO active_weapons (owner_id, name, type, base_dmg, top_dmg, bot_dmg, durability, isEquipped) VALUES ('$id', '$knife_name', '$knife_type', '$knife_base_dmg', '$knife_top_dmg', '$knife_bot_dmg', '$knife_dur', 0 )") or die(mysql_error());
                    }
                }

                if ($clubs > 0) {
                    $club_lookup = mysql_query("SELECT * FROM static_weapon_classes WHERE type='club'") or die(mysql_error());
                    $club_data = mysql_fetch_assoc($club_data);
                    $club_name = $club_data['name'];
                    $club_type = $club_data['type'];
                    $club_base_dmg = $club_data['base_dmg'];
                    $club_top_dmg = $club_data['top_dmg'];
                    $club_bot_dmg = $club_data['bot_dmg'];
                    $club_dur = $club_data['durability'];

                    for ($i=0; $i < $clubs; $i++) {
                        $insert = mysql_query("INSERT INTO active_weapons (owner_id, name, type, base_dmg, top_dmg, bot_dmg, durability, isEquipped) VALUES ('$id', '$club_name', '$club_type', '$club_base_dmg', '$club_top_dmg', '$club_bot_dmg', '$club_dur', 0 )") or die(mysql_error());
                    }
                }

                if ( $guns > 0 ) {
                    $gun_lookup = mysql_query("SELECT * FROM static_weapon_classes WHERE type='gun'") or die(mysql_error());
                    $gun_data = mysql_fetch_assoc ($gun_lookup);
                    $gun_name = $gun_data['name'];
                    $gun_type = $gun_data['type'];
                    $gun_base_dmg = $gun_data['base_dmg'];
                    $gun_top_dmg = $gun_data['top_dmg'];
                    $gun_bot_dmg = $gun_data['bot_dmg'];
                    $gun_durability = $gun_data['durability'];

                    for ($i=0; $i < $guns; $i++) {
                        $insert = mysql_query("INSERT INTO active_weapons (owner_id, name, type, base_dmg, top_dmg, bot_dmg, durability, isEquipped) VALUES ('$id', '$gun_name', '$gun_type', '$gun_base_dmg', '$gun_top_dmg', '$gun_bot_dmg', '$gun_durability', 0 )") or die(mysql_error());
                    }
                }


                //after the weapons have been created on the active weapons table- remove them from the homebase
                $update = mysql_query("UPDATE homebase_sheet SET knife_for_pickup=0, club_for_pickup=0, ammo_for_pickup=0, gun_for_pickup=0, active_survivor_for_pickup=0 WHERE id='$id'") or die(mysql_error());

                array_push($return_array, "Success");
                array_push($return_array, "Player has completed a resupply");
                if ($knives > 0 || $clubs > 0 || $ammo > 0 || $guns > 0 || $survivors > 0) {
                    array_push($return_array, $pickup_array);
                }else{
                    array_push($return_array, "none");
                }
                $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
                echo $json_return;
            } else {
                //if the player has no supply to transfer
                array_push($return_array, "Failed");
                array_push($return_array, "You cannot transfer negative supply");
                $json_return = json_encode($return_array);
                echo $json_return;
            } 

        } else if (mysql_num_rows($query1) > 1 ) {
            array_push($return_array, "Failed");
            array_push($return_array, "More than one entry found for the players homebase");
            $json_return = json_encode($return_array);
            echo $json_return;
        }


    } else {
        //the player has not yet set their homebase- return failure
        array_push($return_array, "Failed");
        array_push($return_array, "No matching entries for the players homebase");
        $json_return = json_encode($return_array);
        echo $json_return;
    }


} else {
    echo "No user ID sent in form";
}

?>