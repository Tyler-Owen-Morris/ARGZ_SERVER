<?php
include("db_connect.php");

$returnArray = array();

$building_id = isset($_POST['building_id']) ? protect($_POST['building_id']) : '';

if ($id <> '') {
    if ( $building_id <> '') {
        //cost the brains off the account.
        $brains_update = mysql_query("UPDATE player_sheet SET brains=brains-25 WHERE id='$id' AND brains>=25") or die(mysql_error());
        if (mysql_affected_rows()){
            $existing_query = mysql_query("SELECT * FROM baited_buildings WHERE owner_id='$id' AND building_id='$building_id' AND status=0 ") or die(mysql_error());
            if (mysql_num_rows($existing_query) >0 ){

                $updateExisting_query = mysql_query ("UPDATE baited_buildings SET status=0 WHERE owner_id='$id' AND building_id='$building_id'") or die(mysql_error());
                if (mysql_affected_rows()){
                    //player data
                    $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
                    $player_data_array = mysql_fetch_assoc($player_query);
                    //building data
                    $bldg_query = mysql_query("SELECT * FROM baited_buildings WHERE owner_id='$id'") or die(mysql_error());
                    $bldg_data_array = array();
                    if (mysql_num_rows($bldg_query)>0) {
                        while($bldg = mysql_fetch_assoc($bldg_query)){
                            array_push($bldg_data_array, $bldg);
                        }
                    }else{
                        $bldg_data_array = null;
                    }

                    array_push($returnArray, "Success");
                    array_push($returnArray, $player_data_array);
                    array_push($returnArray, $bldg_data_array);
                } else {
                    array_push($returnArray, "Failed");
                    array_push($returnArray, "Unable to update existing query to baited status");
                }

            }else{
                $insert_query = mysql_query("INSERT INTO baited_buildings SET owner_id='$id', building_id='$building_id', status=0")or die (mysql_error());
                if (mysql_affected_rows()){
                    //player data
                    $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
                    $player_data_array = mysql_fetch_assoc($player_query);
                    //building data
                    $bldg_query = mysql_query("SELECT * FROM baited_buildings WHERE owner_id='$id'") or die(mysql_error());
                    $bldg_data_array = array();
                    if (mysql_num_rows($bldg_query)>0) {
                        while($bldg = mysql_fetch_assoc($bldg_query)){
                            array_push($bldg_data_array, $bldg);
                        }
                    }else{
                        $bldg_data_array = null;
                    }

                    array_push($returnArray, "Success");
                    array_push($returnArray, $player_data_array);
                    array_push($returnArray, $bldg_data_array);

                }else{
                    array_push($returnArray, "Failed");
                    array_push($returnArray, "Unable to INSERT entry into database");
                }
            }
        }else{
            array_push($returnArray, "Failed");
            array_push($returnArray, "Unable to expense brains");
        }
    }else{
        array_push($returnArray, "Failed");
        array_push($returnArray, "Building ID not set");
    }
} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "Player ID not set");
}
$jsonreturn = json_encode($returnArray, JSON_NUMERIC_CHECK);
echo $jsonreturn;
?>