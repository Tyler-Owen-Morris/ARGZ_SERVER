<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$wep_name = isset($_POST['wep_name']) ? protect($_POST['wep_name']) : '';
$cost = isset($_POST['cost']) ? protect($_POST['cost']) : '';
$duration = isset($_POST['duration']) ? protect($_POST['duration']) : '';
$index = isset($_POST['weapon_index']) ? protect($_POST['weapon_index']) : '';

if ($id <> '') {
    if($wep_name <> '') {

    //I would rather see this:
    // $weapon = mysql_query("SELECT * from weapon_list WHERE type=$type") or die(mysql_error());
    // if(mysql_num_rows() > 0) {
    //  $row = mysql_fetch_assoc($weapon);
    //  $type = $row['type'];
    //  $cost = $row['cost'];
    //  $duration = $row['duration'];

    //Subtract the cost from the homebase_sheet
    $update1 = $mysqli->query("UPDATE homebase_sheet SET supply = supply - $cost WHERE id='$id' AND supply >= $cost") or die($mysqli->error());
    if($update1->affected_rows > 0) {
        $start = 'now()'; // simply use the mysql function

        //find the last weapon from this user that haven't been completed
        $query1 = $mysqli->query("SELECT time_complete FROM weapon_crafting WHERE time_complete > NOW() AND id='$id' ORDER BY time_complete DESC LIMIT 1") or die($mysqli->error());

        if ($query1->num_rows > 0) {
            //take the 1 entry, and add this new weapon 
            $row = $query1->fetch_assoc();
            $start = "'".$row['time_complete']."'"; // we just need to add quotes around it...
        }

        $interval_string = "interval $duration minute";
        $insert1 = $mysqli->query("INSERT INTO weapon_crafting (id, type, duration, time_complete, weapon_index) VALUES ('$id', '$wep_name', '$duration', date_add($start, $interval_string), '$index')") or die($mysqli->error());
        if($insert1->affected_rows > 0 ){        
            array_push($return_array, "Success");
            array_push($return_array, "weapon added to craft cue");
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "failed to add the weapon to the crafting DB");
        }
        
    }else{
        array_push ($return_array, "Failed");
        array_push ($return_array, "player does not have enough supply");
    }
    } else {
       array_push ($return_array, "Failed");
        array_push ($return_array, "wepon name not set");
    }
} else {
    array_push ($return_array, "Failed");
    array_push ($return_array, "player ID not set");
}

$jsonReturn = json_encode($return_array);
echo $jsonReturn;


?>