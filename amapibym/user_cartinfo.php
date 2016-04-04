<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
    $filters['customer_id']= $_REQUEST['customer_id'];
}

try {

    $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters));
    $result = $proxy->call($sessionId, "cart.info", array($quote_id[0]));
//echo $result = json_encode(array('Success'=>$result));exit;
    $cart['quote_id'] = $result['quote_id'];
    $cart['items_count'] = $result['items_count'];
    $cart['items_qty1'] = $result['items_qty'];
    $cart['grand_total1'] = $result['grand_total'];
    $cart['subtotal1'] = $result['subtotal'];
    $cart['discount_amount'] = $result['shipping_address']['discount_amount'];
    $cart['discount_description'] = $result['shipping_address']['discount_description'];
    $cart['shipping_incl_tax1'] = $result['shipping_address']['shipping_incl_tax'];
    $cart['coupon_code'] = $result['coupon_code'];
    $cart['items'] = array();
    $cart_items = array();
    $j=0;
    foreach($result['items'] as $item){
        $prod_info[$j] = array('catalog_product.info', $item['product_id']);
        $images[$j] = array('catalog_product_attribute_media.list', $item['product_id']);
        $j++;
    }

    $i = 0;
    $ProductInfo = $proxy->multiCall($sessionId, $prod_info);
    $ProdImages = $proxy->multiCall($sessionId, $images);
    $total=0;
    foreach($result['items'] as $item){
        $cart_items[$i]['product_id'] = $item['product_id'];
        $cart_items[$i]['description'] = $ProductInfo[$i]['description'];
        $cart_items[$i]['additionalDescription'] = $ProductInfo[$i]['short_description'];
        $cart_items[$i]['url_path'] = $baseUrl . $ProductInfo[$i]['url_path'];
        $cart_items[$i]['sku'] = $item['sku'];
        $cart_items[$i]['name'] = $item['name'];
        $cart_items[$i]['qty'] = $item['qty'];
        $cart_items[$i]['price_incl_tax'] = $item['price_incl_tax'];
        $cart_items[$i]['price'] = $item['price'];
        $cart_items[$i]['statetax'] = $item['statetax'];
        $cart_items[$i]['countytax'] = $item['countrytax'];
        $cart_items[$i]['citytax'] = $item['citytax'];
        $cart_items[$i]['row_total_incl_tax'] = $item['row_total_incl_tax'];
        $cart_items[$i]['image']= $ProdImages[$i][0]['url'];

        $price = $item['price'] * $item['qty'];
        $total_qty_substract = $item['price_incl_tax'] * $item['qty'];

        $total_qty += $item['qty'];
        $total_subtotal += $item['price'] * $item['qty'];
        $total += $item['price_incl_tax'] * $item['qty'];

        $i++;
    }
    $cart['items'] = $cart_items;

    $tax = $cart['grand_total'] - $cart['subtotal'];

    $cart['subtotal'] = $total_subtotal;
    $cart['grand_total'] = $total;
    $cart['shipping_incl_tax'] = $tax;
    $cart['items_qty'] = $total_qty;
    $result = json_encode(array('Success'=>$cart));
}
catch (Exception $e) {
    $error =  $e->getMessage();
    $result = json_encode(array('Error'=>'Cart details not found'));
}
header('Content-type: application/json');
echo $result;
?>