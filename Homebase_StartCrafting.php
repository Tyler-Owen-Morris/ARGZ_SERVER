<?php
include("db_connect.php");

$returnArray = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$type = isset($_POST['type']) ? protect($_POST['type']) : '';
$cost = isset($_POST['cost']) ? protect($_POST['cost']) : '';
$duration = isset($_POST['duration']) ? protect($_POST['duration']) : '';

if ($id <> '') {
    if($type <> '') {

    //I would rather see this:
    // $weapon = mysql_query("SELECT * from weapon_list WHERE type=$type") or die(mysql_error());
    // if(mysql_num_rows() > 0) {
    //  $row = mysql_fetch_assoc($weapon);
    //  $type = $row['type'];
    //  $cost = $row['cost'];
    //  $duration = $row['duration'];

    //Subtract the cost from the homebase_sheet
    $update1 = mysql_query("UPDATE homebase_sheet SET supply = supply - $cost WHERE id='$id' AND supply >= $cost") or die(mysql_error());
    if(mysql_affected_rows() > 0) {
        $start = 'now()'; // simply use the mysql function

        //find all other weapons from this user that haven't been completed
        $query1 = mysql_query("SELECT time_complete FROM weapon_crafting WHERE time_complete > NOW() AND id='$id' ORDER BY time_complete DESC LIMIT 1") or die(mysql_error());

        if (mysql_num_rows($query1) > 0) {
            //take the 1 entry, and add this new weapon 
            $row = mysql_fetch_assoc($query1);
            $start = "'".$row['time_complete']."'"; // we just need to add quotes around it...
        }
        $interval_string = "interval $duration minute";
        $insert1 = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete) VALUES ('$id', '$type', '$duration', date_add($start, $interval_string ))") or die(mysql_error());
                
        $returnArray = array('Success', 'Weapon added to craft cue');
        $jsonReturn = json_encode($returnArray);
        echo $jsonReturn;
    }else{
        $returnArray = array('Failed', 'type of weapon not sent');
        $jsonReturn = json_encode($returnArray);
        echo $jsonReturn;
    }
    } else {
        echo "Weapon Type not set";
    }
} else {
    echo "Player ID not set";
}


// include("db_connect.php");

// $return_array = array();

// if (isset($_POST['id'])) {
//     if(isset($_POST['type'])) {
//         $id = protect($_POST['id']);
//         $type = protect($_POST['type']);
//         $cost = protect($_POST['cost']);
//         $duration = protect($_POST['duration']);
//         $new_supply = protect($_POST['new_supply']);

//         $start = 'now()'; // simply use the mysql function

//         //find all other weapons from this user that haven't been completed
//         $query1 = mysql_query("SELECT time_complete FROM weapon_crafting WHERE time_complete > NOW() AND id='$id' ORDER BY time_complete DESC LIMIT 1") or die(mysql_error());

//         if (mysql_num_rows($query1) > 0) {
//             //take the 1 entry, and add this new weapon 
//             $row = mysql_fetch_assoc($query1);
//             $start = "'".$row['time_complete']."'"; // we just need to add quotes around it...
//         }
//         $interval_string = "interval $duration minute";
//         $insert1 = mysql_query("INSERT INTO weapon_crafting (id, type, duration, time_complete) VALUES ('$id', '$type', '$duration', date_add($start, $interval_string ))") or die(mysql_error());
        
//         //Subtract the cost from the homebase_sheet
//         if ($new_supply >= 0) {
//             $update1 = mysql_query("UPDATE homebase_sheet SET supply = supply - $cost WHERE id='$id'") or die(mysql_error());
//         }
//         array_push($return_array, "Success");
//         array_push($return_array, "Weapon added to craft cue");
//         $jsonReturn = json_encode($return_array);
//         echo $jsonReturn;
//     }else{
//         array_push($return_array, "Failed");
//         array_push($return_array, "type of weapon not sent");
//         $jsonReturn = json_encode($return_array);
//         echo $jsonReturn;
//     }
// } else {
//     echo "Player ID not set";
// }
?>