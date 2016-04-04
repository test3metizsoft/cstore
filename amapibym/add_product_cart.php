<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
    $filters['customer_id']= $_REQUEST['customer_id'];
}
$arrProducts = array(
    array(
        "product_id" => $_REQUEST['product_id'],
        "qty" => $_REQUEST['qty']
    )
);
$quote_id = $proxy->call($sessionId, 'resource_ginger.getQuoteid', array($filters));
$cartId = $quote_id[0];
//print_r($quote_id);exit;
/*if(!isset($quote_id[0])){
    $cartId = $proxy->call($sessionId, 'cart.create', array(1));
    
    $filters1 = array('entity_id' => $_REQUEST['customer_id'], 'mode' => 'customer' );
    $proxy->call($sessionId, 'cart_customer.set', array( $cartId, $filters1) );
    
}*/
try {
        $result = $proxy->call($sessionId, 'cart_product.add', array($cartId, $arrProducts));
        
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
        $resultCustomerAddresses = $proxy->call($sessionId, "cart_customer.addresses", array($cartId, $arrAddresses));
        
        $result = json_encode(array('Success'=>$result));
    }
    catch (Exception $e)
    { 
        $result['Error']=$e->getMessage();
        $result = json_encode($result);
    }
header('Content-type: application/json');    
echo $result;
exit;
?>