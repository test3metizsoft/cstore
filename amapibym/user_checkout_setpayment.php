<?php
ob_start();
include('inc/config.php');
	
$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }

       
try {      

$paymentMethod = array(
   // "method" => "checkmo"
        'po_number' => null,
	"method" => $_REQUEST['payment_method'],
	'cc_number' => $_REQUEST['cc_number'],
	'cc_type' => null,
        'cc_cid' => null,
        'cc_owner' => null,
	'cc_exp_year' => $_REQUEST['cc_exp_year'],
	'cc_exp_month' => $_REQUEST['cc_exp_month']
);

    
 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
 
 $result = $proxy->call($sessionId, "cart_payment.method", array($quote_id[0], $paymentMethod));

}
catch (Exception $e) {
    $error = $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{
    //echo '{"Success":';	
	$results['Success']=$result;
	$result = json_encode($results);
	echo $result;
        // echo '}';
}
else
{
	$result['Error']=$error;
	echo json_encode($result);
}
exit;
?>