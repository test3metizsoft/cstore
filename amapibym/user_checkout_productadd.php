<?php
ob_start();
include('inc/config.php');
		
try {
      
// Create a quote, get quote identifier
$shoppingCartId = $proxy->call($sessionId, 'cart.create');
$arrProducts = array(array(
        'product_id' => $_REQUEST['product_id'],
        'qty' => $_REQUEST['qty']
    ),
    array(
        'sku' => $_REQUEST['sku'],
        'quantity' => $_REQUEST['qty']
    )
);
$result1 = $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
$result = $proxy->call($sessionId, "cart_product.list", array($shoppingCartId,$result));
}
catch (Exception $e) {
   // echo $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{
    echo '{"Success":';	
//$result['Success']=$result;
	$result = json_encode($result);
	echo $result;
        echo '}';
}
else
{
	$result['Error']='0';
	echo json_encode($result);
}
exit;
?>
  
  
