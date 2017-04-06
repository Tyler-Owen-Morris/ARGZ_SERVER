<?php
    include("db_connect.php");

    $return_array = array();

    $bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
    $bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';

    if ( $bldg_id <> '' || $bldg_name <> '') {
		$interval_string = "interval 24 hour";
		//
		$bldg_query = mysql_query("SELECT * FROM wall_tags WHERE bldg_name='$bldg_name' AND tag_time>date_sub(NOW(), $interval_string)") or die(mysql_error()); //Select all valid tags
		$bldg_array = array();
		
		if(mysql_num_rows($bldg_query) > 0){
			
			while ($bldg_data = mysql_fetch_assoc($bldg_query)){
				array_push($bldg_array, $bldg_data);
			}
			
		}else{
			$bldg_array = null;
		}
		array_push($return_array, "Success");
		array_push($return_array, $bldg_array);
		
           
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
//FetchWallData.php
?>