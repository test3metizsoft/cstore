<?php
include('inc/config.php');
header('Content-type: application/json');

/************ Sort by ***********/
$temp = $_REQUEST['customer_id'];
try {
    $result = $proxy->call($sessionId, 'customer_address.list',$temp);
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
   //echo $e->getMessage();
   $result['Error']='No address found!';
   $result = json_encode($result);
}
header('Content-type: application/json');    
echo $result;
exit;
?>