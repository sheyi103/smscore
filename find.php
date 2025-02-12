<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$servername = "";
$username = "";
$password = "";
$dbname = "";
$response = [];

if($_REQUEST['phone']){

	$phone = "0". substr($_REQUEST['phone'], -10);
//$phone = $_REQUEST['phone'];

// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
	if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}

$data = mysqli_query($conn, "SELECT * FROM messages where mobile_no = '$phone' Order By id DESC limit 10 ");
if (!$data) {
    echo("Error select messager: " . mysqli_error($conn));
}

if (mysqli_num_rows($data) > 0){
    while($row = mysqli_fetch_assoc($data)) {
    	array_push($response, $row);
    }
}

echo json_encode($response);

mysqli_close($conn);
}
?>
