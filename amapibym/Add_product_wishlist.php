<?php
ob_start();
error_reporting(0);
include('inc/config.php');

      	  
 $filters = array('wishlist_param' => array(), 'display_param' => array(), 'sort' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $in = array('customer_id' => array('eq'=>trim($_REQUEST['customer_id'])));
         $filters['wishlist_param'] = $filters['wishlist_param'] + $in;
              
       }
	   if(isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '')
       {
         $in = array('entity_id' => array('eq'=>trim($_REQUEST['product_id'])));
         $filters['wishlist_param'] = $filters['wishlist_param'] + $in;
       }
	 
    try
       {     
        $result = $proxy->call($sessionId, 'resource_ginger.addNewItem',array($filters)); 
        $result = json_encode(array('Success'=>$result));
       }  
    catch (Exception $e) 
    {
        //$result['Error']= $e->getMessage();
        $result['Error']='Product not added to wishlist';
	$result = json_encode($result);
    }

header('Content-type: application/json');
echo $result;
exit;
?>