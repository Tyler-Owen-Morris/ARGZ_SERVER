<?php
include("db_connect.php");

$return_array = array();

$id = isset($_POST['id']) ? protect($_POST['id']) : '';
$owner_id = isset($_POST['owner_id']) ? protect($_POST['owner_id']) : '';
$outpost_id = isset($_POST['outpost_id']) ? protect($_POST['outpost_id']) : '';

if ($id <> '') {
    if ($owner_id <> '') {
        if ($outpost_id <> '') {
            $outpost_query = mysql_query("SELECT * FROM outpost_sheet WHERE outpost_id='$outpost_id' AND owner_id='$owner_id' AND capacity > 0") or die(mysql_error());
            
            if(mysql_affected_rows($outpost_query)>0){
                //increment the parent outpost down
                $outpost_update = msql_query("UPDATE outpost_sheet SET capacity=capacity-1 WHERE outpost_id='$outpost_id'") or die(msql_error());

                //add the child outpost using the query data
                $row = mysql_fetch_assoc($outpost_query);
                $expire_time = $row['expire_time'];
                $post_name = $row['name'];
                $post_lat = $row['outpost_lat'];
                $post_lng = $row['outpost_lng'];
                $insert_outpost = mysql_query("INSERT INTO outpost_sheet (name, owner_id, outpost_lat, outpost_lng, expire_time, capacity) VALUES ('$post_name', '$id', '$post_lat', '$post_lng', '$expire_time', 0)") or die(mysql_error());
                if (mysql_affected_rows($insert_outpost) > 0) {
                    array_push($return_array, "Success");
                    array_push($return_array, "Outpost successfully decremented and added to other user");
                }

            } else {
                array_push ($return_array, "Failed");
                array_push ($return_array, "Outpost does not have capacity to add more survivors");
            }

        }else {
            array_push ($return_array, "Failed");
            array_push ($return_array, "outpost ID not set");
        }
    }else {
        array_push ($return_array, "Failed");
        array_push ($return_array, "owner ID not set");
    }

}else {
    array_push ($return_array, "Failed");
    array_push ($return_array, "player ID not set");
}

$jsonReturn = json_encode($return_array);
echo $jsonReturn;
?>