<?php
include("db_connect.php");


if (isset($_POST['id'])){
    $id = protect($_POST['id']);
        
    $usrqry = "DELETE FROM survivor_roster WHERE '$id' = owner_id";
    $survivordata = mysql_query($usrqry) or die(mysql_error);
    echo $survivordata;

} else {
    echo "Failed: id not posted";
}
?>