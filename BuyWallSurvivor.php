<?php
    include("db_connect.php");

    $return_array = array();

    //$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
    //$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
	$buy_id = isset($_POST['buy_id']) ? protect($_POST['buy_id']) : '';
	$buy_name = isset($_POST['buy_name']) ? protect($_POST['buy_name']) : '';

    if ( $buy_id <> '' || $buy_name <> '') {
		
		//look up survivor record that we're going to copy
		$surv_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$buy_id' AND team_position=5 LIMIT 1") or die(mysql_error());
	
		$sold_survivor_data = mysql_fetch_assoc();
		$s_stam = $sold_survivor_data['base_stam'];
		$s_attk = $sold_survivor_data['base_attack'];
		$s_pic_url = $sold_survivor_data['profile_pic_url'];
		
		if (mysql_num_rows($surv_query) > 0){
			//copy the survivor to the purchasing players roster
			$surv_insert = mysql_query("INSERT INTO survivor_roster SET owner_id='$id', name='$buy_name', base_stam='$s_stam', curr_stam='$s_stam', base_attack='$s_attk', start_time=NOW(), team_position=0, paired_user_id='$buy_id', profile_pic_url='$s_pic_url'")or die(mysql_error());
			
			if (mysql_affected_rows()){
				
				//remove food/water cost from purchasing player
				$player_update = mysql_query("UPDATE player_sheet SET food=food-4, water=water-4 WHERE id='$id'") or die(mysql_error());
				if(mysql_affected_rows()){
					$day_interval_string = "interval 24 hour";
					$roster_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' AND start_time>date_sub(NOW(), $day_interval_string)") or die(mysql_error());
					
					if (mysql_num_rows($roster_query) > 0){
						$roster_data = mysql_fetch_assoc($roster_query);
						
						array_push($return_array, "Success");
						array_push($return_array, $roster_data);
						
					}else{
						array_push($return_array, "Failed");
						array_push($return_array, "unable to find players survivor roster");
					}
				}else{
					array_push($return_array, "Failed");
					array_push($return_array, "unable to subtract player resources");
				}
				
				
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "unable to create survivor record");
			}
			
		}else {
			array_push($return_array, "Failed");
			array_push($return_array, "Unable to locate survivor record for survivor being purchased");
		}

    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
//BuyWallSurvivor.php
?>