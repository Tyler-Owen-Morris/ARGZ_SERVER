<?php 
    include("db_connect.php");
?>
<?php
$return_array = array();

if (isset($_POST['id'])) {
    $id = protect($_POST['id']);

    $survivorData = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$id'") or die(mysql_error());

    if (mysql_num_rows($survivorData) > 0) {
        $survivorsArray = array();
        while ($row = mysql_fetch_assoc($survivorData)) {
            $survivor = array("owner_id" => $row['owner_id'], "survivor_id" => $row['survivor_id'], "name" => $row['name'], "base_stam" => $row['base_stam'], "curr_stam" => $row['curr_stam'], "base_attack" => $row['base_attack'], "weapon_equipped" => $row['weapon_equipped'], "isActive" => $row['isActive'], "start_time" => $row['start_time']);
            array_push($survivorsArray, $survivor);
        }
        $json_data = json_encode($survivorsArray,JSON_NUMERIC_CHECK);

    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "no survivors found");
        $json_return = json_encode($return_array);
        echo $json_return;
    }
}

?>