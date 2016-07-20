<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $usrqry = "SELECT * FROM player_sheet WHERE id = '$id'";
        $userdata = mysql_query($usrqry);
        
        $return_array = array();
        
        if (mysql_num_rows($userdata) == 1) {
            
            while ($row = mysql_fetch_assoc($userdata)) {
                $userdataarr = array("id" => $row['id'], "first_name" => $row['first_name'], "last_name" => $row['last_name'], "char_created_DateTime" => $row['char_created_DateTime'], "homebase_lat" => $row['homebase_lat'], "homebase_lon" => $row['homebase_lon'], "supply" => $row['supply'], "water" => $row['water'], "food" => $row['food'], "ammo" => $row['ammo'], "equipped_weapon_id" => $row['equipped_weapon_id'], "curr_stamina" => $row['curr_stamina'], "max_stamina" => $row['max_stamina']);
                array_push($return_array, "Success");
                array_push($return_array, $userdataarr);
                $jsondata = json_encode($return_array, JSON_NUMERIC_CHECK);
                echo $jsondata;
            }
        } else if (mysql_num_rows($userdata) == 0) {
            array_push($return_array, "Failed");
            array_push($return_array, "User has not started a character");
            $jsondata = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $jsondata;
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "More than one entry for the same user");
            $jsondata = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $jsondata;
        }
        
    } else {
        echo "player ID not set";
    }
?>