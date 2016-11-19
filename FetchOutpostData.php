<?php
include("db_connect.php");

$return_array = array();
$active_outpost_array = array();


$id = isset($_POST['id']) ? protect($_POST['id']) : '';

$active_outpost_query = $mysqli->query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time > now()") or die($mysqli->error());
$expired_outpost_query = $mysqli->query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time <= now()") or die($mysqli->error());

if ($active_outpost_query->num_rows < 1 && $expired_outpost_query->num_rows < 1) {
    array_push($return_array, "Failed");
    array_push($return_array, "User has no active or inactive outposts");
} else {
    //remove any expired entries
    if($expired_outpost_query->num_rows >0) {
        while($expired_entry = $expired_outpost_query->fetch_assoc()) {
            $outpost_id = $expired_entry['outpost_id'];
            $delete = $mysqli->query("DELETE FROM outpost_sheet WHERE owner_id = '$id' AND outpost_id='$outpost_id'") or die($mysqli->error());
        }
    }
    //construct the array of active outposts
    if($active_outpost_query->num_rows > 0) {
        while($outpost = $active_outpost_query->fetch_assoc()) {
            array_push($active_outpost_array, array("outpost_id" => $outpost['outpost_id'], "name" =>$outpost['name'], "outpost_lat" => $outpost['outpost_lat'], "outpost_lng"=>$outpost['outpost_lng'], "expire_time"=>$outpost['expire_time'], "capacity"=>$outpost['capacity']));
        }
    } else {
        array_push($active_outpost_array, "none");
    }


    array_push($return_array, "Success");
    array_push($return_array, $active_outpost_array);
}
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;

?>