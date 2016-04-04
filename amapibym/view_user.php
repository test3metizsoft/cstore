<?php
include('inc/config.php');

$filters = array('customer_param' => array(), 'display_param' => array(), 'sort' => array());
if (isset($_REQUEST['firstname']) && $_REQUEST['firstname'] != '') {

    $in = array('firstname' => array('eq' => trim($_REQUEST['firstname'])));
    
    $filters['display_param'] = $filters['display_param'] + $in;
    
}


if (isset($_REQUEST['pagesize']) && $_REQUEST['pagesize'] > 0) {
    $in = array('PageSize' => trim($_REQUEST['pagesize']));
} else {
    $in = array('PageSize' => '10');
}
$filters['display_param'] = $filters['display_param'] + $in;

try {
  
    $result = $proxy->call($sessionId, 'resource_ginger.getUserlist', $filters);
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
