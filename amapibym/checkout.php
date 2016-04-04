<?php
include('inc/config.php');
echo "Login ID : $sessionId";

$filters = array('category_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['cartid']) && $_REQUEST['cartid'] != ''){
    $in = array('cart_id' => array('eq'=>trim($_REQUEST['cartid'])));
    $filters['checkout_param'] = $filters['checkout_param'] + $in;
}

try {
    $result = $proxy->call( $sessionId, 'cart.create',$filter);
    echo $result; exit;
}

catch (Exception $e) {
    echo $e->getMessage();
}
$result = json_encode($result);
echo '<pre>';print_r($result);echo '</pre>';

?>