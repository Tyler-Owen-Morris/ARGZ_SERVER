<?php
//db_connect.php
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

if (!isset($_POST['id'])) {
    array_push($return_array, "Player ID not set");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}
$id = protect($_POST['id']);
array_push($return_array, $id);

if (!isset($_POST['login_ts'])) {
    array_push($return_array, "TimeStamp not set");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}
$login_ts = protect($_POST['login_ts']);
array_push($return_array, $login_ts);

if (!isset($_POST['client'])) {
    array_push($return_array, "Client not set");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}
if(($_POST['client'] != "web") and ($_POST['client'] != "mob")) {
    array_push($return_array, "Invalid Client");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}
    
if($login_ts == "12/31/1999 11:59:59") {
    if($_POST['client'] == "web") $updateqry = "UPDATE player_sheet SET web_login_ts=now() WHERE id = '$id'";
    else $updateqry = "UPDATE player_sheet SET mob_login_ts=now() WHERE id = '$id'";
    mysql_query($updateqry);    
    $usrqry = "SELECT * FROM player_sheet WHERE id = '$id'";
} else {
    if($_POST['client'] == "web") $usrqry = "SELECT * FROM player_sheet WHERE id = '$id' AND web_login_ts = '$login_ts'";
    else $usrqry = "SELECT * FROM player_sheet WHERE id = '$id' AND mob_login_ts = '$login_ts'";
}

$userdata = mysql_query($usrqry);
if (mysql_num_rows($userdata) == 0) {
    array_push($return_array, "Invalid Login Check");
    die(json_encode($return_array, JSON_NUMERIC_CHECK));
}

function protect($string) {
	$string = mysql_real_escape_string(strip_tags($string));
	return $string;
}


function createthumb($name,$filename,$new_w,$new_h) {
	$system=explode(".",$name);
	if (preg_match("/jpg|jpeg/",$system[1])){$src_img=imagecreatefromjpeg($name);}
	if (preg_match("/png/",$system[1])){$src_img=imagecreatefrompng($name);}
	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);
	if ($old_x > $old_y)
	{
		$thumb_w=$new_w;
		$thumb_h=$old_y*($new_h/$old_x);
	}
	if ($old_x < $old_y)
	{
		$thumb_w=$old_x*($new_w/$old_y);
		$thumb_h=$new_h;
	}
	if ($old_x == $old_y)
	{
		$thumb_w=$new_w;
		$thumb_h=$new_h;
	}
	$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
	if (preg_match("/png/",$system[1]))
	{
		imagepng($dst_img,$filename);
	} else {
		imagejpeg($dst_img,$filename);
	}
	imagedestroy($dst_img);
	imagedestroy($src_img);
}
?>