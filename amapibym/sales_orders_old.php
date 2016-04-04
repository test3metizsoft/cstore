<?php
include('inc/config.php');
//header('Content-type: application/json');

$filters = array('sales_flat_order' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
   $in = array('customer_id' => array('eq'=>trim($_REQUEST['customer_id'])));
   $filters['sales_flat_order'] = $filters['sales_flat_order'] + $in;

}
   try
    {
       $result = $proxy->call($sessionId,'order.list',$filters);
       //print_r($result);
	   //exit;

    }

   catch (Exception $e)
    {
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
	$result['Error']='Invalid UserId';
	echo json_encode($result);
}
?>  