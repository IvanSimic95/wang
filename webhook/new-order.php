<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/vars.php';

$errorDisplay = "";


isset($_GET['order']) ? $orderID        = $_GET['order']    : $errorDisplay .= " Missing Order ID <br>";
isset($_GET['price']) ? $order_price    = $_GET['price']    : $errorDisplay .= " Missing Price <br>";
isset($_GET['email']) ? $order_email    = $_GET['email']    : $errorDisplay .= " Missing Email <br>";
isset($_GET['name'])  ? $name          = $_GET['name']     : $errorDisplay .= " Missing Name <br>";
isset($_GET['BGid'])  ? $bgOrderID      = $_GET['BGid']     : $errorDisplay .= " Missing Buygoods Order ID <br>";


$signature = hash_hmac('sha256', strval($orderID), 'sk_live_Ncow50B9RdRQFeXBsW45c5LFRVYLCm98');

empty($errorDisplay) ?  $testError = FALSE : $testError = TRUE;
if($testError == TRUE){
echo $errorDisplay;
$errorDisplay = str_replace('<br>', '', $errorDisplay);
$logArray[] = $errorDisplay;
formLogNewAgain($logArray);
}else{

  //Find Correct Order
  $sql = "SELECT * FROM `orders` WHERE `order_id` = '$orderID' ORDER BY  `order_id` DESC LIMIT 1";
  $result = $conn->query($sql);
  $count = $result->num_rows;

  //If order is found input data from BG and update status to paid
  if($result->num_rows != 0) {
    
    $row = $result->fetch_assoc();
    $orderStatus = $row['order_status'];

        if($orderStatus=="pending" OR $orderStatus=="paid" OR $orderStatus=="processing"){
            $sql = "UPDATE `orders` SET `order_email`='$order_email', `order_price`='$order_price', `buygoods_order_id`='$bgOrderID', `order_status`='paid' WHERE order_id='$orderID'";
            $result = $conn->query($sql);


//First create TalkJS User with same ID as conversation
$ch = curl_init();
$data = [
"id" => $orderID,
"name" => $name,
"email" => [$order_email],
"role" => "customer",
"photoUrl" => "https://avatars.dicebear.com/api/adventurer/".$order_email.".svg?skinColor=variant02",
"custom" => ["email" => $order_email, "lastOrder" => $orderID]
];
$data1 = json_encode($data);
curl_setopt($ch, CURLOPT_URL, 'https://api.talkjs.com/v1/ArJWsup2/users/'.$orderID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    
curl_setopt($ch, CURLOPT_POSTFIELDS, $data1);
    
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer sk_live_Ncow50B9RdRQFeXBsW45c5LFRVYLCm98';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
$result = curl_exec($ch);
if (curl_errno($ch)) {
echo 'Error:' . curl_error($ch);
}
curl_close($ch);
echo $result;


//Now create new conversation
$ch2 = curl_init();
$data2 = [
"subject" => "Order #".$orderID,
"participants" => ["administrator", $orderID],
"custom" => ["status" => "Paid"]
];
$data22 = json_encode($data2);
curl_setopt($ch2, CURLOPT_URL, 'https://api.talkjs.com/v1/ArJWsup2/conversations/'.$orderID);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'PUT');

curl_setopt($ch2, CURLOPT_POSTFIELDS, $data22);

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer sk_live_Ncow50B9RdRQFeXBsW45c5LFRVYLCm98';
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);

$result2 = curl_exec($ch2);
if (curl_errno($ch2)) {
    echo 'Error:' . curl_error($ch2);
}
curl_close($ch2);
echo $result2;



            echo "Order Status is updated to Paid! <br>";
            echo "Additional order data is saved!";
        }else{
            echo "Order Status is already: ".$orderStatus;
            echo "<br> No Changes were made to order data.";
        }


  }

}

?>