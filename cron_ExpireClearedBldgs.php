<?php
include("db_connect.php");

$datetime_now = new DateTime();
$datetime_deadline = new DateTime();
date_sub($datetime_deadline, date_interval_create_from_date_string('1 day'));

$query = "SELECT * FROM cleared_buildings";
$allBldgData = mysql_query($query) or die(mysql_error());

if (mysql_num_rows($allBldgData) > 0) {
    while ($row = mysql_fetch_assoc($allBldgData)) {
        $cleared = strtotime($row['time_cleared']);
        if ($cleared < $deadline ) {
            echo "found an expired bldg- attempting to update SQL";
            $update = mysql_query ("UPDATE cleared_buildings WHERE id = ".$row['id']." AND bldg_id = ".$row['bldg_id']." SET active ='true'") or die(mysql_error());
        } else {
			echo $row['bldg_name']+" is not ready to be set back to active yet.";
		}
    }
} else {
    echo "SQL server did not return any buildings for cron script";
}
?>