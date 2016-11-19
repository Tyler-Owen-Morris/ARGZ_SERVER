<?php
include("db_connect.php");

$return_array = array();


if ($id <> '') {
    
    $player_update = $mysqli->query("UPDATE player_sheet SET isZombie=2 WHERE id=$id") or die($mysqli->error());

    if ($mysqli->affected_rows) {
        array_push($return_array, "Success");
        //handle the zombie high score
        $player_query = $mysqli->query("SELECT * FROM player_sheet WHERE id='$id'") or die($mysqli->error());
        $player_data = $player_query->fetch_assoc();

        $zombie_kills = $player_data['zombies_killed'];
        $zombie_highscore = $player_data['zombies_killed_high_score'];
        if ($zombie_kills > $zombie_highscore) {
            $zombie_update = $mysqli->query("UPDATE player_sheet SET zombies_killed_high_score='$zombie_kills' WHERE id='$id'") or die($mysqli->error());
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