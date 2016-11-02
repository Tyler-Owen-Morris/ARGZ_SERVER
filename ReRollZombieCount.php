<?php
include("db_connect.php");
$return_array = array();

$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
$zombie_count = isset($_POST['zombie_count']) ? protect($_POST['zombie_count']) : '';

if ( $bldg_name <> '' || $bldg_id <> '' || $zombie_count <> '' ) {
    $bldg_update = mysql_query("UPDATE cleared_buildings SET zombies='$zombie_count' WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());
    if (mysql_affected_rows() > 0) {
        array_push($return_array, "Success");
        array_push($return_array, "Zombie count updated");
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "could not update cleared building data");
    }
} else { 
    array_push($return_array, "Failed");
    array_push($return_array, "Variables not set");
}
$json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
echo $json_return;
?>