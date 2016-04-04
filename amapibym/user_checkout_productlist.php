<?php
ob_start();
include('inc/config.php');

   $filters = array(
   'quoteId' => array('like'=>$_REQUEST['quoteId'])
    );         
          
try {
      
// Create a quote, get quote identifier
$shoppingCartId = $proxy->call($sessionId, 'cart.create');
  //  print_r ($shoppingCartId); 
// get list of products
$shoppingCartProducts = $proxy->call($sessionId, 'cart_product.list', array($shoppingCartId,$filters));
    print_r($shoppingCartProducts);

}
catch (Exception $e) {
    echo $e->getMessage();
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
  
  
