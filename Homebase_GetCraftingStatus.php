<?php 
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])){
    $id = protect($_POST['id']);
    //get the weapons still being worked on
    $query1 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete > NOW()") or die(mysql_error());
    //get the weapons to expire
    $query2 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete < NOW()") or die(mysql_error());
    
    
    $weapon_inprogress_array = array();
    //construct the weapons in progress into an array
    if (mysql_num_rows($query1) > 0) {
        while ($weapon = mysql_fetch_assoc($query1)) {
            $entry_id = $weapon['entry_id'];
            $type = $weapon['type'];
            $duration = $weapon['duration'];
            $time_complete = $weapon['time_complete'];
            

            $this_weapon_array = array("entry_id" => $entry_id, "type" => $type, "duration" => $duration, "time_complete" => $time_complete);
            array_push($weapon_inprogress_array, $this_weapon_array);
        }
    } 

    
    
    $completed_array = array();
    //construct the weapons completed into an array.
    if(mysql_num_rows($query2) > 0){
        while ($weapon = mysql_fetch_assoc($query2)) {
            $entry_id = $weapon['entry_id'];
            $type = $weapon['type'];
            $duration = $weapon['duration'];
            $weapon_index = $weapon['weapon_index'];

            $this_weapon_array = array("entry_id" => $entry_id, "type" => $type, "duration" => $duration, "time_complete" => $time_complete, "weapon_index"=> $weapon_index);
            array_push($completed_array, $this_weapon_array);
        }
    }
   
    array_push($return_array, "Success");
    array_push($return_array, $weapon_inprogress_array);
    array_push($return_array, $completed_array);
    $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $jsonReturn;

} else {
    echo "id not set";
}

?>