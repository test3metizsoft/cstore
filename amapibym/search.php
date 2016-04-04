<?php
ob_start();
include('inc/config.php');
$queryString = $_REQUEST['name'];
$queryString1 = $_REQUEST['category_ids'];
$customer_id = $_REQUEST['customer_id'];
$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['name']) && $_REQUEST['name'] != ''){
    $in = array('name' => array('like'=>'%'.trim($_REQUEST['name']).'%'));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != ''){
    $filters['customer_id'] = $_REQUEST['customer_id'];
}
try {
    $result = $proxy->call($sessionId, 'resource_ginger.getSearch',array($filters));
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    // echo $e->getMessage();
    $result = json_encode(array('Error'=>'Product Not Found'));
}

header('Content-type: application/json');
echo $result;