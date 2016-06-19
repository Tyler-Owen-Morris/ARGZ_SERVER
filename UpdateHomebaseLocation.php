<?php 
include("db_connect.php");

$returnArray = array();

if (isset($_POST['id'])) {
    if (isset($_POST['lat'])) {
        if(isset($_POST['lon'])){
            $id = protect($_POST['id']);
            $lat = protect($_POST['lat']);
            $lon = protect($_POST['lon']);

            $update1 = mysql_query("UPDATE user_sheet SET homebase_lat = '$lat', homebase_lon = '$lon' WHERE id = '$id'")or die(mysql_error());
            array_push($returnArray, "Success");
            $jsonReturn = json_encode($returnArray);
            echo $jsonReturn;
        }else{echo("Longitude not set");}
    }else {echo("Lattitudes not set");}
} else {echo "Player ID not set";}
?>