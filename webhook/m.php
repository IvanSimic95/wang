<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/vars.php';
date_default_timezone_set('Europe/Bucharest');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$error = "";
//Save to order log function
function f($array) {
    $dataToLog = $array;
    $data = $dataToLog;
    $data .= PHP_EOL;
    $pathToFile = $_SERVER['DOCUMENT_ROOT']."/logs/m.log";
    $success = file_put_contents($pathToFile, $data, FILE_APPEND);
    if ($success === TRUE){
      echo "log saved";
    }
}

//Send data function
function send($data, $domain) {
    $sendData = file_get_contents('https://'.$domain.'/webhook/update.php?data='.$data);
    return $sendData;
}
    
if(isset($_POST['action_type'])){
$action = $_POST['action_type'];
$product_codename = $_POST['product_codename'];
$customer_emailaddress = $_POST['customer_emailaddress'];
$customer_phone = $_POST['customer_phone'];
$price = $_POST['product_price'];
$bgOrderID = $_POST['order_id_global'];

isset($_POST['action_type']) ? $action = $_POST['action_type'] : $action = "NONE";


isset($_POST['subid3']) ? $subid3 = $_POST['subid3'] : $subid3 = "";
isset($_POST['subid4']) ? $subid4 = $_POST['subid4'] : $subid4 = "";
isset($_POST['subid5']) ? $subid5 = $_POST['subid5'] : $subid5 = "";

$ip = $_POST['ipaddress'];
$agent = $_POST['browser_user_agent'];

$subid5Decoded = base64_decode($subid5);
if (str_contains($subid5Decoded, '|')) { 
  $clean = explode("|", $subid5Decoded);
  $orderID  = $clean[0];
  $domain   = $clean[1];
  $c1       = $clean[2];
  $c2       = $clean[3];
  $c3       = $clean[4];
}else{
  $error = "ERROR WITH SUBID5: ".$action. " | " .$product_codename. " | " .$customer_emailaddress. " | " .$customer_phone. " | " .$subid3. " | " .$subid4. " | " .$subid5. " | " .$ip. " | " .$agent;
  f($error);
  echo $error;
}


if($error == ""){
$data = $action."|".$product_codename."|".$customer_emailaddress."|".$customer_phone."|".$price."|".$bgOrderID."|".$subid3."|".$subid4."|".$subid5. " | " .$ip. " | " .$agent;
$data = base64_encode($data);
$return = send($data, $domain);


$success = $domain." - ".$return;
f($success);
echo $success;



//Error Handling for action type and empty error variable
}else{
  $error = "ACTION WASNT NEWORDER OR ERROR VARIABLE WASNT EMPTY: ".$action. " | " .$product_codename. " | " .$customer_emailaddress. " | " .$customer_phone. " | " .$subid3. " | " .$subid4. " | " .$subid5. " | " .$ip. " | " .$agent;
  f($error);
  echo $error;
}
}else{
  echo "no action type detected";
}

  ?>