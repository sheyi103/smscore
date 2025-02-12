<?php

$servername = "";
$username = "";
$password = "";
$dbname = "";
$network = "";
$today = date("Y-m-d");

function log_action($msg) {
    $logFile = './log.log';
    $fp = @fopen($logFile, 'a+');
    @fputs($fp, "[".date('Y-m-d H:i:s')."] ".$msg ."\n<=============================================>\n");
    @fclose($fp);
    return TRUE;
}

$hit = file_get_contents('php://input');
log_action($hit);
log_action(print_r($_REQUEST, true));

$phone = $_REQUEST["phone"];
$msg_id = $_REQUEST["message_id"];
$response = $_REQUEST["response"];
$err_code = $_REQUEST["err"];
$msg = $_REQUEST["msg"];

log_action($phone.$response.$msg_id."==".$msg);
if($phone != "" && $msg_id != "" && $response != ""){

	log_action("Aziko");
    $last_ten = substr($phone, -10);
    $phone_number = "0" . $last_ten;
    $series = substr($phone_number, 0, 4);
log_action("last ten - " .$last_ten); 
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
        log_action("Connection failed: " . mysqli_connect_error());
    }
     
	log_action("Aziko1");
    //check for the number series
    $result = mysqli_query($conn, "SELECT network FROM number_series where series = '$series'");
 
	log_action("select from table: number_series, data " . $series . " number - " . $phone_number);
    //if exists
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $network = $row["network"];
    } else {
        $network = "MTN";
    }
//	$today =date("Y-m-d");
    log_action("network selected - " . $network);
    //save in message
    $result = mysqli_query($conn, "INSERT INTO messages VALUES (null, '$phone_number', '$msg_id' , 0, '$network', '$response','$err_code', null, null, '$msg' )");
   
	$message_size = ceil(strlen($msg)/160);
 
log_action("data inserted - phone: " . $phone_number . ", message_id: " . $msg_id . ", network: " . $network . ", response: " . $response . "error code" . $err_code );

    if ($result){

        //get sentcount that matches network
        $result = mysqli_query($conn, "SELECT id FROM sentcount where network = '$network' and date(entry_date) = '$today' limit 1");
      
	 log_action("message table insert result - " . mysqli_fetch_assoc($result) . ", network ". $network);
        if (!$result) {
            log_action("Error select sentcount: " . mysqli_error($conn));
        }

         //if exist 
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $id = $row[0];

	//	log_action($id);
		
            if (!mysqli_query($conn, "UPDATE sentcount SET counter = counter + $message_size WHERE network = '$network' and date(entry_date) = '$today'")) {
                log_action("Error update sentcount: " . mysqli_error($conn));
            } else {
                log_action( "updated successfully - table: sentcount" );
            }
        }else{
            if (!mysqli_query($conn,"INSERT INTO sentcount VALUES (null, $message_size, '$today', '$network')")) {
                log_action("Error insert sentcount 2: " . mysqli_error($conn));
            } else {
                log_action( "success - table: sentcount, date: " . $today . ", network: " . $network );
            }

        }

        
    } else {

	log_action("Aziko2");
        log_action("Error: <br>" . $conn->error);
    }

    log_action("end");
    mysqli_close($conn);
}
?>
