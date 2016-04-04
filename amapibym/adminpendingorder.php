<?php
ob_start();
include('inc/config.php');
$queryString = $_REQUEST['sku'];
$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
try {
    $result = $proxy->call($sessionId, 'resource_ginger.getPendingOrders',array($filters));
    //$result = json_encode(array('Success'=>$result[0]));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
//echo '<pre>';print_r($result);exit;
header('Content-type: application/json');
print_r(json_encode($result)); exit;
echo $result;