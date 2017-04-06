<?php
    include("db_connect.php");

    $return_array = array();

    $action = isset($_POST['action']) ? protect($_POST['action']) : '';
	$file = isset($_FILES['profileImage']) ? $_FILES['profileImage'] : '' ;

    if ( $action <> '' && $action=="upload image" && count($_FILES)>0) {
		
		unset($imagename);
 
        if(!isset($_FILES) &&  isset($HTTP_POST_FILES)) $_FILES = $HTTP_POST_FILES;
 
        if(!isset($_FILES['profileImage'])) $error["image_file"] = "An image was not found.";
 
        $imagename = basename($_FILES['profileImage']['name']);
		/*
		//we are maintaining raw binary, and do not need to handle the file at all in PHP
		$file = file_get_contents($file);
		$file = base64_encode($file);
		*/
 
 
        if(empty($imagename)) $error["imagename"] = "The name of the image was not found.";
 
        if(empty($error)) {
			
			//the following method attempts to update the SQL database
			$delete_query = mysql_query("UPDATE player_sheet SET profile_image_blob=null WHERE id='$id'") or die (mysql_error());
			$update_query = mysql_query("UPDATE player_sheet SET profile_image_blob='$file' WHERE id='$id'")or die(mysql_error());
			if(mysql_affected_rows()){
				array_push($return_array, "Success");
				array_push($return_array, "database says it's got the blob");
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "Unable to update the database");
			}
			
			/*
			 //the below method aims to write the file to a directory as a PNG
            $newimage = "Profile_images/" . $imagename;
            //echo $newimage;
            $result = move_uploaded_file($_FILES['profileImage']['tmp_name'], $newimage);
            if ( empty($result) ) { array_push($return_array, "Failed");array_push($return_array, "Unable to write the uploaded image to the given path");}else{ array_push($return_array, "Sucess"); array_push($return_array, "no errors reported on image move"); }
			*/
			
		}
		
		
		
		/* //This section was to write the raw binary to SQL, removed to attempt saving the file to a directory. 1-26-17
		$imgData =addslashes(file_get_contents($_FILES['profileImage']));
		$imageProperties = getimageSize($_FILES['profileImage']);
		$sql = "UPDATE player_sheet SET profile_image_blob ='{$imgData}' WHERE id='$id' LIMIT 1";
		$current_id = mysql_query($sql) or die("<b>Error:</b> Problem on Image Insert<br/>" . mysql_error());
		*/
		
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
//UploadProfileImage.php
?>


<?php
/*
if(count($_FILES) > 0) {
if(is_uploaded_file($_FILES['userImage']['tmp_name'])) {
mysql_connect("localhost", "root", "");
mysql_select_db ("phppot_examples");
$imgData =addslashes(file_get_contents($_FILES['profileImage']['tmp_name']));
$imageProperties = getimageSize($_FILES['userImage']['tmp_name']);
$sql = "INSERT INTO output_images(imageType ,imageData)
VALUES('{$imageProperties['mime']}', '{$imgData}')";
$current_id = mysql_query($sql) or die("<b>Error:</b> Problem on Image Insert<br/>" . mysql_error());
if(isset($current_id)) {
header("Location: listImages.php");
}}}
*/
?>