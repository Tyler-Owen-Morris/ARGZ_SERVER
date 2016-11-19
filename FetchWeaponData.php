<?php 
include("db_connect.php");

if(isset($_POST['id'])){
    $id = protect($_POST['id']);

    $weapon_data = $mysqli->query("SELECT * FROM active_weapons WHERE owner_id='$id'") or die($mysqli->error());

    $return_array = array();
    $wepDataArr = array();

    if ($weapon_data->num_rows > 0) {
        while ($weapon = $weapon_data->fetch_assoc()) {
            array_push($wepDataArr, array("weapon_id" => $weapon['weapon_id'], "owner_id" => $weapon['owner_id'], "equipped_id" => $weapon['equipped_id'], "type" => $weapon['type'], "name" => $weapon['name'], "stam_cost" => $weapon['stam_cost'] ,"base_dmg" => $weapon['base_dmg'], "modifier" => $weapon['modifier'], "durability" => $weapon['durability']));
        }
        array_push($return_array, "Success");
        array_push($return_array, $wepDataArr);
        $jsonreturn = json_encode($return_array, JSON_NUMERIC_CHECK);
        echo $jsonreturn;
    } else {
        array_push($return_array, "Success");
        array_push($return_array, "none");
        $jsonreturn = json_encode($return_array);
        echo $jsonreturn;
    }

}else{
    echo "player ID not set";
}



?>