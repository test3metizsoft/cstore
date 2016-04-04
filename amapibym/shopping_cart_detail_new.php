<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => trim($_REQUEST['customer_id']));
try {
    $shoppingCartId = $proxy->call( $sessionId, 'cart.create',   
    $customerAsGuest = array(        
        'firstname' => "test",
	'lastname' => "test1",
	'email' => "mail@gmail.com",
        'website_id' => 0,
	'store_id' => "15",
	'mode' => "guest"
));
var_dump($shoppingCartId); exit;
    $resultdata = $proxy->call($sessionId, 'cart_customer.set', array( $shoppingCartId, $customerAsGuest,$store_id ));
    print_r($resultdata); exit;
    $result= $proxy->call($sessionId, 'cart_product.add', array($resultdata));

    echo '<pre>';print_r($result);echo '</pre>'; exit;
}

catch (Exception $e) {
   echo $e->getMessage();
}
header('Content-type: application/json');
if(!empty($result))
    
{
    echo '{"Success":';	
	$result = json_encode($result);
	echo $result;
        echo '}';
}
else
{
	$result['Error']='Invalid Id';
	echo json_encode($result);
}
exit;
?>