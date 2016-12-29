
<?php 
    include "db_connect.php";

   if (isset($_POST['id']){
    $id = protect($_POST['id']);
    $bldgdata = mysql_query("SELECT * FROM cleared_buildings WHERE id = '$id'");

    if (mysql_num_rows($bldgdata) > 0) {
         while ($row = mysql_fetch_assoc($bldgdata)) {
                $bldgdataarr = array("id" => $row['id'], "bldg_name" => $row['bldg_name'], "bldg_id" => $row['bldg_id'], "active" => $row['active']);
                $jsondata = json_encode($bldgdataarr, JSON_NUMERIC_CHECK);                            
         
        } 
        echo $jsondata;                             
    }else {
        echo"Player has no currentley empty buildings";
    }
   }
?>

</br></br></br>
<form action="BuildingClearedData.php" method="POST"/></br>
FB ID: <input type="text" name="id"/></br>
</br>
<input type="submit" name="BuildingClearedData" value="Get Json Return"/>
</form>
