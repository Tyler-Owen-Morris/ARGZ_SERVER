<?php
include("db_connect.php");

$return_array = array();
$active_outpost_array = array();


$id = isset($_POST['id']) ? protect($_POST['id']) : '';

$active_outpost_query = mysql_query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time > now()") or die(mysql_error());
$expired_outpost_query = mysql_query("SELECT * FROM outpost_sheet WHERE owner_id = '$id' AND expire_time <= now()") or die(mysql_error());

if (mysql_num_rows($active_outpost_query) < 1 && mysql_num_rows($expired_outpost_query) < 1) {
    array_push($return_array, "Failed");
    array_push($return_array, "User has no active or inactive outposts");
} else {
    //remove any expired entries
    if(mysql_num_rows($expired_outpost_query) >0) {
        while($expired_entry = mysql_fetch_assoc($expired_outpost_query)) {
            $outpost_id = $expired_entry['outpost_id'];
            $delete = mysql_query("DELETE FROM outpost_sheet WHERE owner_id = '$id' AND outpost_id='$outpost_id'") or die(mysql_error());
        }
    }
    //construct the array of active outposts
    if(mysql_num_rows($active_outpost_query) > 0) {
        while($outpost = mysql_fetch_assoc($active_outpost_query)) {
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