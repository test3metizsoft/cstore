<?php
ob_start();
  include('inc/config.php');

 $filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }
  
try {
      
// Create a quote, get quote identifier
$quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters));

$resultShippingMethods = $proxy->call($sessionId, "cart_shipping.list", array($quote_id[0]));

$randShippingMethodIndex = rand(1, count($resultShippingMethods));
$shippingMethod = $resultShippingMethods[$randShippingMethodIndex]["shipping"];

$result = $proxy->call($sessionId, "cart_shipping.method", array($quote_id[0], $shippingMethod));
          //echo '<pre>';print_r($result);echo '</pre>'; exit;
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
  
  
