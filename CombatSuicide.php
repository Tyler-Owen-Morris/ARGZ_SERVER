<?php
include("db_connect.php");

$return_array = array();


if ($id <> '') {
    
    $player_update = mysql_query("UPDATE player_sheet SET isZombie=2 WHERE id=$id") or die(mysql_error());

    if (mysql_affected_rows()) {
        array_push($return_array, "Success");
        //handle the zombie high score
        $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
        $player_data = mysql_fetch_assoc($player_query);

        $zombie_kills = $player_data['zombies_killed'];
        $zombie_highscore = $player_data['zombies_killed_high_score'];
        if ($zombie_kills > $zombie_highscore) {
            $zombie_update = mysql_query("UPDATE player_sheet SET zombies_killed_high_score='$zombie_kills' WHERE id='$id'") or die(mysql_error());
            array_push($return_array, $zombie_kills);
        } else {
            array_push($return_array, 0);
        }
        //json[1] is 0 for no new high score and >0 when there is a new zombie high score
    }
} else {
    array_push($return_array, "Failed");
    array_push($return_array, "Player ID not set");
}

$jsonreturn = json_encode($return_array);
echo $jsonreturn;
?>