<?php
ob_start();
include('inc/config.php');
$queryString = $_REQUEST['sku'];
//$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['proid']) && $_REQUEST['proid'] != ''){
    $proid = $_REQUEST['proid'];
    $file = $_REQUEST['file'];
}
try {
    $result = $proxy->call($sessionId, 'catalog_product_attribute_media.remove', array('product' => $proid, 'file' => $file));
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>'Product Not Found'));
}
//echo '<pre>';print_r($result);exit;
header('Content-type: application/json');
echo $result;