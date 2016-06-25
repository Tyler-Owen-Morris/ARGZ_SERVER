<?php 
include("db_connect.php");

$returnArray = array();

if (isset($_POST['id'])) {
    if (isset($_POST['lat'])) {
        if(isset($_POST['lon'])){
            $id = protect($_POST['id']);
            $lat = protect($_POST['lat']);
            $lon = protect($_POST['lon']);

            //update the player sheet with the new coordinates
            $update1 = mysql_query("UPDATE user_sheet SET homebase_lat = '$lat', homebase_lon = '$lon' WHERE id = '$id'")or die(mysql_error());
            //check to make sure there is a homebase_sheet entry for the player (otherwise we will make a new one)
            $query1 = mysql_query("SELECT * FROM homebase_sheet WHERE id='$id") or die(msql_error());
            if (mysql_num_rows($query1) == 1 ) {
                //we have found the players homebase
                array_push($returnArray, "Success");
                array_push($returnArray, "Player homebase updated, and homebase game is initialized");
                $jsonReturn = json_encode($returnArray);
                echo $jsonReturn;
            } else if (mysql_num_rows($query1) < 1) {
                //there is no entry for this players homebase
                $query2 = mysql_query("SELECT * FROM user_sheet WHERE id='$id'");
                $user_data = mysql_fetch_assoc($query2);
                $inactive_survivors = ($user_data['total_survivors'] - $user_data['active_survivors']);
                $insert1 = mysql_query("INSERT INTO homebase_sheet (id, supply, knife_for_pickup, club_for_pickup, ammo_for_pickup, gun_for_pickup, active_survivor_for_pickup, inactive_survivors) VALUES ('$id', 0, 0, 0, 0, 0, 0, '$inactive_survivors')) WHERE id='$id'") or die(mysql_error());
                array_push($returnArray, "Success");
                array_push($returnArray, "Player homebase location updated, and homebase has been initialized");
                $jsonReturn = json_encode($returnArray);
                echo $jsonReturn;
            } else if (mysql_num_rows($query1) > 1) {
                // there is more than one homebase entry for this user- major error
                array_push($returnArray, "Failed");
                array_push($returnArray, "user sheet is updated, but more than one homebase was found on DB");
                $jsonReturn = json_encode($returnArray);
                echo $jsonReturn;
            }

            array_push($returnArray, "Success");
            $jsonReturn = json_encode($returnArray);
            echo $jsonReturn;
        }else{echo("Longitude not set");}
    }else {echo("Lattitudes not set");}
} else {echo "Player ID not set";}
?>