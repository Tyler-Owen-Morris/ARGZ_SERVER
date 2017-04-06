<?php
include("db_connect.php");

$return_array = array();


if ($id <> '') {
    
	$crafting_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete>NOW() ORDER BY time_complete ASC") or die(mysql_error());
	
	if (mysql_num_rows($crafting_query)>0){
		
		//delete the first entry
		$weapon_count = mysql_num_rows($crafting_query);
		$weapon_data = mysql_fetch_assoc($crafting_query);
		
		$delete_entry_id = $weapon_data[0]['entry_id'];
		$delete_query = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$delete_entry_id'") or die(mysql_error());
		if(mysql_affected_rows()){
			//move the 2nd entry to craft at now+interval
			$duration_wep2 = $weapon_data[1]['duration'];
			$wep_id_2 = $weapon_data[1]['entry_id'];
			$interval = "interval $duration_wep2 minute";
			$update_wep_2 = mysql_query("UPDATE weapon_crafting SET time_complete=Date_add(NOW(), '$interval') WHERE entry_id='$wep_id_2'") or die(mysql_error());
			if(mysql_affected_rows()){
				//go through any remaining weapons, and move their time up accordingly
				$remaining_crafting = $weapon_count-2;
				$failed_updates = 0;
				if ($remaining_crafting>0){
					$cumulative_duration += $duration_wep2;
					for ($i=2;$i < $weapon_count; $i++){
						$my_id = $weapon_data[$i]['entry_id'];
						$cumulative_duration += $weapon_data[$i]['duration'];
						$my_interval_string = "interval $cumulative_duration minute";
						$craft_update = mysql_query("UPDATE weapon_crafting SET time_complete=date_add(NOW(), '$my_interval_string') WHERE entry_id='$my_id'") or ($failed_updates++);
						
					}
					if($failed_updates<1){
						$new_data_query = mysql_query("SELECT * FROM weapon_crafting WHERE time_complete>NOW() AND id='$id' ORDER BY time_complete ASC") or die(mysql_error());
						$in_progress_array = array();
						while($item = mysql_fetch_assoc($new_data_query)){
							array_push($in_progress_array, $item);
						}
						$completed_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete<NOW() ORDER BY time_complete DESC") or die(mysql_error());
						$completed_array = array();
						while($thing = mysql_fetch_assoc($completed_query)){
							array_push($completed_array, $thing);
						}
						
						array_push($return_array, "Success");
						array_push($return_array, $in_progress_array);
						array_push($return_array, $completed_array);
						array_push($return_array, "All entries updated successfully");
						
					}else{
						array_push($return_array, "Failed");
						array_push($return_array, "Not all entries were updated successfully, $failed_updates reported as having failed");
					}
					
				}else{
					$new_data_query = mysql_query("SELECT * FROM weapon_crafting WHERE time_complete>NOW() AND id='$id' ORDER BY time_complete ASC") or die(mysql_error());
					$in_progress_array = array();
					while($item = mysql_fetch_assoc($new_data_query)){
						array_push($in_progress_array, $item);
					}
					$completed_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete<NOW() ORDER BY time_complete DESC") or die(mysql_error());
					$completed_array = array();
					while($thing = mysql_fetch_assoc($completed_query)){
						array_push($completed_array, $thing);
					}
					
					array_push($return_array, "Success");
					array_push($return_array, $in_progress_array);
					array_push($return_array, $completed_array);
					array_push($return_array, "one item removed, and it's followup successfully updated");
					
				}
				
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "Unabled to update previous entries");
			}
		}else{
			array_push($return_array, "Failed");
			array_push($return_array, "unable to delete the entry");
		}
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "User has no weapons in cue");
	}
} else {
    array_push ($return_array, "Failed");
    array_push ($return_array, "player ID not set");
}

$jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $jsonReturn;
//Homebase_CancelCurrentCraft.php
?>