<?php
ob_start();
include('inc/config.php');

$filters = array('review_detail' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
    $in = array('customer_id' => array('eq'=>trim($_REQUEST['customer_id'])));    
   $filters['review_detail'] = $filters['review_detail'] + $in;
}
  
if(isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '')
{
    $in = array('entity_pk_value' => array('eq'=>trim($_POST['product_id'])));    
   $filters['review_detail'] = $filters['review_detail'] + $in;
} 
   try
    {
        $result = $proxy->call($sessionId,'resource_ginger.getProductreview',array($filters));
        $result = json_encode(array('Success'=>$result));
    }
   catch (Exception $e)
   {
        $result = json_encode(array('Error'=>'No review found!'));
   }
header('Content-type: application/json');
echo $result;