<?php
    include ("db_connect.php");

    $return_array = array();

    $bldgquery = "SELECT * FROM cleared_buildings WHERE id = '$id'";
    $bldgData = $mysqli->query($bldgquery) or die($mysqli->error());
    
    
    if ($bldgData->num_rows > 0 ) {
        array_push($return_array, $bldgData->fetch_assoc());
    } else {
        array_push($return_array, null);
    }
    $json_data = json_encode($return_array);
    echo $json_data;
        
    //ClearedBuildingData.php
?>
