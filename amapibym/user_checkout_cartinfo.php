<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {
         $filters['customer_id']= $_REQUEST['customer_id'];
       }

//$filters['customer_id']= $_GET['customer_id'];
try {
    // update product in shopping cart
// get full information about shopping cart

 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters));
 
 $arrAddresses = array(
    array(
        "mode" => "shipping",
        "firstname" => "testFirstname",
        "lastname" => "testLastname",
        "company" => "testCompany",
        "street" => "testStreet",
        "city" => "testCity",
        "region" => "testRegion",
        "postcode" => "testPostcode",
        "country_id" => "id",
        "telephone" => "0123456789",
        "fax" => "0123456789",
        "is_default_shipping" => 0,
        "is_default_billing" => 0
    ),
    array(
        "mode" => "billing",
        "firstname" => "testFirstname",
        "lastname" => "testLastname",
        "company" => "testCompany",
        "street" => "testStreet",
        "city" => "testCity",
        "region" => "testRegion",
        "postcode" => "testPostcode",
        "country_id" => "id",
        "telephone" => "0123456789",
        "fax" => "0123456789",
        "is_default_shipping" => 0,
        "is_default_billing" => 0
    )
);
$resultCustomerAddresses = $proxy->call($sessionId, "cart_customer.addresses", array($quote_id[0], $arrAddresses));
 
 //$result = $proxy->call($sessionId, "sales_order.list",array($filters));
 $result = $proxy->call($sessionId, "cart.info", array($quote_id[0]));
 //echo '<pre>'; print_r($result); exit;
 
 
 $cart['quote_id'] = $result['quote_id'];
 $cart['items_count'] = $result['items_count'];
 $cart['items_qty'] = (int)$result['items_qty'];
 $cart['grand_total'] = $result['grand_total'];
 $cart['subtotal'] = $result['subtotal'];
 $cart['discount_amount'] = $result['shipping_address']['discount_amount'];
 $cart['discount_description'] = $result['shipping_address']['discount_description'];
 $cart['shipping_incl_tax'] = $result['shipping_address']['shipping_incl_tax'];
 $cart['coupon_code'] = $result['coupon_code'];
 $cart['items'] = array();
 $cart_items = array();
 //$cart = $result;
 $j=0;
 foreach($result['items'] as $item){
	$prod_info[$j] = array('catalog_product.info', $item['product_id']);//$proxy->call($sessionId, 'catalog_product.info', $item['product_id'],'','array("product_id","image","description")');
	$images[$j] = array('catalog_product_attribute_media.list', $item['product_id']);//$proxy->call($sessionId, 'catalog_product_attribute_media.list', $item['product_id']);
	$j++;
 }

 $i = 0;
 $ProductInfo = $proxy->multiCall($sessionId, $prod_info);
 $ProdImages = $proxy->multiCall($sessionId, $images);

  foreach($result['items'] as $item){
	// $prod_info = $proxy->call($sessionId, 'catalog_product.info', $item['product_id'],'','array("product_id","image","description")');
	// $images = $proxy->call($sessionId, 'catalog_product_attribute_media.list', $item['product_id']);

 	$cart_items[$i]['product_id'] = $item['product_id'];
 	$cart_items[$i]['description'] = $ProductInfo[$i]['description'];
	$cart_items[$i]['additionalDescription'] = $ProductInfo[$i]['short_description'];
	$cart_items[$i]['url_path'] = $baseUrl . $ProductInfo[$i]['url_path'];
 	$cart_items[$i]['sku'] = $item['sku'];
 	$cart_items[$i]['name'] = $item['name'];
 	$cart_items[$i]['qty'] = $item['qty'];
        $cart_items[$i]['statetax'] = $item['statetax'];
        $cart_items[$i]['countytax'] = $item['countrytax'];
        $cart_items[$i]['citytax'] = $item['citytax'];
 	$cart_items[$i]['price_incl_tax'] = $item['price_incl_tax'];
 	$cart_items[$i]['row_total_incl_tax'] = $item['row_total_incl_tax'];
	$cart_items[$i]['image']= $ProdImages[$i][0]['url'];
 	$i++;
 }

 $cart['items'] = $cart_items;
}

catch (Exception $e) {
    $error =  $e->getMessage();
}
// echo "<pre>";print_r($ProdImages);exit;
header('Content-type: application/json');

if(!empty($result))
{
    $results['Success'] = $cart;
	$cart= json_encode($results);
	echo $cart;
}
else
{
	$cart['Error']='Cart details not found';
	echo json_encode($cart);
}
exit;
?>