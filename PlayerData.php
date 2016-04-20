<?php 
    include "db_connect.php";

    $usrqry = "SELECT ID, username, survivors_active, survivors_total, supply, current_health, home_lat, home_lon, date_started FROM character_sheet";
    $userdata = mysql_query($usrqry);

    if (mysql_num_rows($userdata) > 0) {
        while ($row = mysql_fetch_assoc($userdata)) {
            echo "ID: ".$row['ID']." Username: ".$row['username']." Active Survivors: ".$row['survivors_active']." Total Survivors: ".$row['survivors_total']. " Supply: " .$row['supply']." Player Health: ".$row['current_health']." Lattitude of Homebase: ".$row['home_lat']." Longitude of Homebase: ".$row['home_lon']." Date/time Started: ".$row['date_started']."</br>";
        }
    }
?>