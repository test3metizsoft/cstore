<?php
ob_start();
include('inc/config.php');
$level = $_REQUEST['level'];

$filters = array('category_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['level']) && $_REQUEST['level'] != ''){
    $in = array('level' => array('eq'=>trim($_REQUEST['level'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != ''){
    $in = array('parent_id' => array('eq'=>trim($_REQUEST['parent_id'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
try {
    if(isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id']) || isset($_REQUEST['level']) && !empty($_REQUEST['level']) ){
        $result = $proxy->call($sessionId, 'resource_ginger.getrootcategory', array($filters));
    }else{
        //$result = $proxy->catalogCategoryTree((object)array('sessionId' => $sessionId, 'is_active' => '1'));
        $result = $proxy->call($sessionId, 'catalog_category.tree',array('is_active' => 1));
    }
  $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
header('Content-type: application/json');
echo $result;
exit;
?>

