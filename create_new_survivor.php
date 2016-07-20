<?php 
    include("db_connect.php");

?>
<?php

$returnArray = array();

if(isset($_POST['owner_id'])){
    $id = protect($_POST['owner_id']);
    $survivor_id = protect($_POST['survivor_id']);
    $name = protect($_POST['name']);
    $base_stam = protect($_POST['base_stam']);
    $curr_stam = protect($_POST['curr_stam']);
    $base_attk = protect($_POST['base_attack']);
    $wep_equipped = protect($_POST['weapon_equipped']);

    $register1 = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id' AND survivor_id = '$survivor_id'")or die(mysql_error());

    if(!is_numeric($base_stam)) {
        array_push($returnArray, "failed");
        array_push($returnArray, "base_stam not numeric");
        $json_return = json_encode($returnArray);
        echo $json_return;
    } elseif(!is_numeric($curr_stam)) {
        array_push($returnArray, "failed");
        array_push($returnArray, "curr stam not numeric");
        $json_return = json_encode($returnArray);
        echo $json_return;
    }elseif(!is_numeric($base_attk)) {
        array_push($returnArray, "failed");
        array_push($returnArray, "attack not numeric");
        $json_return = json_encode($returnArray);
        echo $json_return;
    }elseif(mysql_num_rows($register1) > 0){
        //in the case that the survivor already exists
        $insert = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, weapon_equipped, isActive, start_time) VALUES ('$id', '$name', '$base_stam', '$curr_stam', '$base_attk', '$wep_equipped', 1, NOW())") or die(mysql_error());
        array_push($returnArray, "Success");
        $json_return = json_encode($returnArray);
        echo $json_return;
    } else {
        //create new entry on the DB
        $insert = mysql_query("INSERT INTO survivor_roster (owner_id, survivor_id, name, base_stam, curr_stam, base_attack, weapon_equipped, isActive, start_time) VALUES ('$id', '$survivor_id','$name', '$base_stam', '$curr_stam', '$base_attk', '$wep_equipped', 1, NOW())") or die(mysql_error());
        array_push($returnArray, "Success");
        $json_return = json_encode($returnArray);
        echo $json_return;
    }
} else {
    array_push($returnArray, "failed");
    array_push($returnArray, "id not set");
    $json_return = json_encode($returnArray);
    echo $json_return;
}