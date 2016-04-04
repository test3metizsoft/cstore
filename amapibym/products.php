<?php
include('inc/config.php');

$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['sku']) && $_REQUEST['sku'] != ''){	

    $in = array('sku' => array('eq'=>trim($_REQUEST['sku'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}

if(isset($_REQUEST['entity_id']) && $_REQUEST['entity_id'] != ''){	

    $in = array('entity_id' => array('eq'=>trim($_REQUEST['entity_id'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['name']) && $_REQUEST['name'] != ''){
    
    $in = array('name' => array('like'=>trim($_REQUEST['name']).'%')); 
    $filters['products_param'] = $filters['products_param'] + $in;
    
}


if(isset($_REQUEST['pagenm']) && $_REQUEST['pagenm'] > 0){
    $in = array('PageName' => trim($_REQUEST['pagenm']));
}else{
    $in = array('pagenm' => '1');
}
$filters['display_param'] = $filters['display_param'] + $in;

if(isset($_REQUEST['pagesize']) && $_REQUEST['pagesize'] > 0){
    $in = array('PageSize' => trim($_REQUEST['pagesize']));
}else{
    $in = array('PageSize' => '10');
}
$filters['display_param'] = $filters['display_param'] + $in;

/************ Sort by ***********/
if(isset($_REQUEST['sortby'])){
    if(isset($_REQUEST['sort_order'])){
        $sort_order = strtoupper($_REQUEST['sort_order']);
    } else {
        $sort_order = 'ASC';
    }
    $arr=explode(",",$_REQUEST['sortby']);
    for($i=0;$i<count($arr);$i++){
        $sorting[$arr[$i]] = $sort_order;
    }
    $filters['sort'] = $sorting;
}
/************ Sort by ***********/

try {
    $result = $proxy->call($sessionId, 'resource_ginger.getProducts',array($filters));
    $result = json_encode(array('Success'=>$result));
} 			   
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
header('Content-type: application/json');
echo $result;
exit;
?>