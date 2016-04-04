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
        "product_id" => $_REQUEST['product_id']
    ),
);

    $arrProducts_update = array(
        array(
            "product_id" =>$_REQUEST['product_id'],
            "qty" => '0'
        ),
    );




 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters));
 $result = $proxy->call($sessionId, "cart_product.remove", array($quote_id[0], $arrProducts));

 $result1 = $proxy->call($sessionId,'cart_product.update',array($quote_id[0],$arrProducts));



    //  echo '<pre>';print_r($result);echo '</pre>'; exit;
}
catch (Exception $e) {
    //echo $e->getMessage();
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
	$result['Error']='Product not removed';
	echo json_encode($result);
}
exit;
?>
  
  
