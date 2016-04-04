<?php
ob_start();
  include('inc/config.php');
 $filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }
           if(isset($_REQUEST['reserved_order_id']) && $_REQUEST['reserved_order_id'] != '')
       {          
         $filters['order_id']= $_REQUEST['reserved_order_id'];
       }
  
try {
// Create a quote, get quote identifier
$quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters));

$result = $proxy->call($sessionId, 'cart.order', array($quote_id[0]));
}
catch (Exception $e) {
    $error= $e->getMessage();
}
header('Content-type: application/json');
//echo '<pre>';print_r($quote_id);
if(!empty($result))
{
    
    $response = $proxy->call($sessionId, "cart.info", array($quote_id[0]));
    $arrProducts = array();
    foreach ($response['items'] as $item) {
        $arrProducts[] = array(
                           "product_id" => $item['product_id']
                        );
    }
    $proxy->call($sessionId, "cart_product.remove", array($quote_id[0], $arrProducts));
    echo '{"Success":';	
	$result = json_encode($result);
	echo $result;
        echo '}';
}
else
{
	$result['Error']='Order not placed. Please try again!';
	echo json_encode($result);
}
exit;
?>
  
  
