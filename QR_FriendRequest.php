<?php 
    include ("db_connect.php");
?> 
<?php
$returnArray = array();
if(isset($_POST['request_id'])){
    $request_id = protect($_POST['request_id']);
    if(isset($_POST['accept_id'])){
        $accept_id = protect($_POST['accept_id']);
        

        //This is a lookup to find matching currently paired players
        $query1 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$request_id' AND id_2='$accept_id'")or die(mysql_error());
        $query2 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$accept_id' AND id_2='$request_id'")or die(mysql_error());

        if(mysql_num_rows($query1) > 0 || mysql_num_rows($query2) > 0) {
            //if the two have paired up before, then update the date on the entry.
            if(mysql_num_rows($query1) > 0) {
                //if there's already an entry for this player that has become "active" by the chron script, deactivate it.
                $now = DateTime.now;
                $update1 = mysql_query("UPDATE qr_pairs SET time='$now' WHERE id_1='$request_id' AND id_2='$accept_id'") or die(mysql_error());
                
                array_push($returnArray, "Success");
                $json_return = json_encode($returnArray);
                echo $json_return;
            }
            if(mysql_num_rows($query2) > 0) {
                //if there's already an entry for this player that has become "active" by the chron script, deactivate it.
                $now = DateTime.now;
                $update1 = mysql_query("UPDATE qr_pairs SET time='$now' WHERE id_1='$accept_id' AND id_2='$request_id'") or die(mysql_error());
                
                array_push($returnArray, "Success");
                $json_return = json_encode($returnArray);
                echo $json_return;
            }
            
            //***************************
            //This will also need to update the player data with rewards for the two.  The return should also update the UI to reflect the added stats or inventory items.
            //***************************
            
        } else {
            //otherwise- create an entire new pair.
            $now = DateTime.now;
            $insert1 = mysql_query("INSERT INTO qr_pairs (id_1, id_2, time) VALUES ('$request_id', '$accept_id', '$now'")or die(mysql_error());
            
            array_push($returnArray, "Success");
            $json_return = json_encode($returnArray);
            echo $json_return;
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