<?php
include("db_connect.php");

$returnArray = array();

$accept_id = isset($_POST['id']) ? protect($_POST['id']) : '';
$request_id = isset($_POST['request_id']) ? protect($_POST['request_id']) : '';

if ($request_id <> '') {
    //find each player's survivor
    $accepting_survivor_game_data = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND team_position=5 LIMIT 1") or die($mysqli->error());
    $requesting_survivor_game_data = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND team_position=5 LIMIT 1") or die($mysqli->error());

    //find 'my-survivor-in-your-game' for eachother
    $MS_YG_accepting_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die($mysqli->error());
    $MS_YG_requesting_query = $mysqli->query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die($mysqli->error());

    //find the player data
    $accepting_player_data = $mysqli->query("SELECT * FROM player_sheet WHERE id='$accept_id'") or die($mysqli->error());
    $requesting_player_data = $mysqli->query("SELECT * FROM player_sheet WHERE id='$request_id'") or die($mysqli->error());

    //This is a lookup to find matching currently paired players
    $QR_query1 = $mysqli->query("SELECT * FROM qr_pairs WHERE id_1='$request_id' AND id_2='$accept_id'")or die($mysqli->error());
    $QR_query2 = $mysqli->query("SELECT * FROM qr_pairs WHERE id_1='$accept_id' AND id_2='$request_id'")or die($mysqli->error());

    if ($QR_query1->num_rows > 0 || $QR_query2->num_rows > 0) {
        //these players have paired before
        $pairing_data = array();
        if ($QR_query1->num_rows > 0) {
            $pairing_data = $QR_query1->fetch_assoc();
        } else if ($QR_query2->num_rows) {
            $pairing_data = $QR_query2->fetch_assoc();
        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "unable to isolate existing pair entry");
            die(json_encode($returnArray));
        }
        $scan_id = $pairing_data['scan_id'];
        $interval_string = "interval 12 hour";
        $pairing_update = $mysqli->query("UPDATE qr_pairs SET pair_ts=now(), pairing_count=pairing_count+1 WHERE scan_id='$scan_id' AND pair_ts<date_sub(NOW(), $interval_string)") or die($mysqli->error());

        if ($mysqli->affected_rows > 0) {
            //successful pairing!
            array_push($returnArray, "Success");

            //construct arrays for each players survivor data.
            $accepting_survivor_data = $accepting_survivor_game_data->fetch_assoc();
            $requesting_survivor_data = $requesting_survivor_game_data->fetch_assoc();
            
            //if the accepting player already has a survivor on the requesting players roster
            if ($MS_YG_accepting_query->num_rows > 0) {
                $MS_YG_data = $MS_YG_accepting_query->fetch_assoc();
                $new_stam = $MS_YG_data['base_stam']+10;
                $new_attk = $MS_YG_data['base_attack']+1;
                $MS_YG_update_query = $mysqli->query("UPDATE survivor_roster SET base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die($mysqli->error());
            } else {
                //insert the accepting survivor onto the requesting players survivor roster
                $surv_name = $accepting_survivor_data['name'];
                $surv_stam = $accepting_survivor_data['base_stam'];
                $surv_attk = $accepting_survivor_data['base_attack'];
                $pic_url = $accepting_survivor_data['profile_pic_url'];
                $accepting_survivor_insert = $mysqli->query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$request_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$accept_id', '$pic_url')") or die($mysqli->error());
            }
            //if the requesting player already has a survivor on the accepting players roster
            if ($MS_YG_requesting_query->num_rows > 0) {
                //permenantly boost stats, and set to full stamina
                $MS_YG_data = $MS_YG_requesting_query->fetch_assoc();
                $new_stam = $MS_YG_data['base_stam']+10;
                $new_attk = $MS_YG_data['base_attack']+1;
                $MS_YG_update_query = $mysqli->query("UPDATE survivor_roster SET base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die($mysqli->error());
            } else {
                //insert the requesting survivor onto the accepting players survivor roster
                $surv_name = $requesting_survivor_data['name'];
                $surv_stam = $requesting_survivor_data['base_stam'];
                $surv_attk = $requesting_survivor_data['base_attack'];
                $pic_url = $requesting_survivor_data['profile_pic_url'];
                $requesting_survivor_insert = $mysqli->query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$accept_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$request_id', '$pic_url')") or die($mysqli->error());
            }

            //update player_sheet with resource bonus
            $accepting_player_update = $mysqli->query("UPDATE player_sheet SET supply=supply+25, food=food+10, water=water+10 WHERE id='$accept_id'") or die($mysqli->error());
            $requesting_player_update = $mysqli->query("UPDATE player_sheet SET supply=supply+25, food=food+10, water=water+10 WHERE id='$request_id'") or die($mysqli->error()); 

           ;
            array_push($returnArray, $requesting_survivor_data);

        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "Users have paired within 12 hours");
        }

    } else {
        //these players have not paired before
        
        //create the QR pairing record
        $QR_insert = $mysqli->query("INSERT INTO qr_pairs (id_1, id_2, pair_ts, pairing_count) VALUES ('$accept_id', '$request_id', NOW(), 1)") or die($mysqli->error());
        if ($mysqli->affected_rows >0) {
            array_push($returnArray, "Success");
        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "Unable to create the QR record");
            die(json_encode($returnArray));
        }

        //insert new survivors onto eachother's survivor roster.
        $accepting_survivor_data = $accepting_survivor_game_data->fetch_assoc();
        $requesting_survivor_data = $requesting_survivor_game_data->fetch_assoc();
        
        //if the accepting player already has a survivor on the requesting players roster
        if ($MS_YG_accepting_query->num_rows > 0) {
            $MS_YG_data = $MS_YG_accepting_query->fetch_assoc();
            $new_stam = $MS_YG_data['base_stam']+10;
            $new_attk = $MS_YG_data['base_attack']+1;
            $MS_YG_update_query = $mysqli->query("UPDATE survivor_roster SET base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die($mysqli->error());
        } else {
            //insert the accepting survivor onto the requesting players survivor roster
            $surv_name = $accepting_survivor_data['name'];
            $surv_stam = $accepting_survivor_data['base_stam'];
            $surv_attk = $accepting_survivor_data['base_attack'];
            $pic_url = $accepting_survivor_data['profile_pic_url'];
            $accepting_survivor_insert = $mysqli->query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$request_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$accept_id', '$pic_url')") or die($mysqli->error());
        }
        if ($accepting_survivor_insert->affected_rows < 1) {
            array_push($returnArray, "Failed");
            array_push($returnArray, "accepting survivor failed to create a record on the requesting players survivor roster");
            die(json_encode($returnArray));
        }
        //if the requesting player already has a survivor on the accepting players roster
        if ($MS_YG_requesting_query->num_rows > 0) {
            //permenantly boost stats, and set to full stamina
            $MS_YG_data = $MS_YG_requesting_query->fetch_assoc();
            $new_stam = $MS_YG_data['base_stam']+10;
            $new_attk = $MS_YG_data['base_attack']+1;
            $MS_YG_update_query = $mysqli->query("UPDATE survivor_roster SET base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die($mysqli->error());
        } else {
            //insert the requesting survivor onto the accepting players survivor roster
            $surv_name = $requesting_survivor_data['name'];
            $surv_stam = $requesting_survivor_data['base_stam'];
            $surv_attk = $requesting_survivor_data['base_attack'];
            $pic_url = $requesting_survivor_data['profile_pic_url'];
            $requesting_survivor_insert = $mysqli->query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$accept_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$request_id', '$pic_url')") or die($mysqli->error());
        }
        if ($mysqli->affected_rows < 1) {
            array_push($returnArray, "Failed");
            array_push($returnArray, "requesting survivor failed to create a record on the accepting players survivor roster");
            die(json_encode($returnArray));
        }

        //update player_sheet with resource bonus
        $accepting_player_update = $mysqli->query("UPDATE player_sheet SET supply=supply+25, food=food+10, water=water+10 WHERE id='$accept_id'") or die($mysqli->error());
        $requesting_player_update = $mysqli->query("UPDATE player_sheet SET supply=supply+25, food=food+10, water=water+10 WHERE id='$request_id'") or die($mysqli->error());

        array_push($returnArray, $requesting_survivor_data);
    }

} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "requesting id not set");
}

$json_return = json_encode($returnArray, JSON_NUMERIC_CHECK);
echo $json_return;

// QR_FriendRequest.php
?>