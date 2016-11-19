<?php
    include("db_connect.php");

    $return_array = array();

    $food = isset($_POST['food']) ? protect($_POST['food']) : '';
    $water = isset($_POST['water']) ? protect($_POST['water']) : '';
    $supply = isset($_POST['supply']) ? protect($_POST['supply']) : '';
    $zombies = isset($_POST['zombies']) ? protect($_POST['zombies']) : '';
    $bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
    $bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';

    if ($food <> '' || $water <> '' || $supply <> '' || $zombies <> '' || $bldg_id <> '' || $bldg_name <> '') {

            $bldg_query = $mysqli->query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_name='$bldg_name'") or die($mysqli->error());

            if ($bldg_query->num_rows > 0) {
                array_push($return_array, "Failed");
                array_push($return_array, "Existing query found- this record shoudl ahve been associated in the client.... somethings severely fuct in merging the records");
            } else {
                $time = "2000-01-01 00:01:00";
                $bldg_insert = $mysqli->query("INSERT INTO cleared_buildings (id, bldg_name, bldg_id, active, time_cleared, last_looted_supply, last_looted_food, last_looted_water, supply, food, water, zombies) VALUES ('$id', '$bldg_name', '$bldg_id', 1, '$time', '$time','$time','$time', '$supply', '$food', '$water', '$zombies')") or die($mysqli->error());
                if($mysqli->affected_rows > 0) {
                    $bldg_query = $mysqli->query("SELECT * FROM cleared_buildings WHERE id='$id'") or die($mysqli->error());
                    $bldg_data_array = array();
                    while($bldg = $bldg_query->fetch_assoc()) 
                        array_push($bldg_data_array, $bldg);
                        
                    array_push($return_array, "Success");
                    array_push($return_array, $bldg_data_array);
                } else {
                    array_push($return_array, "Failed");
                    array_push($return_array, "insert query failed");
                }
            }
    } else {
        array_push($return_array, "Failed");
        array_push($return_array, "variables not set");
    }

    $json_return = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $json_return;
?>