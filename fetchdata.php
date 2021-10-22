<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$servername = "41.76.197.12";
$username = "providus2";
$password = "Providus!@#";
$dbname = "access";
$sent = [];
$data= [];
$delivered = [];
$failed = [];

$today = date("Y-m-d"); 
//echo $today;
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM failed_counter where entry_date = '$today'");
if (!$result) {
    echo("Error select fail_counter: " . mysqli_error($conn));
}
//if exists
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        array_push($failed, $row);
    }
}

$result = mysqli_query($conn, "SELECT * FROM delivered_counter where entry_date = '$today'");
if (!$result) {
    echo("Error select delivered_counter: " . mysqli_error($conn));
}
//if exists
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        array_push($delivered, $row);
    }
}

$re = mysqli_query($conn, "SELECT * FROM sentcount where entry_date = '$today'");

if(!$re) {
    echo("Error select sentcount: " . mysqli_error($conn));
}
//if exists
if (mysqli_num_rows($re) > 0) {
    while($row = mysqli_fetch_assoc($re)) {
     
//	print_r($row);
	
	  array_push($sent, $row);
    }
}

$result_set = [
    "sent" => $sent,
    "delivered" => $delivered,
    "failed" => $failed
];

echo json_encode($result_set);

mysqli_close($conn);
?>
