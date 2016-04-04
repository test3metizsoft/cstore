<?php
ob_start();
include('inc/config.php');
try {
    $quoteId = $proxy->call($sessionId, 'cart.create');

    $arrProducts = array(array(
        'product_id' => $_REQUEST['entity_id'],
        'qty' => $_REQUEST['qty']
    ),
);
$result1 = $proxy->call($sessionId, "cart_product.add", array($quoteId, $arrProducts));
$result = $proxy->call($sessionId, "cart_product.list", array($result1));
$result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>'0'));
}
header('Content-type: application/json');
echo $result;
exit;
?>
  
  
