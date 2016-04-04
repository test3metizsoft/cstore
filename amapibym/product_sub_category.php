<?php
ob_start();
include('inc/config.php');
$parent_id = $_REQUEST['parent_id'];
$category_id = $_REQUEST['category_id'];
$filters = array('category_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != ''){
    $in = array('parent_id' => array('eq'=>trim($_REQUEST['parent_id'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != ''){
    $in = array('category_id' => array('eq'=>trim($_REQUEST['category_id'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}

try {
    $result = $proxy->call($sessionId,'catalog_category.tree',$category_id);
    $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
header('Content-type: application/json');
echo $result;
exit;
?>