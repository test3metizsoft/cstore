<?php
ob_start();
include('inc/config.php');
//$shoppingCartId = $_SESSION['shoppingcartId'];

try {
      
// Create a quote, get quote identifier
$shoppingCartId = $proxy->call($sessionId, 'cart.create');
echo '<pre>';print_r($shoppingCartId);echo '</pre>'; exit;
// Set customer, for example guest
$customerAsGuest = array(
    'firstname' => 'bhavesh',
    'lastname' => 'patel',
    'email' => 'bhavesh.patel@metizsoft.com',
    'website_id' => '1',
    'store_id' => '2',
    'mode' => "guest"
);
$resultCustomerSet = $proxy->call($sessionId, 'cart_customer.set', array( $shoppingCartId, $customerAsGuest) );
// echo '<pre>';print_r($resultCustomerSet);echo '</pre>'; exit;

// Set customer addresses, for example guest's addresses
$arrAddresses = array(
    array(
        "mode" => "shipping",
        "firstname" => "bhavesh",
        "lastname" => "patel",
        "company" => "metiz",
        "street" => "stadium road",
        "city" => "new york",        
        "region" => "testRegion",
        "postcode" => "12132",
        "country_id" => "IN",
        "telephone" => "0123456789",
        "fax" => "0123456789",
        "is_default_shipping" => 0,
        "is_default_billing" => 0
    ),
    array(
        "mode" => "billing",
        "firstname" => "bhavesh",
        "lastname" => "patel",
        "company" => "metiz",
        "street" => "stadium road",
        "city" => "new york",
        "region" => "testRegion",
        "postcode" => "12132",
        "country_id" => "IN",
        "telephone" => "0123456789",
        "fax" => "0123456789",
        "is_default_shipping" => 0,
        "is_default_billing" => 0
    )
);
$resultCustomerAddresses = $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));

   // echo '<pre>';print_r($resultCustomerAddresses);echo '</pre>'; exit;

// add products into shopping cart
$arrProducts = array(array(
        "product_id" => "3014",
        "qty" => 2
    ),
    array(
        "sku" => "NYLON-POHA-2LB",
        "quantity" => 4
    )
);
$resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));


/*  Complete */
// echo '<pre>';print_r($resultCartProductAdd);echo '</pre>'; exit; 
$shoppingCartProducts = $proxy->call($sessionId, "cart_product.list", array($shoppingCartId));
  //   echo '<pre>';print_r($shoppingCartProducts);echo '</pre>'; exit; 

$resultShippingMethods = $proxy->call($sessionId, "cart_shipping.list", array($shoppingCartId));
   //  echo '<pre>';print_r($resultShippingMethods);echo '</pre>'; exit; 

// $randShippingMethodIndex = rand(1, count($resultShippingMethods) );
// $shippingMethod = $resultShippingMethods[$randShippingMethodIndex]["billing"];

$resultShippingMethod = $proxy->call($sessionId, "cart_shipping.method", array($shoppingCartId, $shippingMethod));
    // echo '<pre>';print_r($resultShippingMethods);echo '</pre>'; exit; 

// get list of payment methods
$resultPaymentMethods = $proxy->call($sessionId, "cart_payment.list", array($shoppingCartId));
  //  echo '<pre>';print_r($resultPaymentMethods);echo '</pre>'; exit; 

$paymentMethod = array("method" => "ccsave" );
$resultPaymentMethod = $proxy->call($sessionId, "cart_payment.method", array($shoppingCartId, $paymentMethod));
// echo '<pre>';print_r($resultPaymentMethod);echo '</pre>'; exit; 
 
//   Add a coupan code
$couponCode = "SALE50";
  $resultCartCouponRemove = $proxy->call($sessionId,'cart_coupon.add',array($shoppingCartId,$couponCode));
 // echo '<pre>';print_r($resultCartCouponRemove);echo '</pre>'; exit; 
 
 // get total prices
 $shoppingCartTotals = $proxy->call($sessionId, "cart.totals", array($shoppingCartId));
// echo '<pre>';print_r($shoppingCartTotals);echo '</pre>'; exit; 
 
 // get full information about shopping cart
$shoppingCartInfo = $proxy->call($sessionId, "cart.info", array($shoppingCartId));
print_r( $shoppingCartInfo ); exit;
   //  echo '<pre>';print_r($shoppingCartInfo);echo '</pre>'; exit; 
    
    // create order
$result = $proxy->call($sessionId,"cart.order",array($shoppingCartId));
     echo '<pre>';print_r($result);echo '</pre>'; exit; 
}
catch (Exception $e) {
    echo $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{
    echo '{"Success":';	
//$result['Success']=$result;
	$result = json_encode($result);
	echo $result;
        echo '}';
}
else
{
	$result['Error']='Invalid userId';
	echo json_encode($result);
}
exit;
?>
  
  
