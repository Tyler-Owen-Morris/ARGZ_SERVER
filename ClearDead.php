<?php
    include ("db_connect.php");

    $return_array = array();

    $clear_dead_query = mysql_query("DELETE FROM survivor_roster WHERE owner_id='$id' AND dead=1 AND onMission=0 ") or die(mysql_error());

    if (mysql_affected_rows() > 0) {
        array_push ($return_array, "Success");
        array_push ($return_array, "dead survivors have been deleted");
    } else {
        array_push ($return_array, "Success");
        array_push ($return_array, "no dead survivors found");
    }
    
    $jsonReturn = json_encode($return_array);
    echo $jsonReturn;
?>