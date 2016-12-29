<?php
include("db_connect.php");
$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$wep_choice = isset($_POST['weapon_selected']) ? protect($_POST['weapon_selected']) : '';

if ($id <> '') {
    if($wep_choice <> '') {
        //get the survivors and static weapons
        $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' ORDER BY team_position DESC") or die(mysql_error());
        
        $main_knife_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 4") or die(mysql_error());
        $main_club_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 5") or die(mysql_error());
        $main_gun_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 3") or die(mysql_error());
        $shotgun_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 6") or die(mysql_error());
        $weak_knife_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 1") or die(mysql_error());
        $weak_club_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 2") or die(mysql_error());
        

        $main_knife_data = mysql_fetch_assoc($main_knife_query);
        $main_club_data = mysql_fetch_assoc($main_club_query);
        $main_gun_data = mysql_fetch_assoc($main_gun_query);
        $shotgun_data = mysql_fetch_assoc($shotgun_query);
        $weak_knife_data = mysql_fetch_assoc($weak_knife_query);
        $weak_club_data = mysql_fetch_assoc($weak_club_query);

        //match the weapon to the choice.
        if ($wep_choice == "knife") {
            //create the weapons and ssociate them to the player entries
            while  ($survivor = mysql_fetch_assoc($survivor_query)) {
                $surv_id = $survivor['entry_id'];
                $team_pos = $survivor['team_position'];

                if ($team_pos == 5) {
                    //create a new knife and assign it to this player
                    $wep_name = $main_knife_data['name'];
                    $wep_type = $main_knife_data['type'];
                    $wep_base_dmg = $main_knife_data['base_dmg'];
                    $wep_modifier = $main_knife_data['modifier'];
                    $wep_durability = $main_knife_data['durability'];
                    $wep_stam_cost = $main_knife_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 4) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_knife_data['name'];
                    $wep_type = $weak_knife_data['type'];
                    $wep_base_dmg = $weak_knife_data['base_dmg'];
                    $wep_modifier = $weak_knife_data['modifier'];
                    $wep_durability = $weak_knife_data['durability'];
                    $wep_stam_cost = $weak_knife_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 3) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_club_data['name'];
                    $wep_type = $weak_club_data['type'];
                    $wep_base_dmg = $weak_club_data['base_dmg'];
                    $wep_modifier = $weak_club_data['modifier'];
                    $wep_durability = $weak_club_data['durability'];
                    $wep_stam_cost = $weak_club_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 2) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_club_data['name'];
                    $wep_type = $weak_club_data['type'];
                    $wep_base_dmg = $weak_club_data['base_dmg'];
                    $wep_modifier = $weak_club_data['modifier'];
                    $wep_durability = $weak_club_data['durability'];
                    $wep_stam_cost = $weak_club_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
            }

            //create the completed weapons at players Homebase.
            $shotgun_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'shotgun', 0, NOW(), 6)") or die(mysql_error());
            $baseball_bat_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'baseball bat', 0, NOW(), 2)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());

            array_push ($return_array, "Success");
            array_push ($return_array, "Player has successfully equipped a hunting knife");

        } elseif ($wep_choice == "club") {
            while  ($survivor = mysql_fetch_assoc($survivor_query)) {
                $surv_id = $survivor['entry_id'];
                $team_pos = $survivor['team_position'];

                if ($team_pos == 5) {
                    //create a new knife and assign it to this player
                    $wep_name = $main_club_data['name'];
                    $wep_type = $main_club_data['type'];
                    $wep_base_dmg = $main_club_data['base_dmg'];
                    $wep_modifier = $main_club_data['modifier'];
                    $wep_durability = $main_club_data['durability'];
                    $wep_stam_cost = $main_club_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 4) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_knife_data['name'];
                    $wep_type = $weak_knife_data['type'];
                    $wep_base_dmg = $weak_knife_data['base_dmg'];
                    $wep_modifier = $weak_knife_data['modifier'];
                    $wep_durability = $weak_knife_data['durability'];
                    $wep_stam_cost = $weak_knife_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 3) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_knife_data['name'];
                    $wep_type = $weak_knife_data['type'];
                    $wep_base_dmg = $weak_knife_data['base_dmg'];
                    $wep_modifier = $weak_knife_data['modifier'];
                    $wep_durability = $weak_knife_data['durability'];
                    $wep_stam_cost = $weak_knife_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 2) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_club_data['name'];
                    $wep_type = $weak_club_data['type'];
                    $wep_base_dmg = $weak_club_data['base_dmg'];
                    $wep_modifier = $weak_club_data['modifier'];
                    $wep_durability = $weak_club_data['durability'];
                    $wep_stam_cost = $weak_club_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
            }

            $twentytwo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', '.22 Revolver', 0, NOW(), 3)") or die(mysql_error());
            $sledgehammer_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'sledgehammer', 0, NOW(), 5)") or die(mysql_error());
            $sledgehammer_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'sledgehammer', 0, NOW(), 5)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());

            array_push ($return_array, "Success");
            array_push ($return_array, "Player has successfully equipped a sledghammer");


        } elseif ($wep_choice == "gun") {
             while  ($survivor = mysql_fetch_assoc($survivor_query)) {
                $surv_id = $survivor['entry_id'];
                $team_pos = $survivor['team_position'];

                if ($team_pos == 5) {
                    //create a new knife and assign it to this player
                    $wep_name = $main_gun_data['name'];
                    $wep_type = $main_gun_data['type'];
                    $wep_base_dmg = $main_gun_data['base_dmg'];
                    $wep_modifier = $main_gun_data['modifier'];
                    $wep_durability = $main_gun_data['durability'];
                    $wep_stam_cost = $main_gun_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 4) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_knife_data['name'];
                    $wep_type = $weak_knife_data['type'];
                    $wep_base_dmg = $weak_knife_data['base_dmg'];
                    $wep_modifier = $weak_knife_data['modifier'];
                    $wep_durability = $weak_knife_data['durability'];
                    $wep_stam_cost = $weak_knife_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
                if ($team_pos == 3) {
                    //create a new knife and assign it to this player
                    $wep_name = $weak_knife_data['name'];
                    $wep_type = $weak_knife_data['type'];
                    $wep_base_dmg = $weak_knife_data['base_dmg'];
                    $wep_modifier = $weak_knife_data['modifier'];
                    $wep_durability = $weak_knife_data['durability'];
                    $wep_stam_cost = $weak_knife_data['stam_cost'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_stam_cost' )") or die(mysql_error());
                }
            }

            $twentytwo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', '.22 Revolver', 0, NOW(), 3)") or die(mysql_error());
            $sledgehammer_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'sledgehammer', 0, NOW(), 5)") or die(mysql_error());
            $baseball_bat_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'baseball bat', 0, NOW(), 2)") or die(mysql_error());
            $hunting_knife_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'hunting knife', 0, NOW(), 4)") or die(mysql_error());
            $shiv_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'crude shiv', 0, NOW(), 1)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());
            $ammo_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'ammo', 0, NOW(), 0)") or die(mysql_error());

            //add 30 ammo to the player_sheet
            $ammo_update = mysql_query("UPDATE player_sheet SET ammo=ammo+30 WHERE id='$id'") or die(mysql_error());

            array_push ($return_array, "Success");
            array_push ($return_array, "Player has successfully equipped a .22 pistol");


        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "Player choice does not match options");
        }
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "Weapon choice not set");
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}
$jsonreturn = json_encode($return_array);
echo $jsonreturn;

?>