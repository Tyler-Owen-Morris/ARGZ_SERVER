<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $bldgquery = "SELECT * FROM cleared_buildings WHERE id = '$id'";
        $bldgData = mysql_query($bldgquery);
        
        //echo "The raw sql query returned: " + $userdata;
        
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
