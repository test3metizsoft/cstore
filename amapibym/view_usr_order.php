<?php

include('inc/config.php');

$filters = array('order_param' => array(), 'display_param' => array(), 'sort' => array());
if (isset($_REQUEST['customer_firstname']) && $_REQUEST['customer_firstname'] != '') {

    $in = array('customer_firstname' => array('eq' => trim($_REQUEST['customer_firstname'])));
    // echo print_r($in); exit;
    $filters['order_param'] = $filters['order_param'] + $in;
}

if (isset($_REQUEST['pagesize']) && $_REQUEST['pagesize'] > 0) {
    $in = array('PageSize' => trim($_REQUEST['pagesize']));
} else {
    $in = array('PageSize' => '10');
}
$filters['display_param'] = $filters['display_param'] + $in;



try {
    //$result = $proxy->call($sessionId,'sales_order.list',array($filters));
    $result = $proxy->call($sessionId, 'resource_ginger.getOrderlist', array($filters));
} catch (Exception $e) {
    echo $e->getMessage();
}

if (!empty($result)) {
    $result['Success'] = $result;
    $result = json_encode($result);
    echo $result;
} else {
    $result['Error'] = '';
    echo json_encode($result);
}
exit;
?>
