<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$outpost_lat = isset($_POST['outpost_lat']) ? protect($_POST['outpost_lat']) : '';
$outpost_lng = isset($_POST['outpost_lng']) ? protect($_POST['outpost_lng']) : '';
$capacity = isset($_POST['capacity']) ? protect($_POST['capacity']) : '';
$duration = isset($_POST['duration']) ? protect($_POST['duration']) : '';

if ($id <> '') {
    if($outpost_lat <> '') {
        if ($outpost_lng <> '') {
            $start = 'now()';
            $interval_string = "interval $duration hour";

            $outpost_query = "INSERT INTO outpost_sheet (name, owner_id, outpost_lat, outpost_lng, expire_time, capacity) VALUES ('', '$id', '$outpost_lat', '$outpost_lng', date_add($start, $interval_string), '$capacity')";
            $outpost_insert = $mysqli->query($outpost_query) or die($mysqli->error());

            //$outpost_insert = mysql_query("INSERT INTO outpost_sheet (name, owner_id, latitude, longitude, expire_time, capacity) VALUES ('', '$id', '$outpost_lat', '$outpost_lng', date_add($start, $interval_string), '$capacity')") or die(mysql_error());

            if ($mysqli->affected_rows > 0) {
                array_push ($return_array, "Success");
                array_push ($return_array, "Player outpost successfully added to the database");
            } else {
                array_push ($return_array, "Failed");
                array_push ($return_array, "unable to add the entry to the database");
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