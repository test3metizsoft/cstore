<?php
ob_start();
include('inc/config.php');

$password = $_REQUEST['password'];

$email = $_REQUEST['email'];

$filters = array(
    'email' => array('like'=>$_REQUEST['email'])
);

$user = $proxy->call($sessionId, 'customer.list', array($filters));

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
    $quote_id = $proxy->call($sessionId, 'resource_ginger.getLogin', array($user));

    echo '{"Success":';
    $user = json_encode($user);
    echo $user;
    echo '}';
}
else
{
    echo '{"Error":"Incorrect login. Please try again!"}';
}
exit;
?>