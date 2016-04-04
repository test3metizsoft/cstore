<?php
ob_start();
  include('inc/config.php');

		
try {
     $session = Mage::getSingleton('core/session', array($shoppingCartId)); 
        // Create a quote, get quote identifier
    // $shoppingCartId = $proxy->call($sessionId, 'cart.create');

    $result= $proxy->call($sessionId, "cart_product.list",$session);

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
	$result['Error']='Invalid userId';
	echo json_encode($result);
}
exit;
?>
  
  
