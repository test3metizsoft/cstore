<?php
include('inc/config.php');
$filters = array('sales_flat_order' => array(), 'display_param' => array(), 'sort' => array());
   try
    {
        $result = $proxy->call($sessionId,'sales_order.info',$_REQUEST['increment_id']);
       $order = array();
       $order['increment_id'] = $result['increment_id'];
       $order['order_id'] = $result['order_id'];
       $order['created_at'] = $result['created_at'];
       $order['status'] = $result['status'];
       $order['subtotal'] = $result['subtotal'];
       $order['grand_total'] = $result['grand_total'];
       
       $order['increment_id'] = $result['increment_id'];
       $order['order_id'] = $result['order_id'];
       $order['created_at'] = $result['created_at'];
       $order['status'] = $result['status'];
       $order['subtotal'] = $result['subtotal'];
       $order['grand_total'] = $result['grand_total'];
       
       //Shipping Address
       $order['shipping_address']['firstname'] = $result['shipping_address']['firstname'];
       $order['shipping_address']['lastname'] = $result['shipping_address']['lastname'];
       $order['shipping_address']['company'] = $result['shipping_address']['company'];
       $order['shipping_address']['street'] = $result['shipping_address']['street'];
       $order['shipping_address']['city'] = $result['shipping_address']['city'];
       $order['shipping_address']['region'] = $result['shipping_address']['region'];
       $order['shipping_address']['region_id'] = $result['shipping_address']['region_id'];
       $order['shipping_address']['postcode'] = $result['shipping_address']['postcode'];
       
	    $list = $proxy->call($sessionId, 'country.list');
		$cName = $result['shipping_address']['country_id'];
	 	foreach ($list as $country) {
	            /* @var $country Mage_Directory_Model_Country */
			if($country['country_id'] == $result['shipping_address']['country_id'])
			{
				$cName = $country['name']; // Loading name in default locale
				break;
			}
	     }
       $order['shipping_address']['country'] = $cName;
       $order['shipping_address']['telephone'] = $result['shipping_address']['telephone'];
       
       //Billing Address
       $order['billing_address']['firstname'] = $result['billing_address']['firstname'];
       $order['billing_address']['lastname'] = $result['billing_address']['lastname'];
       $order['billing_address']['company'] = $result['billing_address']['company'];
       $order['billing_address']['street'] = $result['billing_address']['street'];
       $order['billing_address']['city'] = $result['billing_address']['city'];
       $order['billing_address']['region'] = $result['billing_address']['region'];
       $order['billing_address']['region_id'] = $result['billing_address']['region_id'];
       $order['billing_address']['postcode'] = $result['billing_address']['postcode'];
       
	    $list = $proxy->call($sessionId, 'country.list');
		$cName = $result['billing_address']['country_id'];
	 	foreach ($list as $country) {
	            /* @var $country Mage_Directory_Model_Country */
			if($country['country_id'] == $result['billing_address']['country_id'])
			{
				$cName = $country['name']; // Loading name in default locale
				break;
			}
	     }
       $order['billing_address']['country'] = $cName;
       $order['billing_address']['telephone'] = $result['billing_address']['telephone'];
       
       $order['shipping_description'] = $result['shipping_description'];
       $order['shipping_incl_tax'] = $result['shipping_incl_tax'];
       $order['discount_description'] = $result['discount_description'];
       $order['discount_amount'] = $result['discount_amount'];
       
       $order['payment_method'] = $result['payment']['method'];
       
       //Order items
       $order_items = array();
		$j=0;
		foreach($result['items'] as $item){
			$prod_info[$j] = array('catalog_product.info', $item['product_id']);//$proxy->call($sessionId, 'catalog_product.info', $item['product_id'],'','array("product_id","image","description")');
			$images[$j] = array('catalog_product_attribute_media.list', $item['product_id']);//$proxy->call($sessionId, 'catalog_product_attribute_media.list', $item['product_id']);
			$j++;
		}
		$ProductInfo = $proxy->multiCall($sessionId, $prod_info);
		$ProdImages = $proxy->multiCall($sessionId, $images);
 
	 $i = 0;
	 foreach($result['items'] as $item){
	 	$order_items[$i]['product_id']= $item['product_id'];
	 	$order_items[$i]['sku'] = $item['sku'];
		$order_items[$i]['description'] = $ProductInfo[$i]['description'];
		$order_items[$i]['additionalDescription'] = $ProductInfo[$i]['short_description'];
		$order_items[$i]['url_path'] = $baseUrl . $ProductInfo[$i]['url_path'];
	 	$order_items[$i]['name'] = $item['name'];
	 	$order_items[$i]['qty'] = $item['qty_ordered'];
	 	$order_items[$i]['price_incl_tax'] = $item['price_incl_tax'];
	 	$order_items[$i]['row_total_incl_tax'] = $item['row_total_incl_tax'];
		$order_items[$i]['image']= $ProdImages[$i][0]['url'];
	 	$i++;
	 }
	 $order['items'] = $order_items;
         $result = json_encode(array('Success'=>$order));
    }

   catch (Exception $e)
    {
        $result = json_encode(array('Error'=>'No orders found'));
    }
    
header('Content-type: application/json');
echo $result;
?>  