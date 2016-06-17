<?php
include("db_connect.php");

$returnArray = array();

if(isset($_POST['request_id'])){
    if(isset($_POST['accept_id'])){
		//echo "post data is there, proceeding </br>";
        $accept_id = protect($_POST['accept_id']);
        $request_id = protect($_POST['request_id']);

        //This is a lookup to find matching currently paired players
        $query1 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$request_id' AND id_2='$accept_id'")or die(mysql_error());
        $query2 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$accept_id' AND id_2='$request_id'")or die(mysql_error());
        
        //this finds the player data from the user_sheet for use in the json return.
        $usrqry1 = "SELECT * FROM user_sheet WHERE id = '$accept_id'";
        $accepting_user_data = mysql_query($usrqry1) or die(mysql_error());
        $usrqry2 = "SELECT * FROM user_sheet WHERE id = '$request_id'";
        $requesting_user_data = mysql_query($usrqry2) or die(mysql_error());

        if(mysql_num_rows($query1) > 0 || mysql_num_rows($query2) > 0) {
			//echo "found a matching entry, processing </br>";
            //if the two have paired up before, then update the date on the entry.
            if(mysql_num_rows($query1) > 0) {
                //if there's already an entry for this player that has become "active" by the chron script, deactivate it.
                $update1 = mysql_query("UPDATE qr_pairs SET time=NOW() WHERE id_1='$request_id' AND id_2='$accept_id'") or die(mysql_error());
                
                array_push($returnArray, "Success");
                while($row = mysql_fetch_assoc($requesting_user_data)){
                    $userarray = array ("first_name" => $row['first_name'], "last_name" => $row['last_name']);
                    array_push($returnArray, $userarray);
                    $json_return = json_encode($returnArray);
                    echo $json_return;
                }
            }
			//if the request and accept id's are the same both of the queries will return values. This prevents the update from running twice on the same entry. this works for now, but future players will need to be given a failed message as players can't befriend themselves.
			if ($accept_id != $request_id) {
				if(mysql_num_rows($query2) > 0) {
					//if there's already an entry for this player that has become "active" by the chron script, deactivate it.
					$now = DateTime.now;
					$update1 = mysql_query("UPDATE qr_pairs SET time=NOW() WHERE id_1='$accept_id' AND id_2='$request_id'") or die(mysql_error());
					
					array_push($returnArray, "Success");
                    while($row = mysql_fetch_assoc($requesting_user_data)){
                        $userarray = array ("first_name" => $row['first_name'], "last_name" => $row['last_name']);
                        array_push($returnArray, $userarray);
					    $json_return = json_encode($returnArray);
					    echo $json_return;
                    }
				}
			}
            //***************************
            //This will also need to update the player data with rewards for the two.  The return should also update the UI to reflect the added stats or inventory items.
            //***************************
            
        } else {
            //otherwise- create an entire new pair.
            //echo "No matches found, attempting new item insert </br>";
            $now = DateTime.now;
            $insert1 = mysql_query("INSERT INTO qr_pairs (id_1, id_2, time) VALUES ('$request_id', '$accept_id', NOW())")or die(mysql_error());
            
            array_push($returnArray, "Success");
			while($row = mysql_fetch_assoc($requesting_user_data)){
                $userarray = array ("first_name" => $row['first_name'], "last_name" => $row['last_name']);
                array_push($returnArray, $userarray);
                $json_return = json_encode($returnArray);
                echo $json_return;
            }
        }
    } else {
        array_push($returnArray, "failed");
        $json_return = json_encode($returnArray);
        echo $json_return;
    }
} else {
array_push($returnArray, "failed");
$json_return = json_encode($returnArray);
echo $json_return;
}

// QR_FriendRequest.php
?>