<?php

include('inc/config.php');

//echo "Login ID : $sessionId";

$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_POST['sku']) && $_POST['sku'] != ''){	

    $in = array('sku' => array('eq'=>trim($_POST['sku'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}

if(isset($_POST['entity_id']) && $_POST['entity_id'] != ''){	

    $in = array('entity_id' => array('eq'=>trim($_POST['entity_id'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_POST['name']) && $_POST['name'] != ''){
    
    $in = array('name' => array('like'=>trim($_POST['name']).'%'));
    $filters['products_param'] = $filters['products_param'] + $in;
    
}


if(isset($_POST['pagenm']) && $_POST['pagenm'] > 0){
    $in = array('PageName' => trim($_POST['pagenm']));
}else{
    $in = array('pagenm' => '1');
}
$filters['display_param'] = $filters['display_param'] + $in;

if(isset($_POST['pagesize']) && $_POST['pagesize'] > 0){
    $in = array('PageSize' => trim($_POST['pagesize']));
}else{
    $in = array('PageSize' => '10');
}
$filters['display_param'] = $filters['display_param'] + $in;

/************ Sort by json ***********/
//http://localhost/magento/magentoapi/mageapi/products.php?sorting={"name":"ASC","price":"ASC","created_at":"ASC"}
/*if(isset($_GET['sorting'])){
    $sorting = json_decode($_GET['sorting'], true);
    $filters['sort'] = $sorting;
}*/
/************ Sort json***********/

/************ Sort by ***********/
if(isset($_POST['sortby'])){
    if(isset($_POST['sort_order'])){
        $sort_order = strtoupper($_POST['sort_order']);
    } else {
        $sort_order = 'ASC';
    }
    $arr=explode(",",$_POST['sortby']);
    for($i=0;$i<count($arr);$i++){
        $sorting[$arr[$i]] = $sort_order;
    }
    $filters['sort'] = $sorting;
}

//echo '<pre>';print_r($filters);echo '</pre>'; exit;
/************ Sort by ***********/

try {
	        
           $result = $proxy->call($sessionId, 'resource_ginger.getProducts',array($filters));
		   
    } 			   
catch (Exception $e) {
	
    echo $e->getMessage();
	//echo $e; exit; 

}
header('Content-type: application/json');
$result = json_encode($result);
if($result != 'null')
{
	echo '{"Success":';
    
    //echo json_encode($result);
	print_r($result);  
	echo "}";
}
else
{
	echo "Error :0";
	//print_r($result);  
	//echo "}";
}

//echo '<pre>';print_r($result);echo '</pre>';



?>