<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }


try     
{
	$quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
	$couponCode = $_REQUEST['coupan_code'];
    $result = $proxy->call($sessionId,'cart_coupon.add',array($quote_id[0],$couponCode));
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
	$result['Error']="Discount Coupon not added. Please try again!";
	echo json_encode($result);
}
exit;
?>