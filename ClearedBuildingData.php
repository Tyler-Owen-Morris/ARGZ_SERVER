<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $now = 'NOW()';
        $active_cleartime = $now;
        date_sub($active_cleartime, date_interval_create_from_date_string("20 hours"));
        $bldgquery = "SELECT * FROM cleared_buildings WHERE id = '$id' AND time_cleared>$active_cleartime";
        $bldgData = mysql_query($bldgquery);
        
        
        if (mysql_num_rows($bldgData) > 0 ) {
            $bldgDataArray = array();
            while ($row = mysql_fetch_assoc($bldgData)) {
            	$entry = array("id" => $row['id'], "bldg_name" => $row['bldg_name'], "bldg_id" => $row['bldg_id'], "active" => $row['active'], "time_cleared" => $row['time_cleared']);
            	array_push($bldgDataArray, $entry); 
            }
            $jsondata = json_encode($bldgDataArray, JSON_NUMERIC_CHECK);
                
            echo $jsondata;
        } else {
            echo "json did not encode. sql returned less than 1 result";
        }
        
    } else {
        echo "player ID not set";
    }
    //ClearedBuildingData.php
?>
