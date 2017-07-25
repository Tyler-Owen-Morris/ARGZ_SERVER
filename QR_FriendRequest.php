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
    //store the status of their first scan- 1 is not the first time, and 0 is the first time.
    $accepter_first_scan = $accepting_player_data["first_scan"];
    $requesting_first_scan = $requesting_player_data["first_scan"];

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

            //update player_sheet with resource bonus and confirm first scan
            $accepting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10, first_scan=1 WHERE id='$accept_id'") or die(mysql_error());
            $requesting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10, first_scan=1 WHERE id='$request_id'") or die(mysql_error()); 

           ;
            array_push($returnArray, $requesting_survivor_data);

        } else {
            array_push($returnArray, "Failed");
            array_push($returnArray, "Users have paired within 12 hours");
        }

    } else {
        //these players have not paired before
        
        //create the QR pairing record
        $QR_insert = mysql_query("INSERT INTO qr_pairs id_1='$accept_id', id_2='$request_id', pair_ts=NOW(), pairing_count=1") or die(mysql_error());
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
            $accepting_survivor_insert = mysql_query("INSERT INTO survivor_roster owner_id='$id', name='$surv_name', base_stam='$surv_stam', curr_stam='$surv_stam', base_attack='$surv_attk', isActive=1, injured=0, dead=0, onMission=0, start_time=NOW(), team_position=0, paired_user_id='$accept_id', profile_pic_url='$pic_url'") or die(mysql_error());
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
            $requesting_survivor_insert = mysql_query("INSERT INTO survivor_roster owner_id='$accept_id', name='$surv_name', base_stam='$surv_stam', curr_stam='$surv_stam', base_attack='$surv_attk', isActive=1, injured=0, dead=0, onMission=0, start_time=NOW(), team_position=0, paired_user_id='$request_id', profile_pic_url='$pic_url'") or die(mysql_error());
        }
        if (mysql_affected_rows() < 1) {
            array_push($returnArray, "Failed");
            array_push($returnArray, "requesting survivor failed to create a record on the accepting players survivor roster");
            die(json_encode($returnArray));
        }

        //if this is a players first player find- reset their "time started" to 2 days ago.
        $interval_2days = "interval 2 day";
        if ($accepter_first_scan==0){
            $accepter_start_update = mysql_query("UPDATE player_sheet SET char_created_DateTime= date_sub(NOW(), $interval_2days) , meals=8 WHERE id='$accept_id'") or die(mysql_error()); //meals must be set to match the clock
        }
        if($requesting_first_scan==0){
            $requesting_start_update = mysql_query("UPDATE player_sheet SET char_created_DateTime = date_sub(NOW(), $interval_2days) , meals=8 WHERE id='$request_id'") or die(mysql_error());
        }

        //update player_sheet with resource bonus
        $accepting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10, first_scan=1 WHERE id='$accept_id'") or die(mysql_error());
        $requesting_player_update = mysql_query("UPDATE player_sheet SET wood=wood+25, metal=metal+25, food=food+10, water=water+10, first_scan=1 WHERE id='$request_id'") or die(mysql_error());

        //***** Fetch Updated return data ****

        //survivor data
        //First get team. Correct is missing team members...
        $team_pos = 5;
        $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND team_position > 0 ORDER BY team_position DESC") or die(mysql_error());
        $survivor_data_array = array();
        $survivor_count = mysql_num_rows($survivor_query);
        if (mysql_num_rows($survivor_query) > 0) {
            while ($survivor = mysql_fetch_assoc($survivor_query)) {
                if($survivor['team_position'] != $team_pos) {
                    $survivor['team_position'] = $team_pos;
                    mysql_query("UPDATE survivor_roster SET team_position=".$survivor['team_position']." WHERE entry_id=".$survivor['entry_id']) or die(mysql_error());
                }
                array_push($survivor_data_array, $survivor);
                $team_pos--;
            }
            if($team_pos > 0) {
                $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND team_position = 0 AND onMission = 0 LIMIT $team_pos") or die(mysql_error());
                while ($survivor = mysql_fetch_assoc($survivor_query)) {
                    $survivor['team_position'] = $team_pos;
                    mysql_query("UPDATE survivor_roster SET team_position=".$survivor['team_position']." WHERE entry_id=".$survivor['entry_id']);
                    array_push($survivor_data_array, $survivor);
                    $team_pos--;
                }
            }
        }
        //Select everyone else...
        $survivor_query = mysql_query("SELECT * FROM survivor_roster WHERE owner_id = '$id' AND team_position = 0 ORDER BY onMission ASC") or die(mysql_error());
        $survivor_count += mysql_num_rows($survivor_query);
        if (mysql_num_rows($survivor_query) > 0)
            while ($survivor = mysql_fetch_assoc($survivor_query))
                array_push($survivor_data_array, $survivor);

        if($survivor_count == 0)
            $survivor_data_array = null;
        }

        array_push($return_array, $requesting_survivor_data);
        array_push($return_array, $survivor_data_array);

} else {
    array_push($returnArray, "Failed");
    array_push($returnArray, "requesting id not set");
}

$json_return = json_encode($returnArray, JSON_NUMERIC_CHECK);
echo $json_return;

// QR_FriendRequest.php
?>