<?php 
    include ("db_connect.php");
    
?> 
Add Characters
</br></br></br>
<?php

if(isset($_POST['startCharacter'])){
    $username = protect($_POST['username']);
    $alive = protect($_POST['survivors']);
    $supply = protect($_POST['supply']);
    
    if(strlen($username) > 20) {
        echo ("Username must be less than 20 characters");    
    }elseif(!is_numeric($alive)) {
        echo ("Survivors Alive must be a number");
    }elseif(!is_numeric($supply)) {
        echo ("Supply must be a number");
    } else {
//        $register1 = mysql_query("SELECT 'ID' FROM 'character_sheet' WHERE 'username' = '$username'") or die(mysql_error());
//        if(mysql_num_rows($register1) > 0) {
//            echo "that username is already in use";
//        } else {
            $health = 100;
            $home_lat = 10.1234;
            $home_lon = 10.1234;
            $daytime_now = date('c');
            
            $insert1 = mysql_query("INSERT INTO character_sheet (username, survivors_active, survivors_total, supply, current_health, home_lat, home_lon, date_started) VALUES ('$username','$alive','$alive','$supply','$health','$home_lat','$home_lon','$daytime_now')")or die(mysql_error());
            echo "character successfully added to the database";
    }
}

?>
</br></br></br>
<form action="StartCharacter.php" method="POST"/></br>
username: <input type="text" name="username"/></br>
survivors alive: <input type="text" name="survivors"/></br>
starting supply: <input type="text" name="supply"/></br></br>
<input type="submit" name="startCharacter" value="Start Character"/>
</form>