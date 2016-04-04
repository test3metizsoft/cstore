<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }

file_put_contents("test-couponremove.txt",$_REQUEST);
try     
{
	$quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
	// remove coupon
	$result = $proxy->call($sessionId, "cart_coupon.remove", array($quote_id[0])); 
}
   catch (Exception $e)
    {
    $error = $e->getMessage();
    }
header('Content-type: application/json');

if(!empty($result))
{
    $results['Success'] = $result;
	$result = json_encode($results);
	echo $result;
}
else
{
	$result['Error']="Discount coupon not removed. Please try again!";
	echo json_encode($result);
}
exit;
?>