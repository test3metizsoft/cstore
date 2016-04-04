<?php
include('inc/config.php');

try {
    $result1=$proxy->call($sessionId,'resource_ginger.getUserlogin',array($customer));
    $result = $proxy->call($sessionId, 'customer.info',$result1);
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
header('Content-type: application/json');
echo $result;
exit;
?>