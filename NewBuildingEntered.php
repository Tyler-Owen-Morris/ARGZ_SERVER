<?php
    include("db_connect.php");

    $return_array = array();

    $food = isset($_POST['food']) ? protect($_POST['food']) : '';
    $water = isset($_POST['water']) ? protect($_POST['water']) : '';
    $wood = isset($_POST['wood']) ? protect($_POST['wood']) : '';
	$metal = isset($_POST['metal']) ? protect($_POST['metal']) : '';
    $zombies = isset($_POST['zombies']) ? protect($_POST['zombies']) : '';
	$zombies_across = isset($_POST['zombies_across']) ? protect($_POST['zombies_across']) : '';
    $bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
    $bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';

    if ($food <> '' || $water <> '' || $wood <> '' || $metal <> '' || $zombies <> '' || $bldg_id <> '' || $bldg_name <> '' || $zombies_across <> '') {

            $bldg_query = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_name='$bldg_name'") or die(mysql_error());

            if (mysql_num_rows($bldg_query) > 0) {
                array_push($return_array, "Failed");
                array_push($return_array, "Existing query found- this record shoudl ahve been associated in the client.... somethings severely fuct in merging the records");
            } else {
                $time = "2000-01-01 00:01:00";
                $bldg_insert = mysql_query("INSERT INTO cleared_buildings 
					(id, bldg_name, bldg_id, active, time_cleared, last_looted_supply, last_looted_food, last_looted_water, wood, metal, food, water, zombies, zombies_across) 
				VALUES
				('$id', '$bldg_name', '$bldg_id', 1, '$time', '$time','$time','$time', '$wood', '$metal', '$food', '$water', '$zombies', '$zombies_across')") or die(mysql_error());
                if(mysql_affected_rows() > 0) {
                    $bldg_query = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id'") or die(mysql_error());
                    $bldg_data_array = array();
                    while($bldg = mysql_fetch_assoc($bldg_query)) 
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