<?php
ob_start();
include('inc/config.php');

$filters = array('category_param' => array(), 'display_param' => array(), 'sort' => array());
    if(isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != ''){
    $in = array('category_id' => array('eq'=>trim($_REQUEST['category_id'])));
    $filters['category_param'] = $filters['category_param'] + $in;
}

try {
    
   $result = $proxy->call($sessionId, 'category.tree');
   
}
catch (Exception $e) {
    echo $e->getMessage();
}

header('Content-type: application/json');

if($result != null)
{
	$result['Success']=$result;
	$result = json_encode($result);
	echo $result;
}
else
{
	$result['Error']='Invalid user';
	echo json_encode($result);
}
exit;
?>

