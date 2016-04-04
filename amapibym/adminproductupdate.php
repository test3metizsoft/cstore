<?php
ob_start();
include('inc/config.php');

$result = '';
if(!$_REQUEST['product']['sku']){
    $result = array('Error'=>'Please insert SKU');
}else{
    
    //$attributeSets = $proxy->call($sessionId, 'product_attribute_set.list');
    //$attributeSet = current($attributeSets);

    $product = $_REQUEST['product'];
    try {
        //echo (string)$product['sku'];exit;
        $productId = $proxy->call($sessionId, 'catalog_product.update', array($product['id'], array(
            'categories' => $product['cat'],
            'websites' => array(1),
            'name' => $product['name'],
            'description' => $product['desc'],
            'short_description' => $product['sdesc'],
            'weight' => $product['weight'],
            'status' => $product['status'],
            'visibility' => $product['visi'],
            'price' => $product['price'],
            'upccode' => $product['upccode'],
            'retailupc' => $product['retailupc'],
            'tax_class_id' => 0,
	    'boxincase' => $product['boxincase'],
            'unitinbox' => $product['unitinbox'],
            'taxunit' => $product['taxunit']
        )));
        /*if($productId){
            foreach ($product['images'] as $key=>$image):
                //insertimages($product['sku'],'product.jpg',$key);
            endforeach;
        }*/
        $result = array('Success'=>$productId);
    }
    catch(Exception $e) {
        $result = array('Error'=>$e->getMessage());
    }
}
function insertimages($sku, $imagePath,$i){
    global $proxy;
    global $sessionId;
    
    $newImage = array(
            'file' => array(
                'name' => 'file_name',
                'content' => base64_encode(file_get_contents('product.jpg')),
                'mime'    => 'image/jpeg'
            ),
            'label'    => 'Cool Image Through Soap',
            'position' => 2,
            'exclude'  => 0
        );
        if($i == 0){
            $newImage['types'] = array('image','small_image','thumbnail');
        }
        $imageFilename = $proxy->call($sessionId, 'product_media.create', array($sku, $newImage));
        return $imageFilename;
}
echo json_encode($result);
