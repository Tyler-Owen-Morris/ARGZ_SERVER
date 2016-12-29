<?php 
    include ("db_connect.php");
    
?> 
Start New Character - Manual UI</br></br>
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
    
    $knife_count = protect($_POST['knife_count']);
    $club_count = protect($_POST['club_count']);
    $gun_count = protect($_POST['gun_count']);
    
    $knife_dur = 50;
    $club_dur = 25;
    $meals = 0;
    $health = 100;
    $home_lat = 10.1234;
    $home_lon = 10.1234;
    $daytime_now = date('c');
    
    $register1 = mysql_query("SELECT id FROM user_sheet WHERE id='$id'")or die(mysql_error());
    
    
        
    if(strlen($id) > 20) {
        echo ("id must be less than 20 characters");    
    }elseif(!is_numeric($survivors)) {
        echo ("Survivors Alive must be a number");
    }elseif(!is_numeric($supply)) {
        echo ("Supply must be a number");
    }elseif(mysql_num_rows($register1) > 0) {
        //if the id is already registered, it should just overwrite the new character data into that acccount.
        $update1 = mysql_query("UPDATE user_sheet (first_name, last_name, total_survivors, char_created_DateTime, homebase_lat, homebase_lon, last_player_current_health, supply, water, food, meals, knife_count, club_count, gun_count, knife_durability, club_durability) VALUES ('$first_name', '$last_name', '$survivors', '$daytime_now', '$home_lat', '$home_lon', '$health', '$supply', '$water', '$food', '$meals', '$knife_count', '$club_count', '$gun_count', '$knife_dur', '$club_dur')")or die(mysql_error());
    } else {
        //otherwise- create an entire new user based on the post data.
        $insert1 = mysql_query("INSERT INTO user_sheet (id, first_name, last_name, total_survivors, char_created_DateTime, homebase_lat, homebase_lon, last_player_current_health, supply, water, food, meals, knife_count, club_count, gun_count, knife_durability, club_durability) VALUES ('$id', '$first_name', '$last_name', '$survivors', '$daytime_now', '$home_lat', '$home_lon', '$health', '$supply', '$water', '$food', '$meals', '$knife_count', '$club_count', '$gun_count', '$knife_dur', '$club_dur')")or die(mysql_error());
            echo "character successfully added to the database";
    }
}
?>



</br></br></br>
<form action="StartNewCharacter-UI.php" method="POST"/></br>
FB ID: <input type="text" name="id"/></br>
first name: <input type="text" name="first_name"/></br>
last name: <input type="text" name="last_name"/></br>
total survivors alive: <input type="text" name="total_survivors" value="7"/></br>
active survivors: <input type="text" name="active_survivors" value="5"/></br>
starting supply: <input type="text" name="supply" value="25"/></br>
starting water: <input type="text" name="water" value="10"/></br>
starting food: <input type="text" name="food" value="10"/></br>
starting knife count: <input type="text" name="knife_count" value="5"/></br>
starting club count: <input type="text" name="club_count" value="3"/></br>
starting gun count: <input type="text" name="gun_count" value="30"/></br>
</br>
<input type="submit" name="startNewCharacter-UI" value="Start New Character"/>
</form>
