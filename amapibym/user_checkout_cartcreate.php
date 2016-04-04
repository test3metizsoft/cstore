<?php
ob_start();
  include('inc/config.php');

		
try {
      
// Create a quote, get quote identifier
$shoppingCartId = $proxy->call($sessionId, 'cart.create');
   // echo '<pre>';print_r($shoppingCartId);echo '</pre>'; exit;
// Set customer, for example guest
$customerAsGuest = array(
    'firstname' => $_REQUEST['firstname'],
    'lastname' => $_REQUEST['lastname'],
    'email' => $_REQUEST['email'],
    'website_id' => $_REQUEST['website_id'],
    'store_id' => $_REQUEST['store_id'],
    'mode' => "guest"
);
$result = $proxy->call($sessionId, 'cart_customer.set', array( $shoppingCartId, $customerAsGuest) );
   // echo '<pre>';print_r($resultCustomerSet);echo '</pre>'; exit;

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
  
  
