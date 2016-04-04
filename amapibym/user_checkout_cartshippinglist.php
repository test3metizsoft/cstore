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
//print_r($quote_id);
$result = $proxy->call($sessionId, "cart_shipping.list", array($quote_id[0]));
          //echo '<pre>';print_r($result);echo '</pre>'; exit;
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
	$result['Error']='No Shipping address found';
	echo json_encode($result);
}
exit;
?>
  
  
