<?php 
    include "db_connect.php";


    $usrqry = "SELECT id, first_name, total_survivors, active_survivors, char_created_DateTime, homebase_lat, homebase_lon, last_player_current_health, supply, water, food, meals, knife_count, club_count, gun_count, knife_durability, knife_durability FROM user_sheet";
    $userdata = $mysqli->query($usrqry);

    if ($userdata->num_rows > 0) {
		$userdata = array();
        while ($row = $userdata->fetch_assoc()) {
           $entry =  array("id" => $row['id'], "first_name" => $row['first_name'], "last_name" => $row['last_name'],"total_survivors" => $row['total_survivors'],"active_survivors" => $row['active_survivors'],"char_created_DateTime" => $row['char_created_DateTime'], "homebase_lat" => $row['homebase_lat'],"homebase_lon" => $row['homebase_lon'],"supply" => $row['supply'], "food" => $row['food'],"water" => $row['water'],"meals" => $row['meals'], "knife_count" => $row['knife_count'],"club_count" => $row['club_count'],"gun_count" => $row['gun_count'],"last_player_current_health" => $row['last_player_current_health'],"knife_durability" => $row['knife_durability'],"club_durability" => $row['club_durability']);
		   array_push($userdata, $entry);
        }
		$jsondata = json_encode($userdata, JSON_NUMERIC_CHECK);
		echo $jsondata;
    }
?>