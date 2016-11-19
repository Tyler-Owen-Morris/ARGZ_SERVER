<?php 
    include("db_connect.php");
?>
<?php
$return_array = array();

if (isset($_POST['id'])) {
    $id = protect($_POST['id']);

    $survivorData = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$id' ORDER BY team_pos DESC") or die($mysqli->error());

    if ($survivorData->num_rows > 0) {
        array_push($return_array, "Success");
        $survivorsArray = array();
        while ($row = $survivorData->fetch_assoc()) {
            $survivor = array("owner_id" => $row['owner_id'], "survivor_id" => $row['survivor_id'], "name" => $row['name'], "base_stam" => $row['base_stam'], "curr_stam" => $row['curr_stam'], "base_attack" => $row['base_attack'], "weapon_equipped" => $row['weapon_equipped'], "isActive" => $row['isActive'], "start_time" => $row['start_time'], "team_pos" => $row['team_pos']);
            array_push($survivorsArray, $survivor);
        }
        $json_data = json_encode($survivorsArray,JSON_NUMERIC_CHECK);

    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "no survivors found");
        $json_return = json_encode($return_array);
    }
    echo $json_return;
}

?>