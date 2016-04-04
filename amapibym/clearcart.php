<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
if(isset($_REQUEST['cid']) && $_REQUEST['cid'] != '')
{
    $filters['customer_id']= $_REQUEST['cid'];
}
try {
   $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters));
   if($quote_id[0]){
       $results['Success'] = $proxy->call($sessionId, 'resource_ginger.clearCart', array(array('quoteid'=>$quote_id[0])));
   }else{
       $results['Success'] = true;
   }
}	
catch (Exception $e) {
    $results['Error'] = $e->getMessage();
}
header('Content-type: application/json');
echo json_encode($results);
exit;
?>
