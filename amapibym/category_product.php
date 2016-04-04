<?php
ob_start();
include('inc/config.php');
$data = $_REQUEST['category_id'];
$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != ''){
     $in = array('entity_id' => array('eq'=>trim($_REQUEST['category_id'])));
     $filters['products_param'] = $filters['products_param'] + $in;
     $filters['products_param']['category_id'] = $_REQUEST['category_id'];
}
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != ''){
     $filters['customer_id'] = $_REQUEST['customer_id'];
}
try {
  $result = $proxy->call($sessionId, 'resource_ginger.getSearchproduct',array($filters));
  $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
   //echo $e->getMessage();
   $result['Error']='There are no products in this category';
   $result = json_encode($result);  
}
header('Content-type: application/json');
echo $result;
exit;
?>
