<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $usrqry = "SELECT * FROM survivor_roster WHERE owner_id = '$id'";
        $survivordata = mysql_query($usrqry);
        
        //echo "The raw sql query returned: " + $userdata;
        $survivordataarr = array();
        if (mysql_num_rows($survivordata) > 0 ) {
            
            while ($row = mysql_fetch_assoc($survivordata)) {
                array_push($survivordataarr, array("entry_id" => $row['entry_id'], "owner_id" => $row['owner_id'], "survivor_id" => $row['survivor_id'], "name" => $row['name'], "base_stam" => $row['base_stam'], "curr_stam" => $row['curr_stam'], "base_attack" => $row['base_attack'], "weapon_equipped" => $row['weapon_equipped'], "isActive" => $row['isActive'], "start_time" => $row['start_time']));
            }
            $jsondata = json_encode($survivordataarr, JSON_NUMERIC_CHECK);
            echo $jsondata;
        } else {
            echo "json did not encode. sql returned less than 1 result";
        }
        
    } else {
        echo "player ID not set";
    }
?>