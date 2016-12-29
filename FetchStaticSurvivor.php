<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $survivor_query = "SELECT * FROM static_survivors ORDER BY RAND()";
        $survivordata = mysql_query($survivor_query) or die(mysql_error());
    
        $return_array = array();
        $survivordataarr = array();
        
        if (mysql_num_rows($survivordata) > 0 ) {
            array_push($return_array, "Success");
            while ($row = mysql_fetch_assoc($survivordata)) {
                array_push($survivordataarr, $row);
            }
            array_push($return_array, $survivordataarr);
            $jsondata = json_encode($return_array, JSON_NUMERIC_CHECK);
            echo $jsondata;
        } else {
            array_push($return_array, "Failed");
            array_push($return_array, "no survivors found");
            $json_return = json_encode($return_array);
            echo $json_return;
        }
        
    } else {
        echo "player ID not set";
    }
?>