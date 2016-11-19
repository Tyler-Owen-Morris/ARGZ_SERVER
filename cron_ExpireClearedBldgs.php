<?php
include("db_connect.php");

$query = "SELECT * FROM cleared_buildings WHERE time_cleared < DATE_SUB(NOW(), INTERVAL 20 HOUR) AND active = '0'";
$allBldgData = $mysqli->query($query) or die($mysqli->error());

if ($allBldgData->num_rows > 0) {
    while ($row = $allBldgData->fetch_assoc) {
      
		echo "Time cleared is ".$row['time_cleared']."</br>";
		
		$id = protect($row['id']);
		$bldg_name = protect($row['bldg_name']);
		$bldg_id = protect($row['bldg_id']);	
		
        $update = $mysqli->query ("UPDATE cleared_buildings SET active ='1' WHERE id = '$id' AND bldg_id = '$bldg_id' ") or die($mysqli->error());
    }
} else {
    echo "SQL server did not return any buildings for cron script";
}
?>