<?php
include("db_connect.php");


if (isset($_POST['id'])){
    $id = protect($_POST['id']);
        
    $usrqry = "DELETE FROM survivor_roster WHERE '$id' = owner_id";
    $survivordata = $mysqli->query($usrqry) or die($mysqli->error);
    echo $survivordata;

} else {
    echo "Failed: id not posted";
}
?>