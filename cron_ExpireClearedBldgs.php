<?php
include("db_connect.php");

$query = "SELECT * FROM cleared_buildings WHERE time_cleared < DATE_SUB(NOW(), INTERVAL 20 HOUR) AND active = '0'";
$allBldgData = mysql_query($query) or die(mysql_error());

if (mysql_num_rows($allBldgData) > 0) {
    while ($row = mysql_fetch_assoc($allBldgData)) {
      
		echo "Resetting the building ".$row['bldg_name']." time cleared is ".$row['time_cleared']." cleared by player ".$row['id']."</br>";
		
		$id = protect($row['id']);
		$bldg_name = protect($row['bldg_name']);
		$bldg_id = protect($row['bldg_id']);	
		
        $update = mysql_query ("UPDATE cleared_buildings SET active ='1' WHERE id = '$id' AND bldg_id = '$bldg_id' ") or die(mysql_error());
    }
} else {
    echo "SQL server did not return any buildings for cron script";
}
?>