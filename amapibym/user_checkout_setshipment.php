<?php
ob_start();
include('inc/config.php');
	
$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }

       
try {      
    // update product in shopping cart
// get full information about shopping cart

 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
 //$result = $proxy->call($sessionId, "sales_order.list",array($filters));
 $result = $proxy->call($sessionId, "cart_shipping.method", array($quote_id[0],$_REQUEST['shipment_method']));
 
}
catch (Exception $e) {
    $error =  $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{
    $results['Success'] = $result;	
	$cart= json_encode($results);
	echo $cart;
}
else
{
	$cart['Error']='Shipment method not set';
	echo json_encode($cart);
}
exit;
?>
  
  
