<?php
ob_start();
include('inc/config.php');
$data = $_REQUEST['customer_id'];
$filters = array('cart_param' => array(), 'display_param' => array(), 'sort' => array());

if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{          
    $in = array('customer_id' => array('eq'=>trim($_REQUEST['customer_id'])));
    $filters['cart_param'] = $filters['cart_param'] + $in;
}

try {
     $result = $proxy->call($sessionId, 'resource_ginger.getshoppingcart', array($filters));
     echo '<pre>';print_r($result);echo '</pre>'; exit;
    }

catch (Exception $e) {
   echo $e->getMessage();
}
header('Content-type: application/json');
if(!empty($result))
{
    echo '{"Success":';
    $result = json_encode($result);
    echo $result;
    echo '}';
} else {
    $result['Error']='Invalid Id';
    echo json_encode($result);
}
exit;
?>