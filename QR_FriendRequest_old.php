<?php
include("db_connect.php");

$returnArray = array();

if(isset($_POST['request_id'])){
    if(isset($_POST['accept_id'])){
		//echo "post data is there, proceeding </br>";
        $accept_id = protect($_POST['id']);
        $request_id = protect($_POST['request_id']);

        //This is a lookup to find matching currently paired players
        $query1 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$request_id' AND id_2='$accept_id'")or die(mysql_error());
        $query2 = mysql_query("SELECT * FROM qr_pairs WHERE id_1='$accept_id' AND id_2='$request_id'")or die(mysql_error());
        
        //this finds the player data from the user_sheet for use in the json return.
        $usrqry1 = "SELECT * FROM user_sheet WHERE id = '$accept_id'";
        $accepting_user_data = mysql_query($usrqry1) or die(mysql_error());
        $usrqry2 = "SELECT * FROM user_sheet WHERE id = '$request_id'";
        $requesting_user_data = mysql_query($usrqry2) or die(mysql_error());

        //find the survivor card for each player that represets the player
        $accepting_survivor_game_data = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND team_position='5' LIMIT 1") or die(mysql_error());
        $requsting_survivor_game_data = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND team_position='5' LIMIT 1") or die(mysql_error());

        if(mysql_num_rows($query1) > 0 || mysql_num_rows($query2) > 0) {
			//echo "found a matching entry, processing </br>";
            //if the two have paired up before, then update the date on the entry.
            if(mysql_num_rows($query1) > 0) {
                //if there's already an entry for this player that has become "active" by the chron script, deactivate it.
                $update1 = mysql_query("UPDATE qr_pairs SET time=NOW() WHERE id_1='$request_id' AND id_2='$accept_id'") or die(mysql_error());
                
                array_push($returnArray, "Success");
                while($row = mysql_fetch_assoc($requesting_user_data)){
                    $userarray = array ("first_name" => $row['first_name'], "last_name" => $row['last_name']);
                    array_push($returnArray, $userarray);
                    $json_return = json_encode($returnArray);
                    echo $json_return;
                }
            }
			//if the request and accept id's are the same both of the queries will return values. This prevents the update from running twice on the same entry. this works for now, but future players will need to be given a failed message as players can't befriend themselves.
			if ($accept_id != $request_id) {
				if(mysql_num_rows($query2) > 0) {
					//if there's already an entry for this player that has become "active" by the chron script, deactivate it.
					$now = DateTime.now;
					$update1 = mysql_query("UPDATE qr_pairs SET time=NOW() WHERE id_1='$accept_id' AND id_2='$request_id'") or die(mysql_error());
					
					array_push($returnArray, "Success");
                    while($row = mysql_fetch_assoc($requesting_user_data)){
                        $userarray = array ("first_name" => $row['first_name'], "last_name" => $row['last_name']);
                        array_push($returnArray, $userarray);
					    $json_return = json_encode($returnArray);
					    echo $json_return;
                    }
				}
			}

            //locate the existing matching pair
            $accept_survivor_on_requster = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die(mysql_query());
            $request_survivor_on_accepter = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die(mysql_query());

            //if entries are found, then update them, otherwise create new entries. since it is possible that the survivor has died, and thus been deleted.
            if (mysql_num_rows($accept_survivor_on_requster) > 0) {
                //verifying that there is ONLY 1 entry
                if (mysql_num_rows($accept_survivor_on_requster) < 2){
                    $row1 = mysql_fetch_assoc($accept_survivor_on_requster);
                    $survivor_entry_id = $row1['entry_id'];
                    $survivor_stamina = $row1['base_stam'];
                    $update2 = mysql_query("UPDATE survivor_roster SET base_stam='$survivor_stamina', curr_stam='$survivor_stamina', start_time=NOW() WHERE entry_id='$survivor_entry_id'") or die(mysql_error());
                }
            } else {
                //no match found- insert a new entry 
                $row2 = mysql_fetch_assoc($accepting_survivor_game_data);
                $name = $row2['name'];
                $base_stam = $row2['base_stam'];
                $base_attack = $row2['base_attack'];
                $insert2 = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, weapon_equipped, team_position, paired_user_id) VALUES ('$request_id', '$name', '$base_stam', '$base_stam', '$base_attack', 1, '0', '0', '$accept_id')") or die(mysql_error());
            }
            if (mysql_num_rows($request_survivor_on_accepter) > 0) {
                //verifying that there is ONLY 1 entry
                if (mysql_num_rows($request_survivor_on_accepter) < 2){
                    $row1 = mysql_fetch_assoc($request_survivor_on_accepter);
                    $survivor_entry_id = $row1['entry_id'];
                    $survivor_stamina = $row1['base_stam'];
                    $update2 = mysql_query("UPDATE survivor_roster SET base_stam='$survivor_stamina', curr_stam='$survivor_stamina', start_time=NOW() WHERE entry_id='$survivor_entry_id'") or die(mysql_error());
                }
            } else {
                //no match found- insert a new entry
                $row4 = mysql_fetch_assoc($requsting_survivor_game_data);
                $name1 = $row4['name'];
                $base_stam1 = $row4['base_stam'];
                $base_attack1 = $row4['base_attack'];
                $insert3 = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, weapon_equipped, team_position, paired_user_id) VALUES ('$accept_id', '$name1', '$base_stam1', '$base_stam1', '$base_attack1', 1, '0', '0', '$request_id'") or die(mysql_query());
            }
            
        } else {
            //otherwise- create an entire new pair.
            //echo "No matches found, attempting new item insert </br>";
            $now = DateTime.now;
            $insert1 = mysql_query("INSERT INTO qr_pairs (id_1, id_2, time) VALUES ('$request_id', '$accept_id', NOW())")or die(mysql_error());
            
            array_push($returnArray, "Success");
			while($row = mysql_fetch_assoc($requesting_user_data)){
                $userarray = array ("first_name" => $row['first_name'], "last_name" => $row['last_name']);
                array_push($returnArray, $userarray);
                $json_return = json_encode($returnArray);
                echo $json_return;
            }

             //locate the existing matching pair
            $accept_survivor_on_requster = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$request_id' AND paired_user_id='$accept_id'") or die(mysql_query());
            $request_survivor_on_accepter = mysql_query("SELECT * FROM survivor_roster WHERE owner_id='$accept_id' AND paired_user_id='$request_id'") or die(mysql_query());

            //if entries are found, then update them, otherwise create new entries. since it is possible that the survivor has died, and thus been deleted.
            if (mysql_num_rows($accept_survivor_on_requster) > 0) {
                //verifying that there is ONLY 1 entry
                if (mysql_num_rows($accept_survivor_on_requster) < 2){
                    $row1 = mysql_fetch_assoc($accept_survivor_on_requster);
                    $survivor_entry_id = $row1['entry_id'];
                    $survivor_stamina = $row1['base_stam'];
                    $update2 = mysql_query("UPDATE survivor_roster SET base_stam='$survivor_stamina', curr_stam='$survivor_stamina', start_time=NOW() WHERE entry_id='$survivor_entry_id'") or die(mysql_error());
                }
            } else {
                //no match found- insert a new entry 
                $row2 = mysql_fetch_assoc($accepting_survivor_game_data);
                $name = $row2['name'];
                $base_stam = $row2['base_stam'];
                $base_attack = $row2['base_attack'];
                $insert2 = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, weapon_equipped, team_position, paired_user_id) VALUES ('$request_id', '$name', '$base_stam', '$base_stam', '$base_attack', 1, '0', '0', '$accept_id')") or die(mysql_error());
            }
            if (mysql_num_rows($request_survivor_on_accepter) > 0) {
                //verifying that there is ONLY 1 entry
                if (mysql_num_rows($request_survivor_on_accepter) < 2){
                    $row1 = mysql_fetch_assoc($request_survivor_on_accepter);
                    $survivor_entry_id = $row1['entry_id'];
                    $survivor_stamina = $row1['base_stam'];
                    $update2 = mysql_query("UPDATE survivor_roster SET base_stam='$survivor_stamina', curr_stam='$survivor_stamina', start_time=NOW() WHERE entry_id='$survivor_entry_id'") or die(mysql_error());
                }
            } else {
                //no match found- insert a new entry
                $row4 = mysql_fetch_assoc($requsting_survivor_game_data);
                $name1 = $row4['name'];
                $base_stam1 = $row4['base_stam'];
                $base_attack1 = $row4['base_attack'];
                $insert3 = mysql_query("INSERT INTO survivor_roster (owner_id, name, base_stam, curr_stam, base_attack, isActive, weapon_equipped, team_position, paired_user_id) VALUES ('$accept_id', '$name1', '$base_stam1', '$base_stam1', '$base_attack1', 1, '0', '0', '$request_id'") or die(mysql_query());
            }
        }
    } else {
        array_push($returnArray, "failed");
        $json_return = json_encode($returnArray);
        echo $json_return;
    }
} else {
array_push($returnArray, "failed");
$json_return = json_encode($returnArray);
echo $json_return;
}

// QR_FriendRequest.php
?>