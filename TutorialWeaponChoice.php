<?php
include("db_connect.php");
$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$wep_choice = isset($_POST['weapon_selected']) ? protect($_POST['weapon_selected']) : '';

if ($id <> '') {
    if($wep_choice <> '') {
        //get the survivors and static weapons
        $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' ORDER BY team_position DESC") or die(mysql_error());
        
        $shiv_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 1") or die(mysql_error());
		$crude_club_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 2") or die(mysql_error());
		$zip_gun_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 3") or die(mysql_error());
        $shank_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 4") or die(mysql_error());
		$reinforced_club_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 5") or die(mysql_error());
		$zip_gun2_query  = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 6") or die(mysql_error());
		$basic_knife_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 7") or die(mysql_error());
		$deadly_bat_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 8") or die(mysql_error());
		$revolver_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 9") or die(mysql_error());
		$hunting_knife_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 10") or die(mysql_error());
		$sledge_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 11") or die(mysql_error());
		$shotgun_query = mysql_query("SELECT * FROM static_weapon_classes WHERE wep_id = 12") or die(mysql_error());
		
		$shiv_data=mysql_fetch_assoc($shiv_query);
		$crude_club_data = mysql_fetch_assoc($crude_club_query);
		$zip_gun_data = mysql_fetch_assoc($zip_gun_query);
		$shank_data = mysql_fetch_assoc($shank_query);
		$reinforced_club_data = mysql_fetch_assoc($reinforced_club_query);
		$zip_gun2_data = mysql_fetch_assoc($zip_gun2_query);
		$basic_knife_data = mysql_fetch_assoc($basic_knife_query);
		$deadly_bat_data = mysql_fetch_assoc($deadly_bat_query);
		$revolver_data = mysql_fetch_assoc($revolver_query);
		$hunting_knife_data =mysql_fetch_assoc($hunting_knife_query);
		$sledge_data = mysql_fetch_assoc($sledge_query);
		$shotgun_data = mysql_fetch_assoc($shotgun_query);
		

        //match the weapon to the choice.
        if ($wep_choice == "knife") {
            //give the player a knife
			while ($survivor = mysql_fetch_assoc($survivor_query)){
				$surv_id = $survivor['entry_id'];
                $team_pos = $survivor['team_position'];
				
				if($team_pos==5){
					//this is the players survivor record, use it.
					$wep_name = $basic_knife_data['name'];
                    $wep_type = $basic_knife_data['type'];
                    $wep_base_dmg = $basic_knife_data['base_dmg'];
                    $wep_modifier = $basic_knife_data['modifier'];
                    $wep_durability = $basic_knife_data['durability'];
                    $wep_stam_cost = $basic_knife_data['stam_cost'];
					$wep_miss_chance = $basic_knife_data['miss_chance'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons 
					(owner_id, equipped_id, name, type, base_dmg, modifier, durability, max_durability, miss_chance, stam_cost) VALUES 
					('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_durability', '$wep_miss_chance', '$wep_stam_cost')") or die(mysql_error());
				}
			}

            //create the completed weapons at players Homebase.
            //$shotgun_insert = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', 'shotgun', 0, NOW(), 6)") or die(mysql_error());
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

        } else if ($wep_choice=="club"){
			
			while ($survivor = mysql_fetch_assoc($survivor_query)){
				$surv_id = $survivor['entry_id'];
                $team_pos = $survivor['team_position'];
				
				if($team_pos==5){
					//this is the players survivor record, use it.
					$wep_name = $sledge_data['name'];
                    $wep_type = $sledge_data['type'];
                    $wep_base_dmg = $sledge_data['base_dmg'];
                    $wep_modifier = $sledge_data['modifier'];
                    $wep_durability = $sledge_data['durability'];
                    $wep_stam_cost = $sledge_data['stam_cost'];
					$wep_miss_chance = $sledge_data['miss_chance'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, max_durability, miss_chance, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_durability', '$wep_miss_chance', '$wep_stam_cost' )") or die(mysql_error());
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

			
        } else if ($wep_choice=="gun"){
			
			while ($survivor = mysql_fetch_assoc($survivor_query)){
				$surv_id = $survivor['entry_id'];
                $team_pos = $survivor['team_position'];
				
				if($team_pos==5){
					//this is the players survivor record, use it.
					$wep_name = $revolver_data['name'];
                    $wep_type = $revolver_data['type'];
                    $wep_base_dmg = $revolver_data['base_dmg'];
                    $wep_modifier = $revolver_data['modifier'];
                    $wep_durability = $revolver_data['durability'];
                    $wep_stam_cost = $revolver_data['stam_cost'];
					$wep_miss_chance = $revolver_data['miss_chance'];

                    $insert_wep = mysql_query("INSERT INTO active_weapons (owner_id, equipped_id, name, type, base_dmg, modifier, durability, max_durability, miss_chance, stam_cost) VALUES ('$id', '$surv_id', '$wep_name', '$wep_type', '$wep_base_dmg', '$wep_modifier', '$wep_durability', '$wep_durability', '$wep_miss_chance', '$wep_stam_cost' )") or die(mysql_error());
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