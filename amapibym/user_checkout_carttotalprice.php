<?php
ob_start();
include('inc/config.php');
	
$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }

       
try {      

    
    // get total prices

//print_r( $shoppingCartTotals );
 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 

 $result = $proxy->call($sessionId, "cart.totals", array($quote_id[0]));
       // echo '<pre>';print_r($result);echo '</pre>'; exit;
}
catch (Exception $e) {
    $error =  $e->getMessage();
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
	$result['Error']='Error displaying total. Please try again!';
	echo json_encode($result);
}
exit;
?>
  
  
