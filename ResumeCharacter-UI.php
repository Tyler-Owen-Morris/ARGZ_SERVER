<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $usrqry = "SELECT * FROM user_sheet WHERE id = '$id'";
        $userdata = mysql_query($usrqry);
        
        //echo "The raw sql query returned: " + $userdata;
        
        if (mysql_num_rows($userdata) > 0 && mysql_num_rows($userdata) < 2) {
            
            while ($row = mysql_fetch_assoc($userdata)) {
                $userdataarr = array("id" => $row['id'], "first_name" => $row['first_name'], "last_name" => $row['last_name'], "total_survivors" => $row['total_survivors'], "active_survivors" => $row['active_survivors'], "char_created_DateTime" => $row['char_created_DateTime'], "homebase_lat" => $row['homebase_lat'], "homebase_lon" => $row['homebase_lon'], "last_player_current_health" => $row['last_player_current_health'], "supply" => $row['supply'], "water" => $row['water'], "food" => $row['food'], "meals" => $row['meals'], "knife_count" => $row['knife_count'], "club_count" => $row['club_count'], "gun_count" => $row['gun_count'], "knife_durability" => $row['knife_durability'], "club_durability" => $row['club_durability']);
                $jsondata = json_encode($userdataarr);
                
                echo $jsondata;
            }
        } else {
            echo "</br></br>json did not encode. sql returned more or less than 1 result";
        }
        
    } else {
        echo "player ID not set";
    }
?>

</br></br></br>
<form action="ResumeCharacter-UI.php" method="POST"/></br>
FB ID: <input type="text" name="id"/></br>
</br>
<input type="submit" name="ResumeCharacter-UI" value="Resume Character"/>
</form>