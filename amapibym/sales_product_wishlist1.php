<?php
ob_start();
include('inc/config.php');
//header('Content-type: application/json');

 $customer = $_REQUEST['customer_id'];

$filters = array('wishlist_detail' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
    $in = array('customer_id' => array('eq'=>trim($_REQUEST['customer_id'])));    
   $filters['wishlist_detail'] = $filters['wishlist_detail'] + $in;
   }
   try
    {
        
    $result = $proxy->call($sessionId,'resource_ginger.getProductwishlist1');
        //$result = $proxy->call($sessionId,'review.list',$filters);
       	   //exit;

    }
   catch (Exception $e)
    {
    echo $e->getMessage();
    }
header('Content-type: application/json');
echo'<pre>'; print_r($result); echo'</pre>'; exit;
if(!empty($result))
{
    
	//$result['Success']=$result;        
	$result = json_encode($result);
	echo $result;
}
else
{
	$result['Error']='';
	echo json_encode($result);
}
exit;
?>