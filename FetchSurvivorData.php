<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $usrqry = "SELECT * FROM survivor_roster WHERE owner_id = '$id' ORDER BY team_position DESC";
        $survivordata = mysql_query($usrqry) or die(mysql_error());
        $return_array = array();
        
        //echo "The raw sql query returned: " + $userdata;
        $survivordataarr = array();
        if (mysql_num_rows($survivordata) > 0 ) {
            array_push($return_array, "Success");
            while ($row = mysql_fetch_assoc($survivordata)) {
                array_push($survivordataarr, array("entry_id" => $row['entry_id'], "owner_id" => $row['owner_id'], "survivor_id" => $row['survivor_id'], "name" => $row['name'], "base_stam" => $row['base_stam'], "curr_stam" => $row['curr_stam'], "base_attack" => $row['base_attack'], "weapon_equipped" => $row['weapon_equipped'], "isActive" => $row['isActive'], "start_time" => $row['start_time'], "team_pos" => $row['team_position']));
            }
            array_push($return_array, $survivordataarr);
            $jsondata = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $jsondata;
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "no survivors found");
            $json_return = json_encode($return_array);
            echo $json_return;
        }
        
    } else {
        echo "player ID not set";
    }
?>