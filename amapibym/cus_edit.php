<?php
ob_start();
include('inc/config.php');

try {

 $result = $proxy->call($sessionId,'customer.update',array
     ('customerId' => $_REQUEST['customer_id'], 
         'customerData' => array(
         'firstname' => $_REQUEST['firstname'],
         'lastname' => $_REQUEST['lastname'],
         'email' => $_REQUEST['email'],
         'password_hash' => $_REQUEST['password_hash']
         )));
 $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
   $result['Error']='Please edit data first';
   $result = json_encode($result);
}
header('Content-type: application/json');    
echo $result;
exit;
?>
