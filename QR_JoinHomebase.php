<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$homebase_owner_id = isset($_POST['homebase_owner_id']) ? protect($_POST['homebase_owner_id']) : '';
$outpost_lat = isset($_POST['base_lat']) ? protect($_POST['base_lat']) : '';
$outpost_lng = isset($_POST['base_lng']) ? protect($_POST['base_lng']) : '';

if ($id <> '') {
    if($outpost_lat <> '') {
        if ($outpost_lng <> '') {
            $start = 'NOW()';
            $interval_string = "interval 24 hour";
            //find out if this player has added this homebase already
            $existing_query = mysql_query("SELECT * FROM outpost_sheet WHERE owner_id='$id' AND homebase_owner_id='$homebase_owner_id'") or die(mysql_error());
            if(mysql_affected_rows($existing_query) > 0) {
                //update the existing entry
                $update_query = "UPDATE outpost_sheet SET expire_time=date_add($start, $interval_string), outpost_lat='$outpost_lat', outpost_lng='$outpost_lng' WHERE owner_id='$id' AND homebase_owner_id='$homebase_owner_id'";
                $update_outpost = mysql_query($update_query) or die(mysql_error());
            } else {
                //create a new entry
                $insert_query = "INSERT INTO outpost_sheet (owner_id, outpost_lat, outpost_lng, expire_time, capacity, homebase_owner_id) VALUES ('$id', '$outpost_lat', '$outpost_lng', date_add($start, $interval_string), 0, '$homebase_owner_id')";
                $insert_outpost = mysql_query($insert_query) or die(mysql_error());
            }

            if (mysql_affected_rows() > 0 ) {
                array_push($return_array, "Success");
                array_push($return_array, "Homebase successfullly added as a 24hr player outpost");
            }
        
        }else{
            array_push ($return_array, "Failed");
            array_push ($return_array, "Outpost longitue not set");
        }
    }else{
        array_push ($return_array, "Failed");
        array_push ($return_array, "outpost lat not set");
    }
} else {
    array_push ($return_array, "Failed");
    array_push ($return_array, "player ID not set");
}

$jsonReturn =  json_encode($return_array);
echo $jsonReturn;

?>