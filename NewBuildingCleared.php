<?php 
    include ("db_connect.php");
    
?> 
<?php

if(isset($_POST['id'])){
    $id = protect($_POST['id']);
    if(isset($_POST['bldg_name'])){
        $bldg_name = protect($_POST['bldg_name']);
        $bldg_id = protect($_POST['bldg_id']);

        $supply_earned = protect($_POST['supply']);
        $water_earned = protect($_POST['water']);
        $food_earned = protect($_POST['food']);
        
        if(strlen($id) > 30) {
            echo ("id must be less than 30 characters");    
        }
        
        //This is a lookup to find matching currently clear entries
        $query1 = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_id ='$bldg_id' AND active=0")or die(mysql_error());
            
        if(mysql_num_rows($query1) > 0) {
            //if there is an entry matching user and building, that is already in a deactivated state
            echo "This building has already been cleared by the user";
            
        } else {
            //this finds the active entry to be deactivated in the update
            $query2 = mysql_query("SELECT * FROM cleared_buildings WHERE id='$id' AND bldg_id ='$bldg_id' AND bldg_name ='$bldg_name' AND active=1")or die(mysql_error());
            
            if(mysql_num_rows($query2) > 0 && mysql_num_rows($query2) < 2) {
                //if there's already an entry for this player that has become "active" by the chron script, deactivate it.
                $update1 = mysql_query("UPDATE cleared_buildings SET active=0, time_cleared=NOW() WHERE id='$id' AND bldg_id='$bldg_id'");
            } else {
                //otherwise- create an entire new user based on the post data.
                $insert1 = mysql_query("INSERT INTO cleared_buildings (id, bldg_name, bldg_id, active, time_cleared) VALUES ('$id', '$bldg_name', '$bldg_id', 0, NOW())")or die(mysql_error());
                echo "building successfully added to the cleared_building database";
            }
        }

        //update the player_sheet with the new inventory numbers
        $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id' LIMIT 1") or die(mysql_error());
        $player_data = mysql_fetch_assoc($player_query);
        $new_supply = $player_data['supply'] + $supply_earned;
        $new_food = $player_data['food'] + $food_earned;
        $new_water = $player_data['water'] + $water_earned;

        $update2 = mysql_query("UPDATE player_sheet SET supply=$new_supply, food=$new_food, water=$new_water WHERE id = '$id'") or die(mysql_error());
    }
}
// StartNewCharacter.php
?>