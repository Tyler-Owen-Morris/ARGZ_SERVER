<?php
    include("db_connect.php");

    $return_array = array();

    //evaluate eating/drinking
    $meals_query = mysql_query("SELECT FLOOR(HOUR(TIMEDIFF(NOW(), char_created_DateTime))/6) as total_meals, meals, food, water FROM player_sheet WHERE id='$id'");
    $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' AND dead=0");
    $meals_data = mysql_fetch_assoc($meals_query);
    $survivor_data = mysql_fetch_assoc($survivor_query);
    $curr_meals = intval($meals_data["meals"]);
    $total_meals = intval($meals_data["total_meals"]);
    $food_count = intval($meals_data["food"]);
    $water_count = intval($meals_data["water"]);
    $survivor_count = mysql_num_rows($survivor_query);
    $meals_to_process = $total_meals - $curr_meals;
    $ima_zombie = 0;

    $death_data_array = array();

    if ($meals_to_process > 0){
        for ( ;$meals_to_process > 0; $meals_to_process--) {
            
            $food_count = $food_count-$survivor_count;
            $water_count = $water_count-$survivor_count;
            if (($food_count <= $survivor_count*-12) || ($water_count <= $survivor_count*-8)) {
                if($survivor_count > 0) {
                    $survivor_count--;
                    var_dump($survivor_count);
                    $who_to_kill_query = mysql_query("SELECT entry_id FROM survivor_roster WHERE owner_id='$id' AND dead=0 ORDER BY entry_id DESC LIMIT 1") or die(mysql_error());
                    $who_to_kill_data = mysql_fetch_assoc($who_to_kill_query);
                    $entry_id = $who_to_kill_data["entry_id"];
                    $death_query = mysql_query("UPDATE survivor_roster SET dead=1 WHERE owner_id='$id' AND entry_id='$entry_id'") or die(mysql_error());
                } else {
                    $ima_zombie = 1;
                    $total_meals = $total_meals - $meals_to_process;
                    $meals_to_process = 0;
                }
            }
        }

        //update the new player food/water values w/ minimums
        if($food_count < $survivor_count * -12) $food_count = $survivor_count * -12;
        if($water_count < $survivor_count * -8) $water_count = $survivor_count * -8;

        //update the new food and water values to the player sheet.
        $temp_string = "UPDATE player_sheet SET food='$food_count', water='$water_count', meals='$total_meals', isZombie='$ima_zombie' WHERE id='$id'";
        $meal_update = mysql_query($temp_string) or die(mysql_error());
    }

    //evaluate injury data
    $injury_query = mysql_query("SELECT * FROM injury_table WHERE owner_id='$id' AND expire_time<Now()") or die(mysql_error());
    if (mysql_num_rows($injury_query) > 0) {
        while($expired_injury = mysql_fetch_assoc($injury_query)){
            $surv_id = $expired_injury['survivor_id'];
            $stam_loss = $expired_injury['stam_loss'];
            $attk_loss = $expired_injury['attk_loss'];
            //update the survivor roster w/ permenant effects
            $survivor_activate = mysql_query("UPDATE survivor_roster SET injured=0, isActive=1, base_attack=base_attack-$attk_loss, base_stam=base_stam-$stam_loss WHERE owner_id='$id' AND entry_id='$surv_id'") or die(mysql_error());
        }
        //remove expired entries
        $injury_delete = mysql_query("DELETE FROM injury_table WHERE owner_id='$id' AND expire_time<now()") or die(mysql_error());
    }
    $injury_query1 = mysql_query("SELECT * FROM injury_table WHERE owner_id='$id' AND expire_time>now()") or die(mysql_error());
    $active_injury_array = mysql_fetch_assoc($injury_query1);

    //player data
    $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
    $player_data_array = mysql_fetch_assoc($player_query);

    //survivor data
    $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id = '$id' ORDER BY team_position DESC, onMission ASC") or die(mysql_error());
    $survivor_data_array = array();
    if (mysql_num_rows($survivor_query) > 0) {
        while ($survivor = mysql_fetch_assoc($survivor_query)) 
            array_push($survivor_data_array, $survivor);
    } else {
        $survivor_data_array = null;
    }

    //weapon data
    $weapon_data = mysql_query("SELECT * FROM active_weapons WHERE owner_id='$id'") or die(mysql_error());
    $weapon_data_array = array();
    if (mysql_num_rows($weapon_data) > 0) {
        while ($weapon = mysql_fetch_assoc($weapon_data)) 
            array_push($weapon_data_array, $weapon);
    } else {
        $weapon_data_array = null;
    }
    
    //cleared building data
    $active_cleartime = 'now()';
    date_sub($active_cleartime, date_interval_create_from_date_string("20 hours"));
    $bldg_data = mysql_query("SELECT * FROM cleared_buildings WHERE id = '$id' AND time_cleared>$active_cleartime");
    $bldg_data_array = array();
    if (mysql_num_rows($bldg_data) > 0) {
        while ($bldg = mysql_fetch_assoc($bldg_data)) 
            array_push($bldg_data_array, $bldg); 
    } else {
        $bldg_data_array = null;
    }

    //outpost data
    $active_outpost_data = mysql_query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time > now()") or die(mysql_error());
    $active_outpost_array = array();
    if (mysql_num_rows($active_outpost_data) > 0){
        while($outpost = mysql_fetch_assoc($active_outpost_data))
            array_push($active_outpost_array, $outpost);
    }else{
        $active_outpost_array = null;
    }

    //mission data
    $mission_data = mysql_query("SELECT * FROM missions_table WHERE owner_id='$id' ORDER BY time_complete DESC") or die(mysql_error());
    $mission_data_array = array();
    if (mysql_num_rows($mission_data) > 0) {
        while($mission = mysql_fetch_assoc($mission_data))
            array_push($mission_data_array, $mission);
    } else {
        $mission_data_array = null;
    }

    //death data
    $death_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND dead=1 AND onMission=0") or die(mysql_error());
    $death_data_array = array();
    if (mysql_num_rows($death_query) > 0) {
        while ($death = mysql_fetch_assoc($death_query)) 
            array_push($death_data_array, $death);
    } else {
        $death_data_array = null;
    }

    //assemble the array
    array_push($return_array, "Success");
    array_push($return_array, $player_data_array);
    array_push($return_array, $survivor_data_array);
    array_push($return_array, $weapon_data_array);
    array_push($return_array, $bldg_data_array);
    array_push($return_array, $active_outpost_array);
    array_push($return_array, $mission_data_array);
    array_push($return_array, $death_data_array);
    array_push($return_array, $active_injury_array);

    echo json_encode($return_array, JSON_NUMERIC_CHECK);
?>
