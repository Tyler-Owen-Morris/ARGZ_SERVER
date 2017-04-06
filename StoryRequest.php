<?php
    include("db_connect.php");

    $return_array = array();

    $type = isset($_POST['type']) ? $_POST['type'] : '';
	$roll = isset($_POST['roll']) ? $_POST['roll'] : '';//stored as float 0-100

    if ( $turns <> '' || $roll <> '') {
		if ($type == "run"){
			$stories_query = mysql_query("SELECT * FROM story_table WHERE story_type='run'") or die(mysql_error());
			$stories_count = mysql_num_rows($stories_query);
			if ($stories_count>0){
				$story_data = mysql_fetch_assoc($stories_query);
				
				if($stories_count >1){
					//roll the result
					$selected_story_data= array();
					$section = 100/$stories_count;
					$success=false;
					for ($i = 0; $i < $stories_count;$i++){
						if($roll<($section*$i)){
							$selected_story_data=$story_data[$i];
							$success=true;
							break;
						}
					}
					
					if($success){
						array_push($return_array, "Success");
						array_push($return_array, $selected_story_data);
					}else{
						array_push($return_array, "Failed");
						array_push($return_array, "unable to select a story with given roll");
					}
					
					
				}else{
					//otherwise just return the 1
					array_push($return_array, "Success");
					array_push($return_array, $story_data);
				}
				
				
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "unable to find matching story");
			}
		}
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "inadequate data sent");
	}
array_push($return_array, $rec_unequips);
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;

// StoryRequest.php
?>