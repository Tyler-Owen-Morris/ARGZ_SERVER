<?php
    include ("db_connect.php");

    $return_array = array();

    $bldgquery = "SELECT * FROM cleared_buildings WHERE id = '$id'";
    $bldgData = mysql_query($bldgquery) or die(mysql_error());
    
    
    if (mysql_num_rows($bldgData) > 0 ) {
        array_push($return_array, mysql_fetch_assoc($bldgData));
    } else {
        array_push($return_array, null);
    }
    $json_data = json_encode($return_array);
    echo $json_data;
        
    //ClearedBuildingData.php
?>
