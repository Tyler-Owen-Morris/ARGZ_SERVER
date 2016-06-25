<?php
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])) {
    $id = protect($_POST['id']);

    //first we check to be sure there is a homebase created for the user.
    $query1 = mysql_query ("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());

    if (mysql_num_rows($query1) > 0) {
        //check that there is only one entry
        if (mysql_num_rows($query1) == 1) {
            //success: now we need to remove all supply from the user_sheet and add that number to the homebase_sheet
            $query2 = mysql_query("SELECT * FROM user_sheet WHERE id='$id'") or die(mysql_error());
            $row1 = mysql_fetch_assoc($query1);
            $row2 = mysql_fetch_assoc($query2);
            $transfered_supply = $row2['supply'];
            $new_supply = $row1['supply'] + $transfered_supply;

            if ($transfered_supply > 0) {
                //create the insert queries to update both tables
                $update = mysql_query("UPDATE homebase_sheet SET supply ='$new_supply' WHERE id='$id'") or die(mysql_error());
                $update = mysql_query("UPDATE user_sheet SET supply = 0 WHERE id='$id'");

                array_push($return_array, "Success");
                array_push($return_array, "Player has dropped their supply to their homebase");
                array_push($return_array, $transfered_supply);
                $json_return = json_encode($return_array);
                echo $json_return;
            } else {
                //if the player has no supply to transfer
                array_push($return_array, "Failed");
                array_push($return_array, "You have no supply to transfer");
                $json_return = json_encode($return_array);
                echo $json_return;
            } 

        } else if (mysql_num_rows($query1) > 1 ) {
            array_push($return_array, "Failed");
            array_push($return_array, "More than one entry found for the players homebase");
            $json_return = json_encode($return_array);
            echo $json_return;
        }


    } else {
        //the player has not yet set their homebase- return failure
        array_push($return_array, "Failed");
        array_push($return_array, "No matching entries for the players homebase");
        $json_return = json_encode($return_array);
        echo $json_return;
    }


} else {
    echo "No user ID sent in form";
}

?>