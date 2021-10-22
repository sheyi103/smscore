<?php
echo "ACK/Jasmin";

$servername = "41.76.197.12";
$username = "providus2";
$password = "Providus!@#";
$dbname = "access";
$network = "";
$status = 0;

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

$message_status = $_REQUEST['message_status'];
$msg_id = $_REQUEST['id'];
$level = $_REQUEST['level'];
$err_code = $_REQUEST['err'];

if($msg_id !="" && $message_status !="" && $level !=""){

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
    log_action("Connection failed: " . mysqli_connect_error());

    }

    if($level == 2){
        $today = date("Y-m-d");

        if($message_status == "DELIVRD"){
            $status=1;
            $table = "delivered_counter";
        } else {
            $table = "failed_counter";
            $status=2;
        }
        log_action($message_status. " is status and table is ".$table );

        $sql = "UPDATE messages SET status = $status, error_code = '$err_code' WHERE msg_id = '$msg_id' and status = 0 " ;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            log_action("Message update - $sql " . $status);

            $sql = "SELECT network FROM messages where msg_id = '$msg_id' limit 1";
            $result = mysqli_query($conn, $sql);
		log_action("Message query: " . $sql);             
            if (!$result) {
                log_action( "Error" . $sql . mysqli_error($conn) );
            } 

            //if exist 
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                $network = $row["network"];

                $data = mysqli_query($conn, "SELECT id FROM $table where network='$network' and date(entry_date)='$today'");
                log_action($table." table - network ". $network);

                if (mysqli_num_rows($data) > 0) {
                    $id = mysqli_fetch_assoc($data)["id"];
                    //run update
                    $sql = "UPDATE $table SET counter = counter+1 WHERE id=$id";
                    $result = mysqli_query($conn, $sql);
                    if (!$result) {
                        log_action("Error " . $sql . mysqli_error($conn));
                    }

                }else{
                    //insert
                    $sql = "INSERT INTO $table VALUES (null, '$today', 1, '$network')";
                    $result = mysqli_query($conn, $sql);
                    if (!$result) {
                        log_action("Error " . $sql . mysqli_error($conn));
                    } 
                }


            }
        }
    }    
	mysqli_close($conn);
   log_action('ACK/Jasmin');    
}

log_action("end");

