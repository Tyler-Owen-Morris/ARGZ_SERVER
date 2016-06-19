<?php 
    include ("db_connect.php");
    
?> 
<?php

if(isset($_POST['id'])){
    $id = protect($_POST['id']);
    $first_name = protect($_POST['first_name']);
    $last_name = protect($_POST['last_name']);
    
    $survivors = protect($_POST['total_survivors']);
    $active_survivors = protect($_POST['active_survivors']);
    $supply = protect($_POST['supply']);
    $water = protect($_POST['water']);
    $food = protect($_POST['food']);
    
    $time_char_started = protect($_POST['char_created_DateTime']);
    
    $knife_count = protect($_POST['knife_count']);
    $club_count = protect($_POST['club_count']);
    $gun_count = protect($_POST['gun_count']);
    $knife_dur = protect($_POST['knife_durability']);
    $club_dur = protect($_POST['club_durability']);
    
    $meals = 0;
    $health = 100;
    $home_lat = 10.1234;
    $home_lon = 10.1234;
    //$daytime_now = date('MySQL');
    //echo $daytime_now;
    
    $register1 = mysql_query("SELECT id FROM user_sheet WHERE id='$id'")or die(mysql_error());
    
    
        
    if(strlen($id) > 20) {
        echo ("id must be less than 20 characters");    
    }elseif(!is_numeric($active_survivors)) {
        echo ("Survivors Alive must be a number");
    }elseif(!is_numeric($supply)) {
        echo ("Supply must be a number");
    }elseif(mysql_num_rows($register1) > 0) {
        //if the id is already registered, it should just overwrite the new character data into that acccount.
        $update1 = mysql_query("UPDATE user_sheet SET first_name = '$first_name', last_name = '$last_name', total_survivors = '$survivors', active_survivors = '$active_survivors', supply = '$supply', food = '$food', water = '$water', char_create_DateTime = '$time_char_started', knife_count = '$knife_count', club_count = '$club_count', gun_count = '$gun_count', knife_durability = '$knife_dur', club_durability = '$club_dur' WHERE id = '$id'")or die(mysql_error());
    
//mysql_query("UPDATE user_sheet SET first_name = \'.$first_name.'\, last_name = \'.$lastname.'\, total_survivors = '.$survivors.', active_survivors = '.$active_survivors.', supply = '.$supply.', food = '.$food.', water = '.$water.', knife_count = '.$knife_count.', club_count = '.$club_count.', gun_count = '.$gun_count.', knife_durability = '.$knife_dur.', club_durability = '.$club_dur.' WHERE id = '.$id.'")or die(mysql_error());

//'first_name'='$first_name', 'last_name'='$last_name', 'total_survivors' = '$survivors', 'char_created_DateTime' = '$daytime_now', 'homebase_lat' = '$home_lat', 'homebase_lon' = '$home_lon', 'last_player_current_health' = '$health', 'supply' = '$supply', 'water' = '$water', 'food' = '$food', 'meals' = '$meals', 'knife_count' = '$knife_count', 'club_count' = '$club_count', 'gun_count' = '$gun_count', 'knife_durability' = '$knife_dur', 'club_durability' = '$club_dur' WHERE 'id' = '$id'")or die(mysql_error());

//        (first_name, last_name, total_survivors, char_created_DateTime, homebase_lat, homebase_lon, last_player_current_health, supply, water, food, meals, knife_count, club_count, gun_count, knife_durability, club_durability) VALUES ('$first_name', '$last_name', '$survivors', '$daytime_now', '$home_lat', '$home_lon', '$health', '$supply', '$water', '$food', '$meals', '$knife_count', '$club_count', '$gun_count', '$knife_dur', '$club_dur')
        
        
    } else {
        //otherwise- create an entire new user based on the post data.
        $insert1 = mysql_query("INSERT INTO user_sheet (id, first_name, last_name, total_survivors, char_created_DateTime, homebase_lat, homebase_lon, last_player_current_health, supply, water, food, meals, knife_count, club_count, gun_count, knife_durability, club_durability) VALUES ('$id', '$first_name', '$last_name', '$survivors', '$daytime_now', '$home_lat', '$home_lon', '$health', '$supply', '$water', '$food', '$meals', '$knife_count', '$club_count', '$gun_count', '$knife_dur', '$club_dur')")or die(mysql_error());
            echo "character successfully added to the database";
    }
}
// StartNewCharacter.php
?>