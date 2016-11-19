<?php 
include("db_connect.php");
$return_array = array();

if (isset($_POST['id'])){
    $id = protect($_POST['id']);
    $query1 = $mysqli->query("SELECT * FROM homebase_sheet WHERE id='$id'") or die($mysqli->error());

    if ($query1->num_rows == 1 ) {
        $row = $query1->fetch_assoc();
        $supply = $row['supply'];
        array_push($return_array, "Success");
        array_push($return_array, $supply);
        $jsonReturn = json_encode($return_array, JSON_NUMERIC_CHECK);
        echo $jsonReturn;
    } else {
        //I will write this to return the exception later, for now I'm assuming there is only 1 entry or nothing
        echo "More or less than 1 entry found";
    }


} else {
    echo "id not set";
}

?>