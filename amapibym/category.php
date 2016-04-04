<?php
ob_start();
include('inc/config.php');

$filters = array('category_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['catid']) && $_REQUEST['catid'] != ''){
    $in = array('entity_id' => array('eq'=>trim($_REQUEST['catid'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['cname']) && $_REQUEST['cname'] != ''){
    $in = array('name' => array('eq'=>trim($_REQUEST['cname'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['subcat_of']) && $_REQUEST['subcat_of'] != ''){
    $in = array('parent_id' => array('eq'=>trim($_REQUEST['subcat_of'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['level']) && $_REQUEST['level'] != ''){
    $in = array('level' => array('eq'=>trim($_REQUEST['level'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
try {
   $result = $proxy->call($sessionId, 'resource_ginger.getCategories',$filters);
}	
catch (Exception $e) {
    echo $e->getMessage();
}
header('Content-type: application/json');
if(!empty($result))
{
    $result = json_encode(array('Success'=>$result));
}
else
{
    $result['Error']='Invalid userId';
    $result = json_encode($result);
}
echo $result;
exit;
// $result = $proxy->call($sessionId, 'resource_ginger.getCategories',$filters);
// echo '<pre>';print_r($result);echo '</pre>';
?>
