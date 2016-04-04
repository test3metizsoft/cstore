<?php
/* Country related Region List */
ob_start();
include('inc/config.php');

$filters = array('county_param' => array(), 'display_param' => array(), 'sort' => array());

if(isset($_REQUEST['country_id']) && $_REQUEST['country_id'] != ''){
    $in = array('country_id' => array('eq'=>trim($_REQUEST['country_id'])));
    $filters['county_param'] = $filters['county_param'] + $in;
}

try {
    $result = $proxy->call($sessionId, 'resource_ginger.getregion', array($filters));
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
echo $result;
exit;
?>
