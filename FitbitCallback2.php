<?php
//manually select the DB W/O credentials
$return_array = array();
array_push($return_array, "Failed");

$con = mysql_connect("localhost","arg_admin","razo5-wire");
if (!$con) {
    array_push($return_array, "connection to SQL server failed");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}
$db = mysql_select_db("ARGZ_DEV_DB",$con);
if (!$db) {
    array_push($return_array, "error connecting to database");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}


$return_array = array();
$id = isset($_GET['state']) ? $_GET['state'] :  '';//client passes userID in the state parameter that's forwarded with the Oauth request
$access_token = isset($_GET['access_token']) ? $_GET['access_token'] : '';
$refresh_token = isset($_GET['refresh_token']) ? $_GET['refresh_token'] : '';
$expires_in = isset($_GET['expires_in']) ? $_GET['expires_in'] : '';

if ($id <> '' || $access_token <> '' || $refresh_token <> '') {
	$access_token = $return_json['access_token'];
	$refresh_token = $return_json['refresh_token'];
	$duration = 0;
	if($return_json['expires_in']==28800){
		$duration = 8;
	}else{
		$duration =1;
	}
	$interval_string = "interval $duration hour";
	$update_query = mysql_query("UPDATE homebase_sheet SET fitbit_access_token='$access_token', fitbit_refresh_token='$refresh_token', fitbit_expire_datetime=DATE_ADD(NOW(), $interval_string) WHERE id='$id'") or die(mysql_error());
	if (mysql_affected_rows()){
		array_push($return_array, "Success");
		array_push($return_array, "access and refresh tokens added to player data");
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "unable to update database with tokens");
	}
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "variables not set");
}

$jsonreturn = json_encode($return_array);
echo $jsonreturn;
//FitbitCallback.php
?>