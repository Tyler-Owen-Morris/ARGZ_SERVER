<?php 
    include ("db_connect.php");
    
?> 
<?php

if(isset($_POST['id'])){
    $id = protect($_POST['id']);
    $first_name = protect($_POST['first_name']);
    $last_name = protect($_POST['last_name']);
    $name = protect($_POST['name']);
    
    $supply = protect($_POST['supply']);
    $water = protect($_POST['water']);
    $food = protect($_POST['food']);
    
    $ammo = protect($_POST['ammo']);
    
    $stamina = 100;
    $attack = 15;
    $home_lat = 10.1234;
    $home_lon = 10.1234;
    //$daytime_now = date('MySQL');
    //echo $daytime_now;
    
    $register1 = mysql_query("SELECT id FROM player_sheet WHERE id='$id'")or die(mysql_error());
    
    
        
    if(strlen($id) > 20) {
        echo ("id must be less than 20 characters");    
    }elseif(!is_numeric($supply)) {
        echo ("Supply must be a number");
    }elseif(mysql_num_rows($register1) > 0) {
        //if the id is already registered, it should just overwrite the new character data into that acccount.
        $update1 = mysql_query("UPDATE player_sheet SET first_name = '$first_name', last_name = '$last_name', supply = '$supply', food = '$food', water = '$water', char_created_DateTime = NOW(), ammo = '$ammo', equipped_weapon_id = '0', curr_stamina = '$stamina', max_stamina= '$stamina' WHERE id = '$id'")or die(mysql_error());
    
        //DELETE ALL OTHER ACTIVE PLAYER DATA IN ALL OTHER TABLES.

        //1) remove all active survivors from survivor_roster
        $delete = mysql_query("DELETE FROM survivor_roster WHERE '$id' = owner_id ") or die(mysql_error());

        //2) deactivate all weapons from active_weapons 
        $delete2 = mysql_query("DELETE FROM active_weapons WHERE owner_id ='$id'") or die(mysql_error());

        //3) remove all weapons from weapon_crafting
        $delete3 = mysql_query("DELETE FROM weapon_crafting WHERE id='$id'") or die(mysql_error());

        //4) if there is a homebase, reset all data for homebase_sheet
        $home_query = mysql_query("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());
        if (mysql_num_rows($home_query) > 0 ) {
            $update2 = mysql_query("UPDATE homebase_sheet SET supply=0, knife_for_pickup=0, club_for_pickup=0, ammo_for_pickup=0, gun_for_pickup=0, active_survivor_for_pickup=0, inactive_survivors=0 WHERE id = '$id'") or die(mysql_error());
        }

        //5) Remove all cleared buildings from the cleared_buildings table
        $delete4 = mysql_query("DELETE FROM cleared_buildings WHERE id = '$id'") or die(mysql_error());

        //6) Reset all QR friendships
        $delete5 = mysql_query("DELETE FROM qr_pairs WHERE id_1='$id' OR id_2='$id'") or die(mysql_error());

        //After DELTES are complete- create the survivor entry for the player character at team position 5
        $insert2 = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, weapon_equipped, isActive, start_time, team_position, paired_user_id) VALUES ('$id', '$name', '$stamina', '$stamina', '$attack', '0', '1', NOW(), '5', '$id')") or die(mysql_error());

        echo "Success";

    } else {
        //otherwise- create an entire new user based on the post data.
        $insert1 = mysql_query("INSERT INTO player_sheet (id, first_name, last_name, char_created_DateTime, homebase_lat, homebase_lon, supply, food, water, ammo, equipped_weapon_id, curr_stamina, max_stamina) VALUES ('$id', '$first_name', '$last_name', NOW(), '$home_lat', '$home_lon', '$supply', '$food', '$water', '$ammo', '0', '$stamina', '$stamina')")or die(mysql_error());
            echo "character successfully added to the database";

        //DELETE ALL OTHER ACTIVE PLAYER DATA IN ALL OTHER TABLES.

        //1) remove all active survivors from survivor_roster
        $delete = mysql_query("DELETE FROM survivor_roster WHERE '$id' = owner_id ") or die(mysql_error());

        //2) deactivate all weapons from active_weapons 
        $delete2 = mysql_query("DELETE FROM active_weapons WHERE owner_id ='$id'") or die(mysql_error());

        //3) remove all weapons from weapon_crafting
        $delete3 = mysql_query("DELETE FROM weapon_crafting WHERE id='$id'") or die(mysql_error());

        //4) if there is a homebase, reset all data for homebase_sheet
        $home_query = mysql_query("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());
        if (mysql_num_rows($home_query) > 0 ) {
            $update2 = mysql_query("UPDATE homebase_sheet SET supply=0, knife_for_pickup=0, club_for_pickup=0, ammo_for_pickup=0, gun_for_pickup=0, active_survivor_for_pickup=0, inactive_survivor=0 WHERE id = '$id'") or die(mysql_error());
        }

        //5) Remove all cleared buildings from the cleared_buildings table
        $delete4 = mysql_query("DELETE FROM cleared_buildings WHERE id = '$id'") or die(mysql_error());

        //6) Reset all QR friendships
        $delete5 = mysql_query("DELETE FROM qr_pairs WHERE id_1='$id' OR id_2='$id'") or die(mysql_error());

         //After DELTES are complete- create the survivor entry for the player character at team position 5
        $insert2 = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, weapon_equipped, isActive, start_time, team_position, paired_user_id) VALUES ('$id', '$name', '$stamina', '$stamina', '$attack', '0', '1', NOW(), '5', '$id')") or die(mysql_error());
    }
}

// StartNewCharacter.php
?>