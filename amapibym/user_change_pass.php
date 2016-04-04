<?php
ob_start();
include('inc/config.php');

$customer = $_REQUEST['customer_id'];
$email = $_REQUEST['email'];
$password = $_REQUEST['password'];
$newpassword = $_REQUEST['new_password'];

$filters = array(
    'entity_id' => array('like'=>$_REQUEST['customer_id'])
 );


$user = $proxy->call($sessionId, 'customer.list',array($filters));

function validateHash($password, $hash)
{
    $hashArr = explode(':', $hash);
    
    switch (count($hashArr)) {
	
        case 1:
            return md5($password) === $hash;
        case 2:
            return md5($hashArr[1] . $password) === $hashArr[0];
    }
}
header('Content-type: application/json');

    if(validateHash($password,$user[0]['password_hash']))
{
        $result = $proxy->call($sessionId, 'customer.update', array('customerId' => $user[0]['customer_id'], 
    'customerData' => array('password_hash' => md5($newpassword))));
        
        
echo '{"Success":"Password Successfully Reset"}';
}
else
{
   echo '{"Error":"Password Not match"}';
}
exit;
?>