<?php
ob_start();
include('inc/config.php');

$category_id =  $_REQUEST['category_id'];
try
    {
$productArr = $proxy->call($sessionId,'catalog_category.assignedProducts',array($category_id));
foreach ($productArr as $product) {
    $productListing = $proxy->call($sessionId, 'catalog_product.info', $product['sku']);
    //$result = $proxy->call($sessionId, 'catalog_product_attribute_media.list', $product['sku']);
 // $productListing = $proxy->call($sessionId, 'product.info', $product['sku']);
    $result = $proxy->call($sessionId, 'catalog_product_attribute_media.list', $product['sku']);
     $extnDetails[] = array (
        'product_id'=>$productListing['product_id'],
        'name'=>$productListing['name'],        
        'sku' =>$productListing['sku'],
        'weight' =>$productListing['weight'],
        'price' =>$productListing['price'],
     // 'special_price' =>$productListing['special_price'],
        'status' =>$productListing['status'],
        'description' =>$productListing['short_description'],
        'image' => $result[0]['url']
       //'url' =>$productListing['url_path'],
        //'category_ids' => $productListing['category_ids'],
        );   
    }
}
catch (Exception $e)
    {
  //  echo $e->getMessage();
    }
header('Content-type: application/json'); 

if(!empty($extnDetails))
    {
        echo '{"Success":';	
//$result['Success']=$result;
	$extnDetails = json_encode($extnDetails);
	echo $extnDetails;
        echo '}';
    }
else 
    {
        //echo '{"There are no products for the category":';
        $extnDetails['Error']='There are no products for the category';
	echo json_encode($extnDetails);
        
    }
exit;
?>
