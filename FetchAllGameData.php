<?php
    include("db_connect.php");

    $return_array = array();

    //evaluate eating/drinking
    $meals_query = $mysqli->query("SELECT FLOOR(HOUR(TIMEDIFF(NOW(), char_created_DateTime))/6) as total_meals, meals, food, water, char_created_DateTime FROM player_sheet WHERE id='$id'") or die($mysqli->error());
    $survivor_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$id' AND dead=0") or die($mysqli->error());
    $meals_data = $meals_query->fetch_assoc();
    $survivor_data = $survivor_query->fetch_assoc();
    $curr_meals = intval($meals_data["meals"]);
    $total_meals = intval($meals_data["total_meals"]);
    $food_count = intval($meals_data["food"]);
    $water_count = intval($meals_data["water"]);
    $survivor_count = $survivor_query->num_rows;
    $meals_to_process = $total_meals - $curr_meals;
    $ima_zombie = 0;
    $created_time = $meals_data["char_created_DateTime"];
    $game_over_score_hrs = 0;

    $death_data_array = array();

    if ($meals_to_process > 0){
        for ( ;$meals_to_process > 0; $meals_to_process--) {
            
            $food_count = $food_count-$survivor_count;
            $water_count = $water_count-$survivor_count;
            if (($food_count <= $survivor_count*-12) || ($water_count <= $survivor_count*-8)) {
                if($survivor_count > 0) {
                    $survivor_count--;
                    var_dump($survivor_count);
                    $who_to_kill_query = $mysqli->query("SELECT entry_id FROM survivor_roster WHERE owner_id='$id' AND dead=0 ORDER BY entry_id DESC LIMIT 1") or die($mysqli->error());
                    $who_to_kill_data = $who_to_kill_query->fetch_assoc();
                    $entry_id = $who_to_kill_data["entry_id"];
                    $death_query = $mysqli->query("UPDATE survivor_roster SET dead=1 WHERE owner_id='$id' AND entry_id='$entry_id'") or die($mysqli->error());
                } else {
                    $ima_zombie = 1;
                    $total_meals = $total_meals - $meals_to_process;
                    $meals_to_process = 0;
                    $game_over_score_hrs = $total_meals * 6;
                }
            }
        }

        //update the new player food/water values w/ minimums
        if($food_count < $survivor_count * -12) $food_count = $survivor_count * -12;
        if($water_count < $survivor_count * -8) $water_count = $survivor_count * -8;

        //update the new food and water values to the player sheet.
        if ($game_over_score_hrs == 0) {
            $temp_string = "UPDATE player_sheet SET food='$food_count', water='$water_count', meals='$total_meals' WHERE id='$id'";
            $meal_update = $mysqli->query($temp_string) or die($temp_string . $mysqli->error());
        } else {
            $interval_string = "interval ".$game_over_score_hrs." hour";
            $temp_string = "UPDATE player_sheet SET food='$food_count', water='$water_count', meals='$total_meals', isZombie='$ima_zombie', game_over_datetime=date_add(char_created_DateTime, $interval_string) WHERE id='$id' AND isZombie=0";
            $game_over_update = $mysqli->query($temp_string) or die($temp_string . $mysqli->error());
        }
    }


	//player data
    $player_query = $mysqli->query("SELECT * FROM player_sheet WHERE id='$id'") or die($mysqli->error());
    $player_data_array = $player_query->fetch_assoc();

	//evaluate offline stamina regen
	$survivor_array = array();//for returning regen'd charcters by ID
	$minutes_query = $mysqli->query("SELECT FLOOR(MINUTE(TIMEDIFF(NOW(), last_stamina_regen))/2) as stam_regen_ticks FROM player_sheet WHERE id='$id'") or die($mysqli->error());
	$hours_query = $mysqli->query("SELECT FLOOR(HOUR(TIMEDIFF(NOW(), last_stamina_regen))) as stam_regen_hr FROM player_sheet WHERE id='$id'") or die($mysqli->error());
	$minutes_data = $minutes_query->fetch_assoc();
	$hours_data = $hours_query->fetch_assoc();
	$offline_regen_ticks = intval($minutes_data['stam_regen_ticks']);
	$offline_regen_hrs = intval($hours_data['stam_regen_hr']);
	array_push($survivor_array, $offline_regen_hrs." hrs offline, and ".$offline_regen_ticks." Ticks");

	if ($offline_regen_hrs >= 1) { 
		//1hr = 240 stamina... set all to full
		$stamina_update = $mysqli->query("UPDATE survivor_roster SET curr_stam=base_stam WHERE owner_id='$id'") or die($mysqli->error());
		array_push($survivor_array, "all survivors set to full stamina");
		$player_update = $mysqli->query("UPDATE player_sheet SET last_stamina_regen=NOW() WHERE id='$id'") or die($mysqli->query());
		
	}else if ($offline_regen_ticks>=1){
		$stam_to_regen = $offline_regen_ticks*4;
		//Get all of the survivors that belong to the player
		$survivor_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$id'") or die($mysqli->error());

		//Loop through them for survivors w/o full stamina
		while ($survivor_data = $survivor_query->fetch_assoc()) {
			$max_stam = $survivor_data['base_stam'];
			$curr_stam = $survivor_data['curr_stam'];
			$entry_id = $survivor_data['entry_id'];
			//if the survivor is not full.
			if ($curr_stam < $max_stam) {
				if ($curr_stam+$stam_to_regen > $max_stam) {
					$update = $mysqli->query("UPDATE survivor_roster SET curr_stam=base_stam WHERE entry_id='$entry_id' AND owner_id='$id'") or die($mysqli->error());
					array_push($survivor_array, $entry_id." Set to full");
				} else {
					$new_stam= $curr_stam+$stam_to_regen;
					$update = $mysqli->query("UPDATE survivor_roster SET curr_stam='$new_stam' WHERE entry_id='$entry_id' AND owner_id='$id'") or die($mysqli->error());
					array_push($survivor_array, $entry_id." Set to ".$new_stam." stam");
				}
			}
		}
		$player_update = $mysqli->query("UPDATE player_sheet SET last_stamina_regen=NOW() WHERE id='$id'") or die($mysqli->query());
	}
	
	
	array_push($survivor_array, "Calculated ".$offline_regen_ticks." offline regen ticks for a total stam of: ".$offline_regen_ticks*4);
	


    //evaluate injury data
    $injury_query = $mysqli->query("SELECT * FROM injury_table WHERE owner_id='$id' AND expire_time<Now()") or die($mysqli->error());
    if ($injury_query->num_rows > 0) {
        while($expired_injury = $injury_query->fetch_assoc()){
            $surv_id = $expired_injury['survivor_id'];
            $stam_loss = $expired_injury['stam_loss'];
            $attk_loss = $expired_injury['attk_loss'];
            //update the survivor roster w/ permenant effects
            $survivor_activate = $mysqli->query("UPDATE survivor_roster SET injured=0, isActive=1, base_attack=base_attack-$attk_loss, base_stam=base_stam-$stam_loss WHERE owner_id='$id' AND entry_id='$surv_id'") or die($mysqli->error());
        }
        //remove expired entries
        $injury_delete = $mysqli->query("DELETE FROM injury_table WHERE owner_id='$id' AND expire_time<now()") or die($mysqli->error());
    }
    $active_injury_array = array();
    $injury_query1 = $mysqli->query("SELECT * FROM injury_table WHERE owner_id='$id' AND expire_time>now()") or die($mysqli->error());
    if ($injury_query1->num_rows > 0) {
        array_push($active_injury_array, $injury_query1);
    } else {
        $active_injury_array = null;
    }
	

    //survivor data
    //First get team. Correct is missing team members...
    $team_pos = 5;
    $survivor_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND team_position > 0 ORDER BY team_position DESC") or die($mysqli->error());
    $survivor_data_array = array();
    $survivor_count = $survivor_query->num_rows;
    if ($survivor_query->num_rows > 0) {
        while ($survivor = $survivor_query->fetch_assoc()) {
            if($survivor['team_position'] != $team_pos) {
                $survivor['team_position'] = $team_pos;
                $mysql->query("UPDATE survivor_roster SET team_position=".$survivor['team_position']." WHERE entry_id=".$survivor['entry_id']) or die($mysqli->error());
            }
            array_push($survivor_data_array, $survivor);
            $team_pos--;
        }
        if($team_pos > 0) {
            $survivor_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND team_position = 0 AND onMission = 0 LIMIT $team_pos") or die($mysqli->error());
            while ($survivor = $survivor_query->fetch_assoc()) {
                $survivor['team_position'] = $team_pos;
                $mysqli->query("UPDATE survivor_roster SET team_position=".$survivor['team_position']." WHERE entry_id=".$survivor['entry_id']);
                array_push($survivor_data_array, $survivor);
                $team_pos--;
            }
        }
    }
    //Select everyone else...
    $survivor_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND team_position = 0 ORDER BY onMission ASC") or die($mysqli->error());
    $survivor_count += $survivor_query->num_rows;
    if ($survivor_query->num_rows > 0)
        while ($survivor = $survivor_query->fetch_assoc())
            array_push($survivor_data_array, $survivor);

    if($survivor_count == 0)
        $survivor_data_array = null;

    //weapon data
    $weapon_data = $mysqli->query("SELECT * FROM active_weapons WHERE owner_id='$id'") or die($mysqli->error());
    $weapon_data_array = array();
    if ($weapon_data->num_rows > 0) {
        while ($weapon = $weapon_data->fetch_assoc()) 
            array_push($weapon_data_array, $weapon);
    } else {
        $weapon_data_array = null;
    }
    
    //cleared building data
    $bldg_activate = $mysqli->query("UPDATE cleared_buildings SET active=1 WHERE time_cleared<date_sub(now(), interval 12 hour) AND id='$id'") or die($mysqli->error());
    $bldg_data = $mysqli->query("SELECT * FROM cleared_buildings WHERE id = '$id'");
    $bldg_data_array = array();
    if ($bldg_data->num_rows > 0) {
        while ($bldg = $bldg_data->fetch_assoc()) 
            array_push($bldg_data_array, $bldg);
    } else {
        $bldg_data_array = null;
    }

    //outpost data
    $active_outpost_data = $mysqli->query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time > now()") or die($mysqli->error());
    $active_outpost_array = array();
    if ($active_outpost_data->num_rows > 0){
        while($outpost = $active_outpost_data->fetch_assoc())
            array_push($active_outpost_array, $outpost);
    }else{
        $active_outpost_array = null;
    }

    //mission data
    $mission_data = $mysqli->query("SELECT * FROM missions_table WHERE owner_id='$id' ORDER BY time_complete DESC") or die($mysqli->error());
    $mission_data_array = array();
    if ($mission_data->num_rows > 0) {
        while($mission = $mission_data->fetch_assoc())
            array_push($mission_data_array, $mission);
    } else {
        $mission_data_array = null;
    }

    //death data
    $death_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND dead=1 AND onMission=0") or die($mysqli->error());
    $death_data_array = array();
    if ($death_query->num_rows > 0) {
        while ($death = $death_query->fetch_assoc()) 
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
	array_push($return_array, $survivor_array);//this is actually stamina regenerated survivor array

    echo json_encode($return_array, JSON_NUMERIC_CHECK);
?>
