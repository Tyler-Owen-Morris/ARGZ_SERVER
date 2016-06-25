<?php
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])) {
    //first step is to query for an existing homebase entry.
    $id = protect($_POST['id']);
    $query1 = mysql_query ("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());

    if (mysql_num_rows($query1) > 0) {
        //if there is a homebase entry already, we check that there is only one, and then create the json containing the resume game data
        if(mysql_num_rows($query1) == 1) {
            //there is only 1 entry in the db for our players homebase, cleared to proceed.
            $row2 = mysql_fetch_assoc($query1);
            $homebase_array = array("id"=>$row2['id'], "supply"=>$row2['supply'], "knife_for_pickup"=>$row2['knife_for_pickup'], "club_for_pickup"=>$row2['club_for_pickup'], "ammo_for_pickup"=>$row2['ammo_for_pickup'], "gun_for_pickup"=>$row2['gun_for_pickup'], "active_survivor_for_pickup"=>$row2['active_survivor_for_pickup'], "inactive_survivors"=>$row2['inactive_survivors']);

            //construct and return the json package
            array_push($return_array, "Success");
            array_push($return_array, "Resuming");
            array_push($return_array, $homebase_array);
            $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $json_return;
        }else if (mysql_num_rows($query1) >= 2){
            //something is wrong that 2 entries have been created for the same user.
            array_push($return_array, "Failed");
            array_push($return_array, "More than one entry on the database- catastrophic failure");
            $json_return = json_encode($return_array);
            echo $json_return;
        }
    } else {
        //if there is not a homebase entry, we need to check if there is a player created for mobile.
        $query2 = mysql_query("SELECT * FROM user_sheet WHERE id='$id'") or die(mysql_error());

        if(mysql_num_rows($query2) > 0) {
            //if there is a player, but no homebase entry- we want to create a new homebase entry now.
            $row = mysql_fetch_assoc($query2);
            $inactive_survivors = ($row['total_survivors'] - $row['active_survivors']);
            $insert1 = mysql_query("INSERT INTO homebase_sheet (id, supply, knife_for_pickup, club_for_pickup, ammo_for_pickup, gun_for_pickup, active_survivor_for_pickup, inactive_survivors) VALUES ('$id', 0, 0, 0, 0, 0, 0, '$inactive_survivors')")or die(mysql_error());

            //create an array of the game data to return to the client in the json.
            $query3 = mysql_query ("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());
            $row2 = mysql_fetch_assoc($query3);
            $homebase_array = array("id"=>$row2['id'], "supply"=>$row2['supply'], "knife_for_pickup"=>$row2['knife_for_pickup'], "club_for_pickup"=>$row2['club_for_pickup'], "ammo_for_pickup"=>$row2['ammo_for_pickup'], "gun_for_pickup"=>$row2['gun_for_pickup'], "active_survivor_for_pickup"=>$row2['active_survivor_for_pickup'], "inactive_survivors"=>$row2['inactive_survivors']);

            //construct and return the json package
            array_push($return_array, "Success");
            array_push($return_array, "New homebase entry created");
            array_push($return_array, $homebase_array);
            $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $json_return;
        } else {
            //if no player has been created with this id on mobile- then return to the client to display that and NOT load into the game.
            array_push($return_array, "Failed");
            array_push($return_array, "No player created on mobile. Please download the app, and login with the same facebook account you wish to play with here.");
            $json_return = json_encode($return_array);
            echo $json_return;
        }
    }

} else {
    array_push($return_array, "Failed");
    array_push($return_array, "id not set");
    $json_return = json_encode($return_array);
    echo $json_return;
}

?>