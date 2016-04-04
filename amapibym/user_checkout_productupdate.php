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
$arrProducts = array(
    array(
        "product_id" =>$_REQUEST['product_id'],
        "qty" => $_REQUEST['qty']
    ),
);

 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
 $result = $proxy->call($sessionId,'cart_product.update',array($quote_id[0],$arrProducts));
      //  echo '<pre>';print_r($result);echo '</pre>'; exit;
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
	$result['Error']='Cart not updated';
	echo json_encode($result);
}
exit;
?>
  
  
