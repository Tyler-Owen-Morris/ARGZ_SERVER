<?php
//manually select the DB W/O credentials
$return_array = array();
array_push($return_array, "Failed");
function var_dump_plus($var, $name) {
 echo "<b>$name:</b><br><pre>";
 var_dump($var);
 echo "</pre>";
}

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
$auth_code = isset($_GET['code']) ? $_GET['code'] : '';

if ($id <> '' || $auth_code <> '') {
	
	
	
	$token_update = mysql_query("UPDATE homebase_sheet SET fitbit_authorization_code='$auth_code' WHERE id='$id'") or die(mysql_error());
	if(mysql_affected_rows()){
		
		//forward the curl request for access token and refresh token
		$redirect_uri = urlencode("http://www.argzombie.com/ARGZ_DEV_SERVER/FitbitCallback.php");//$redirect_uri = curl_escape("http://www.argzombie.com/ARGZ_DEV_SERVER/FitbitCallback.php");
		$encoded = base64_encode("227YT4:1a761965ce558d433ada1aa9b23ab398"); //encode ID+secret
		$fields_string = "&client_id=227YT4&grant_type=authorization_code&redirect_uri=".$redirect_uri."&code=".$auth_code."&state=".$id;
	
		$url = "https://api.fitbit.com/oauth2/token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Basic '.$encoded));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		//var_dump($redirect_uri);var_dump($encoded);var_dump($fields_string);var_dump($ch);
		$return = curl_exec($ch);
		//var_dump($return);
		
		$return_json = json_decode($return, true);
		curl_close($return);
		var_dump_plus($return_json, "return_json"); 
		//exit();
		$access_token = $return_json['access_token'];
		$refresh_token = $return_json['refresh_token'];
		$user_id = $return_json['user_id'];
		//exit();
		$duration = 0;
		if($return_json['expires_in']==28800){
			$duration = 8;
		}else{
			$duration =1;
		}
		
		$interval_string = "interval $duration hour";
		echo "</br></br>";
		//var_dump_plus($access_token, "access_token");
		
		//var_dump_plus($access_token,"access token");var_dump_plus($refresh_token, "refresh token");var_dump_plus($duration, "duration");
		//exit();
		$update_query = mysql_query("UPDATE homebase_sheet SET fitbit_access_token='$access_token', fitbit_refresh_token='$refresh_token', fitbit_expire_datetime=DATE_ADD(NOW(), $interval_string) WHERE id='$id'") or die(mysql_error());
		if (mysql_affected_rows()){
			$existing_query = mysql_query("SELECT * FROM homebase_sheet WHERE id='$id'") or die(mysql_error());
			$query_data = mysql_fetch_assoc($existing_query);
			array_push($return_array, "Success");
			array_push($return_array, "access and refresh tokens added to player data");
			array_push($return_array, $query_data);
			echo "Fitbit authorization successful</br></br>";
			
			$today = date("m.d.y");
			$fitbit_query_url ="https://api.fitbit.com/1/user/".$user_id."/activities/date/".$today.".json";
			curl_setopt($ch, CURLOPT_URL, $fitbit_query_url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Basic '.$encoded));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		}else{
			array_push($return_array, "Failed");
			array_push($return_array, "unable to update database with tokens");
			echo "Fitbit authorization unsuccessful";
		}
		
		//echo "<script>window.close();</script>";

	} else {
		array_push($return_array, "Failed");
		array_push($return_array, "needed variables not set");
	}
} 



/*
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
}

if ($id <> '' || $access_token <> '' || $refresh_token <> '' || $auth_code <> ''){
	array_push($return_array, "Failed");
	array_push($return_array, "NONE of the expected variables were included.");
}
*/

$jsonreturn = json_encode($return_array);
echo $jsonreturn;
//FitbitCallback.php
?>