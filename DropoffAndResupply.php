<?php
include("db_connect.php");
$return_array = array();
$weapon_string = "";
if (isset($_POST['id'])) {
    $id = protect($_POST['id']);

    //first we check to be sure there is a homebase created for the user.
    $query1 = $mysqli->query ("SELECT * FROM homebase_sheet WHERE id='$id'") or die($mysqli->error());
    //grab any expired weapons
    $weaponquery1 = $mysqli->query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete<NOW()") or die($mysqli->error());

    //expire any completed weapons and add them to the homebase_sheet for the same user
    if ($weaponquery1->num_rows > 0) {
        while ($weapon = $weaponquery1->fetch_assoc()) {
            $entry_id = $weapon['entry_id'];
            $wep_type = $weapon['type'];
            $index = $weapon['weapon_index'];//this corresponds to it's index on the static table for crafted weapon templates
            
            //index 0 means that it's ammunition
            if ($index != 0 ) {
                //get static weapon to construct from
                $static_weapon_query = $mysqli->query("SELECT * FROM static_weapon_classes WHERE wep_id='$index' LIMIT 1") or die($mysqli->error());
                $weapon_data = $static_weapon_query->fetch_assoc();
                $weapon_name = $weapon_data['name'];
                $weapon_type = $weapon_data['type'];
                $weapon_modifier = $weapon_data['modifier'];
                $weapon_base_dmg = $weapon_data['base_dmg'];
                $weapon_durability = $weapon_data['durability'];
                $weapon_stam_cost = $weapon_data['stam_cost'];

                $weapon_string += " found expired: "+$wep_name;

                //create the crafted weapon into active inventory
                //check if we need to add ammo or an actual weapon
            
                $insert = $mysqli->query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, stam_cost, base_dmg, modifier, durability) values ('$id', 0, '$weapon_name', '$weapon_type', '$weapon_stam_cost', '$weapon_base_dmg', '$weapon_modifier', '$weapon_durability')") or die($mysqli->error());
                $weapon_string += " creating weapon record";
				
			} else {
				if ($wep_type == "trap"){
					$trap_update  = $mysqli->query("UPDATE player_sheet SET trap=trap+1 WHERE id='$id'") or die($mysqli->error());
				} else if ($wep_type == "barrel") {
					$barrel_update = $mysqli->query("UPDATE player_sheet SET barrel=barrel+1 WHERE id='$id'") or die($mysqli->error());
				}else if ($wep_type == "greenhouse"){
					$greenhouse_udpate = $mysqli->query("UPDATE player_sheet SET greenhouse=greenhouse+1 WHERE id='$id'") or die($mysqli->error());
				}else{
					 //increment the players ammo up by 1
                	$ammo_update = $mysqli->query("UPDATE player_sheet SET ammo=ammo+5 WHERE id = '$id'") or die($mysqli->error());
                	$weapon_string += " adding ammo";
				}
            }
           

            //remove the expired entry from the crafting database
            $delete1 = $mysqli->query("DELETE FROM weapon_crafting WHERE entry_id='$entry_id' AND id='$id'") or die($mysqli->error());
            $weapon_string += " and deleting from craftDB :: ";
        }
    
    } 
    //otherwise continue to doing the drop/pickup

    if ($query1->num_rows > 0) {
        //check that there is only one entry
        if ($query1->num_rows == 1) {
            //success: now we need to remove all supply from the user_sheet and add that number to the homebase_sheet
            $query2 = $mysqli->query("SELECT * FROM player_sheet WHERE id='$id'") or die($mysqli->error());
            $row1 = $query1->fetch_assoc();
            $row2 = $query2->fetch_assoc();
            $transfered_supply = $row2['supply'];
            $new_supply = $row1['supply'] + $transfered_supply;

            if ($transfered_supply >= 0) {
                //create the insert queries to update both tables
                $update = $mysqli->query("UPDATE homebase_sheet SET supply ='$new_supply' WHERE id='$id'") or die($mysqli->error()); 
                $update = $mysqli->query("UPDATE player_sheet SET supply = 0 WHERE id='$id'");
                
                array_push($return_array, "Success");
                array_push($return_array, $weapon_string);
            } else {
                //if the player has no supply to transfer
                array_push($return_array, "Failed");
                array_push($return_array, "You cannot transfer negative supply");
            } 

        } else if ($query1->num_rows > 1 ) {
            array_push($return_array, "Failed");
            array_push($return_array, "More than one entry found for the players homebase");
        }


    } else {
        //the player has not yet set their homebase- return failure
        array_push($return_array, "Failed");
        array_push($return_array, "No matching entries for the players homebase");
        
    }
} else {
    echo "No user ID sent in form";
}

$json_return = json_encode($return_array);
echo $json_return;

?>