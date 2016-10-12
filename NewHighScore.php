<?php
    include("db_connect.php");

    $return_array = array();

    $new_high_score = isset($_POST['high_score']) ? protect($_POST['high_score']) : '';

    $high_score_update_query = mysql_query("UPDATE player_sheet SET high_score='$new_high_score' WHERE id='$id'") or die(mysql_error());
?>