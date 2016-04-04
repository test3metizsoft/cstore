<?php

include('inc/config.php');
//echo "hi"; exit;
echo "Login ID : $sessionId";

$filters = array('category_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['catid']) && $_REQUEST['catid'] != ''){
    $in = array('entity_id' => array('eq'=>trim($_REQUEST['catid'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['cname']) && $_REQUEST['cname'] != ''){
    $in = array('name' => array('like'=>trim($_REQUEST['cname']).'%'));
    $filters['category_param'] = $filters['category_param'] + $in;
}
if(isset($_REQUEST['subcat_of']) && $_REQUEST['subcat_of'] != ''){
    $in = array('parent_id' => array('eq'=>trim($_REQUEST['subcat_of'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}
 //echo print_r($filters); exit; 
try {

  // $result_new = $proxy->call($sessionId, 'resource_ginger.getCategories');
	
	$result_new	= $proxy->call($sessionId,'catalog_category.tree');
	//$result_new = $proxy1->call($sessionId1,'resource_ginger.getCategories',$filters);
	
	}

catch (Exception $e) {
    echo $e->getMessage();
}
$result = json_encode($result);

//var_dump($result);
	
	//echo '<pre>';print_r($result_new);echo '</pre>';

?>