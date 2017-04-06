<?php 
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])){
    $id = protect($_POST['id']);
	
	//check for crafted tier items
	$workbench = "workbench";
	$T1_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete < NOW() AND type='$workbench'") or die(mysql_error());
	if (mysql_num_rows($T1_query)>0){
		$t1_row = mysql_fetch_assoc($T1_query);
		$craft_id = $t1_row['entry_id'];
		//adjust homebase record
		$homebase_update = mysql_query("UPDATE homebase_sheet SET craft_t1=1 WHERE id='$id'")or die(mysql_error());
		//delete entry
		$T1_delete = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$craft_id'") or die(mysql_error());
	}
	$forge = "forge";
	$T2_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete< NOW() AND type='$forge'")or die(mysql_error());
	if (mysql_num_rows($T2_query)>0){
		$t2_row = mysql_fetch_assoc($T2_query);
		$craft_id = $t2_row['entry_id'];
		
		$homebase_update = mysql_query("UPDATE homebase_sheet SET craft_t2=1 WHERE id='$id'")or die(mysql_error());
		
		$T2_delete = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$craft_id'") or die(mysql_error());
	}
	$lathe = "lathe";
	$T3_query = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete< NOW() AND type='$lathe'")or die(mysql_error());
	if (mysql_num_rows($T3_query)>0){
		$t3_row = mysql_fetch_assoc($T3_query);
		$craft_id = $t3_row['entry_id'];
		
		$homebase_update = mysql_query("UPDATE homebase_sheet SET craft_t3=1 WHERE id='$id'")or die(mysql_error());
		
		$T3_delete = mysql_query("DELETE FROM weapon_crafting WHERE entry_id='$craft_id'") or die(mysql_error());
	}
	
	
    //get the weapons still being worked on
    $query1 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete > NOW() ORDER BY time_complete ASC") or die(mysql_error());
    //get the weapons to expire
    $query2 = mysql_query("SELECT * FROM weapon_crafting WHERE id='$id' AND time_complete < NOW() ORDER BY time_complete DESC") or die(mysql_error());
    
    
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
   	
	$now_d = mysql_query("SELECT NOW()");
	$now = mysql_fetch_assoc($now_d);
    array_push($return_array, "Success");
    array_push($return_array, $weapon_inprogress_array);
    array_push($return_array, $completed_array);
	array_push($return_array, $now); //sent for purposes of generating an offset between server and client.
		
    $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
    echo $jsonReturn;

} else {
    echo "id not set";
}

?>