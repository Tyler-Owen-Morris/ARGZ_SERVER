<?php
include("db_connect.php");

$returnArray = array();

$accept_id = isset($_POST['id']) ? protect($_POST['id']) : '';
$request_id = isset($_POST['request_id']) ? protect($_POST['request_id']) : '';

if ($request_id <> '') {
    //find each player's survivor
    $accepting_survivor_game_data = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND team_position=5 LIMIT 1") or die(mysql_error());
    $requesting_survivor_game_data = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND team_position=5 LIMIT 1") or die(mysql_error());

    //find 'my-survivor-in-your-game' for eachother
    $MS_YG_accepting_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die(mysql_error());
    $MS_YG_requesting_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die(mysql_error());

    //find the player data
    $accepting_player_data = mysql_query("SELECT * FROM player_sheet WHERE id='$accept_id'") or die(mysql_error());
    $requesting_player_data = mysql_query("SELECT * FROM player_sheet WHERE id='$request_id'") or die(mysql_error());

    //This is a lookup to find an existing record for this pairing.
    $QR_query1 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$request_id' AND id_2='$accept_id' ")or die(mysql_error());
    $QR_query2 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$accept_id' AND id_2='$request_id' ")or die(mysql_error());

    if (mysql_num_rows($QR_query1) > 0 || mysql_num_rows($QR_query2) > 0) {
        //these players have paired before
        $pairing_data = array();
        if (mysql_num_rows($QR_query1) > 0) {
            $pairing_data = mysql_fetch_assoc($QR_query1);
        } else if (mysql_num_rows($QR_query2)) {
            $pairing_data = mysql_fetch_assoc($QR_query2);
        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "unable to isolate existing pair entry");
            die(json_encode($returnArray));
        }
        $scan_id = $pairing_data['scan_id'];
        $interval_string = "interval 12 hour";
        $pairing_update = mysql_query("UPDATE qr_pairs SET pair_ts=now(), pairing_count=pairing_count+1 WHERE scan_id='$scan_id' AND pair_ts<date_sub(NOW(), $interval_string)") or die(mysql_error()); //if the scan was within the last 12hrs it's too recent- invalid pairing

        if (mysql_affected_rows() > 0) {
            //successful pairing!
            array_push($returnArray, "Success");

            //construct arrays for each players survivor data.
            $accepting_survivor_data = mysql_fetch_assoc($accepting_survivor_game_data);
            $requesting_survivor_data = mysql_fetch_assoc($requesting_survivor_game_data);
            
            //if the accepting player already has a survivor on the requesting players roster
            if (mysql_num_rows($MS_YG_accepting_query) > 0) {
                $MS_YG_data = mysql_fetch_assoc($MS_YG_accepting_query);
                $new_stam = $MS_YG_data['base_stam']+10;
                $new_attk = $MS_YG_data['base_attack']+1;
                $MS_YG_update_query = mysql_query("UPDATE survivor_roster SET dead=0, base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die(mysql_error()); //dead=0 should ressurect the player.
            } else {
                //insert the accepting survivor onto the requesting players survivor roster
                $surv_name = $accepting_survivor_data['name'];
                $surv_stam = $accepting_survivor_data['base_stam'];
                $surv_attk = $accepting_survivor_data['base_attack'];
                $pic_url = $accepting_survivor_data['profile_pic_url'];
                $accepting_survivor_insert = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$request_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$accept_id', '$pic_url')") or die(mysql_error());
            }
            //if the requesting player already has a survivor on the accepting players roster
            if (mysql_num_rows($MS_YG_requesting_query) > 0) {
                //permenantly boost stats, and set to full stamina
                $MS_YG_data = mysql_fetch_assoc($MS_YG_requesting_query);
                $new_stam = $MS_YG_data['base_stam']+10;
                $new_attk = $MS_YG_data['base_attack']+1;
                $MS_YG_update_query = mysql_query("UPDATE survivor_roster SET dead=0,  base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die(mysql_error());
            } else {
                //insert the requesting survivor onto the accepting players survivor roster
                $surv_name = $requesting_survivor_data['name'];
                $surv_stam = $requesting_survivor_data['base_stam'];
                $surv_attk = $requesting_survivor_data['base_attack'];
                $pic_url = $requesting_survivor_data['profile_pic_url'];
                $requesting_survivor_insert = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$accept_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$request_id', '$pic_url')") or die(mysql_error());
            }

            //update player_sheet with resource bonus
            $accepting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10 WHERE id='$accept_id'") or die(mysql_error());
            $requesting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10 WHERE id='$request_id'") or die(mysql_error()); 

           ;
            array_push($returnArray, $requesting_survivor_data);

        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "Users have paired within 12 hours");
        }

    } else {
        //these players have not paired before
        
        //create the QR pairing record
        $QR_insert = mysql_query("INSERT INTO qr_pairs (id_1, id_2, pair_ts, pairing_count) VALUES ('$accept_id', '$request_id', NOW(), 1)") or die(mysql_error());
        if (mysql_affected_rows() >0) {
            array_push($returnArray, "Success");
        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "Unable to create the QR record");
            die(json_encode($returnArray));
        }

        //insert new survivors onto eachother's survivor roster.
        $accepting_survivor_data = mysql_fetch_assoc($accepting_survivor_game_data);
        $requesting_survivor_data = mysql_fetch_assoc($requesting_survivor_game_data);
        
        //if the accepting player already has a survivor on the requesting players roster
        if (mysql_num_rows($MS_YG_accepting_query) > 0) {
            $MS_YG_data = mysql_fetch_assoc($MS_YG_accepting_query);
            $new_stam = $MS_YG_data['base_stam']+10;
            $new_attk = $MS_YG_data['base_attack']+1;
            $MS_YG_update_query = mysql_query("UPDATE survivor_roster SET dead=0, base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die(mysql_error());
        } else {
            //insert the accepting survivor onto the requesting players survivor roster
            $surv_name = $accepting_survivor_data['name'];
            $surv_stam = $accepting_survivor_data['base_stam'];
            $surv_attk = $accepting_survivor_data['base_attack'];
            $pic_url = $accepting_survivor_data['profile_pic_url'];
            $accepting_survivor_insert = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$request_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$accept_id', '$pic_url')") or die(mysql_error());
        }
        if (mysql_affected_rows() < 1) {
            array_push($returnArray, "Failed");
            array_push($returnArray, "accepting survivor failed to create a record on the requesting players survivor roster");
            die(json_encode($returnArray));
        }
        //if the requesting player already has a survivor on the accepting players roster
        if (mysql_num_rows($MS_YG_requesting_query) > 0) {
            //permenantly boost stats, and set to full stamina
            $MS_YG_data = mysql_fetch_assoc($MS_YG_requesting_query);
            $new_stam = $MS_YG_data['base_stam']+10;
            $new_attk = $MS_YG_data['base_attack']+1;
            $MS_YG_update_query = mysql_query("UPDATE survivor_roster SET dead=0, base_stam='$new_stam', curr_stam='$new_stam', base_attack='$new_attk' WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die(mysql_error());
        } else {
            //insert the requesting survivor onto the accepting players survivor roster
            $surv_name = $requesting_survivor_data['name'];
            $surv_stam = $requesting_survivor_data['base_stam'];
            $surv_attk = $requesting_survivor_data['base_attack'];
            $pic_url = $requesting_survivor_data['profile_pic_url'];
            $requesting_survivor_insert = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, injured, dead, onMission, start_time, team_position, paired_user_id, profile_pic_url) VALUES ('$accept_id', '$surv_name', '$surv_stam', '$surv_stam', '$surv_attk', 1, 0, 0, 0, NOW(), 0, '$request_id', '$pic_url')") or die(mysql_error());
        }
        if (mysql_affected_rows() < 1) {
            array_push($returnArray, "Failed");
            array_push($returnArray, "requesting survivor failed to create a record on the accepting players survivor roster");
            die(json_encode($returnArray));
        }

        //update player_sheet with resource bonus
        $accepting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10 WHERE id='$accept_id'") or die(mysql_error());
        $requesting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10 WHERE id='$request_id'") or die(mysql_error());

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