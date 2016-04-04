<?php
ob_start();
include('inc/config.php');
try
{
    $quoteId = $proxy->call($sessionId, 'cart.create', array('magento_store') );
    $arrProducts = array(
                'product_id' => 1,
		'qty' => 2,
		'sku' => 'testSKU',
		'quantity' => 4
        
    );
$resultCartProductAdd = $proxy->call($sessionId,'cart_product.add',array($quoteId,$arrProducts));
echo '<pre>';print_r($resultCartProductAdd);echo '</pre>'; exit;
}
 catch (Exception $e) {
   // echo $e->getMessage();
}
 exit;
 ?>