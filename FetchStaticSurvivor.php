<?php
    include ("db_connect.php");

    if (isset($_POST['id'])) {
        $id = protect($_POST['id']);
        
        $survivor_query = "SELECT * FROM static_survivors ORDER BY RAND()";
        $survivordata = $mysqli->query($survivor_query) or die($mysqli->error());
    
        $return_array = array();
        $survivordataarr = array();
        
        if ($survivordata->num_rows > 0 ) {
            array_push($return_array, "Success");
            while ($row = $survivordata->fetch_assoc()) {
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