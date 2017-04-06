<?php
    include("db_connect.php");

    $return_array = array();

    $bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
    $bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
	$public_name = isset($_POST['public_name']) ? protect($_POST['public_name']) : '';

    if ( $bldg_name <> '' || $bldg_id <> '' || $public_name <> '') {
		
		//check previous query
		$existing_query = mysql_query("SELECT * FROM wall_tags WHERE bldg_name='$bldg_name' AND player_id='$id'") or die(mysql_error());
		
		$query_string = "";
		if (mysql_num_rows($existing_query) > 0){
			//update
			$query_string = "UPDATE wall_tags SET player_name='$public_name', bldg_id='$bldg_id', tag_time=NOW() WHERE player_id='$id' AND bldg_name='$bldg_name'";
		}else{
			//just create a new query
			$query_string = "INSERT INTO wall_tags SET player_name='$public_name', bldg_name='$bldg_name', bldg_id='$bldg_id', player_id='$id', tag_time=NOW()";
		}
		$entry_ = mysql_query($query_string) or die(mysql_error());
		
		if (mysql_affected_rows()){
			
			$tags_array = array();
			$tags_query = mysql_query("SELECT * FROM wall_tags WHERE player_id='$id'") or die(mysql_error());
			
			if (mysql_num_rows($tags_query)>0){
				while ($tags_data = mysql_fetch_assoc($tags_query)){
					array_push($tags_array, $tags_data);
				}
				
				array_push($return_array, "Success");
				array_push($return_array, $tags_array);
				
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "unable to query current wall tags");
			}
		}
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
//PostNameToWall.php
?>