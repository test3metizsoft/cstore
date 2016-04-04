<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => trim($_REQUEST['customer_id']));
   try
    {
        $result = $proxy->call($sessionId,'resource_ginger.getWishList',array($filters));
        $result = json_encode(array('Success'=>$result));
    }
   catch (Exception $e){
        $result = json_encode(array('Error'=>'No items found in wishlist!'));
   }
header('Content-type: application/json');
echo $result;
exit;
?>