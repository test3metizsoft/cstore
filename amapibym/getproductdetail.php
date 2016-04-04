<?php
ob_start();
include('inc/config.php');
$id = $_REQUEST['id'];

$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != ''){
     $in['customer_id'] = $_REQUEST['customer_id'];
}
if(isset($id) && $id != ''){
    $in['id'] = $id;
}
try {
    $result = $proxy->call($sessionId, 'resource_ginger.getProductdetail',array($in));
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    // echo $e->getMessage();
    $result = json_encode(array('Error'=>'Product Not Found'));
}

header('Content-type: application/json');
echo $result;