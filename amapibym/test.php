<?php
ob_start();
include('inc/config.php');

      	  
 $filters = array('customer_id' => $_REQUEST['customer_id'], 'product_id' => $_REQUEST['product_id'],
     'title' => $_REQUEST['title'],
     'detail' => $_REQUEST['detail'],
     'nickname' => $_REQUEST['nickname'],
     'sort' => array());
     

    try {     
        $result = $proxy->call($sessionId, 'resource_ginger.addnewReview',array($filters));
        $result = json_encode(array('Success'=>$result));
    }  
    catch (Exception $e) {
        //$result['Error']= $e->getMessage();
        $result['Error']='Review not added. Please try again!';
        $result= json_encode($result);
    }

header('Content-type: application/json');
echo $result;
exit;
?>