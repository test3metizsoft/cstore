<?php

include('inc/config.php');

//echo "Login ID : $sessionId";

$filters = array('products_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['sku']) && $_REQUEST['sku'] != ''){	

    $in = array('sku' => array('eq'=>trim($_REQUEST['sku'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}

if(isset($_REQUEST['description']) && $_REQUEST['description'] != ''){	

    $in = array('description' => array('eq'=>trim($_REQUEST['description'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['short_description']) && $_REQUEST['short_description'] != ''){	

    $in = array('short_description' => array('eq'=>trim($_REQUEST['short_description'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['start_price']) && $_REQUEST['start_price'] != ''){	

    $in = array('price' => array('gteq'=>trim($_REQUEST['start_price'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['end_price']) && $_REQUEST['end_price'] != ''){	

    $in = array('price' => array('lteq'=>trim($_REQUEST['end_price'])));
    $filters['products_param'] = $filters['products_param'] + $in;
}
if(isset($_REQUEST['tax_class_id']) && $_REQUEST['tax_class_id'] != ''){	

    $in = array('tax_class_id' => array('eq'=>trim($_REQUEST['tax_class_id'])));
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
} 			   
catch (Exception $e) {
    $error = $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{
    $result = json_encode(array('Success'=>$result));
}
else
{
    $result['Error']= "No product found";
    $result = json_encode($result);
}
echo $result;
exit;
?>