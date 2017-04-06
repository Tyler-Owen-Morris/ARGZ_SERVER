<?php
    include("db_connect.php");

    $return_array = array();

    $turns = isset($_POST['turns']) ? $_POST['turns'] : '';
	$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
	$clear = isset($_POST['clear']) ? protect($_POST['clear']) : '';

    if ( $turns <> '' ) {
		
		$turns_json = json_decode($turns, true);
		
		$weapons_updated=0;
		$survivor_updated=0;
		$bldg_updated=0;
		
		for ( $i=0; $i<sizeof($turns_json);$i++) {
			$type = $turns_json[$i]['attackType'];
			$surv_id = $turns_json[$i]['survivor_id'];
			$wep_id = $turns_json[$i]['weapon_id'];
			$ded = $turns_json[$i]['dead'];
			
			//determine the type of attack
			if ($type =="survivor"){
				//get the weapon used
                $weapon_query = mysql_query("SELECT * FROM active_weapons WHERE owner_id='$id' AND weapon_id='$wep_id'") or die(mysql_error());
                $row = mysql_fetch_assoc($weapon_query); //current weapon data
                $stam_cost = $row['stam_cost'];
                //this query allows for negative stamina numbers
                $stamina_update1 = mysql_query("UPDATE survivor_roster SET curr_stam=curr_stam-$stam_cost WHERE entry_id='$surv_id' AND owner_id='$id'") or die(mysql_error());
				if(mysql_affected_rows()){
					$survivor_updated++;
				}
				

                //subtract durability from the weapon
                $durability = $row['durability'];
                if ($durability-1 <= 0) {
                    //if the weapon is out of durability on this swing, remove it from the DB, it's destoyed
                    $delete = mysql_query("DELETE FROM active_weapons WHERE owner_id='$id' AND weapon_id='$wep_id' LIMIT 1") or die(mysql_error());
                } else {
                    $durability_update = mysql_query("UPDATE active_weapons SET durability=durability-1 WHERE weapon_id='$wep_id' AND owner_id='$id'") or die(mysql_error());
                }
				if(mysql_affected_rows()){
					$weapons_updated=$weapons_updated+1;
				}

                //if it's a gun, deal with the ammo reduction
                $wep_type = $row['type'];
                if ($type == "gun") {
                    $ammo_update = mysql_query("UPDATE player_sheet SET ammo=ammo-1 WHERE id='$id' AND ammo > 0") or die(mysql_error());
                    //the damage returned by the client will adjust for no-ammo swings.
                }
				
				//if player has killed a zombie- update the bldg record, and the player stats
				if ($ded =="1" && $clear == 0){
					$plyr_update = mysql_query("UPDATE player_sheet SET zombies_killed=zombies_killed+1 WHERE id='$id'") or die(mysql_error());
					$bldg_update = mysql_query("UPDATE cleared_buildings SET zombies=zombies-1 WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
					if(mysql_affected_rows()){
						$bldg_updated=$bldg_update+1;
					}
				}
				
				
			}else if ($type == "zombie"){
				//just remove player stamina and the move on.
				$player_update = mysql_query("UPDATE survivor_roster SET curr_stam=curr_stam-5 WHERE entry_id='$surv_id' AND owner_id='$id'")or die(mysql_error());
			}else if ($type == "fullwatch") {
				$survivor_update = mysql_query("UPDATE survivor_roster SET curr_stam=base_stam WHERE entry_id='$surv_id' AND owner_id='$id'") or die(mysql_error());
			}else if ($type == "partialwatch"){
				//do nothing...?
				// this should just ensure that they are NOT killed, no stam is restored. just don't delete the entry.
			}
		}
		$result_array = array("surv"=>$survivor_updated,"wep"=>$weapons_updated,"bldg"=>$bldg_updated);
		array_push($return_array, "Success");
		array_push($return_array, "Server has assimilated attack data");
		array_push($return_array, $result_array);
		
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
//PostTurns.php
?>