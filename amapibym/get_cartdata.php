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
  $result = $proxy->call($sessionId, "cart_product.list", $quote_id[0]);
  $result = json_encode(array('Success'=>$result));
}
catch (Exception $e)
{ 
    //echo $e->getMessage();
    $result['Error'] = $e->getMessage();
    $result = json_encode($result);
}
header('Content-type: application/json');
echo $result;
exit;
?>