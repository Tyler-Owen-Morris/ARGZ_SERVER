<?php 
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])){
    $id = protect($_POST['id']);
    $query1 = mysql_query("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());

    if (mysql_num_rows($query1) == 1 ) {
        $row = mysql_fetch_assoc($query1);
        $supply = $row['supply'];
        $knife_for_pickup = $row['knife_for_pickup'];
        $club_for_pickup = $row['club_for_pickup'];
        $ammo_for_pickup = $row['ammo_for_pickup'];
        $gun_for_pickup = $row['gun_for_pickup'];
        $active_survivor_for_pickup = $row['active_survivor_for_pickup'];
        $inactive_survivor = $row['inactive_survivors'];
        
        $status_array = array("supply" => $supply, "knife_for_pickup" => $knife_for_pickup, "club_for_pickup" => $club_for_pickup, "ammo_for_pickup" => $ammo_for_pickup, "gun_for_pickup" => $gun_for_pickup, "active_survivor_for_pickup" => $active_survivor_for_pickup, "inactive_survivors" => $inactive_survivor);

        array_push($return_array, "Success");
        array_push($return_array, $status_array);
        $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
        echo $jsonReturn;
    } else {
        //I will write this to return the exception later, for now I'm assuming there is only 1 entry or nothing
        array_push($return_array, "Failed");
        array_push($return_array, "More than one user was found matching that ID");
        $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
        echo $jsonReturn;
    }


} else {
    echo "id not set";
}

?>