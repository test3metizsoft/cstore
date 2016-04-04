<?php
ob_start();
include('inc/config.php');
$queryString = $_REQUEST['sku'];
$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['sku']) && $_REQUEST['sku'] != ''){
    $in = array('SKU' => array('eq'=>trim($_REQUEST['sku'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['upc']) && $_REQUEST['upc'] != ''){
    $in = array('upccode' => array('eq'=>trim($_REQUEST['upc'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['retailupc']) && $_REQUEST['retailupc'] != ''){
    $in = array('retailupc' => array('eq'=>trim($_REQUEST['retailupc'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
try {
    $result = $proxy->call($sessionId, 'resource_ginger.getskuSearch',array($filters));
    $result = json_encode(array('Success'=>$result[0]));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>'Product Not Found'));
}
//echo '<pre>';print_r($result);exit;
header('Content-type: application/json');
echo $result;