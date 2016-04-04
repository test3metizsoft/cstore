<?php
ob_start();
  include('inc/config.php');
file_put_contents("test-address_add-cart.txt",$_REQUEST);
  $filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }
		
try {
     
if(isset($_REQUEST['customer_address_id']) && !empty($_REQUEST['customer_address_id'])){
$arrAddresses = array(
    array(
        "mode" => "shipping",
		"address_id" => $_REQUEST['customer_address_id']
		)
	);
}
else{
// Create a quote, get quote identifier
$arrAddresses = array(
    array(
        "mode" => "shipping",
        'firstname' => $_REQUEST['firstname'],
        'lastname' => $_REQUEST['lastname'],
        'company' => $_REQUEST['company'],
        'street' => array($_REQUEST['street']),
        'city' => $_REQUEST['city'],
        'region' => $_REQUEST['region_id'],
        'country_id' => 'US', 
        'statecounty' => $_REQUEST['statecounty'],
        'postcode' => $_REQUEST['postcode'],
        'telephone' => $_REQUEST['telephone'],
        'fax' => $_REQUEST['fax'],
        'outofcityarea' => $_REQUEST['outofcityarea'],
        'same_as_billing' => $_REQUEST['same_as_billing'],
        'save_in_address_book' => $_REQUEST['save_in_address_book'],
    )
    
);
}
$quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
$result = $proxy->call($sessionId, "cart_customer.addresses", array($quote_id[0], $arrAddresses));
       // echo '<pre>';print_r($result);echo '</pre>'; exit;

}
catch (Exception $e) {
    $error = $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{
    //echo '{"Success":';	
	$results['Success']=$result;
	$result = json_encode($results);
	echo $result;
      //  echo '}';
}
else
{
	$result['Error']=$error;
	echo json_encode($result);
}
exit;
?>
  
  
