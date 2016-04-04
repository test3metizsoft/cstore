<?php
ob_start();
  include('inc/config.php');

  $filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }
		
try {
         
if(isset($_REQUEST['customer_address_id']) && !empty($_REQUEST['customer_address_id'])){
$arrAddresses = array(
    array(
        "mode" => "billing",
		"address_id" => $_REQUEST['customer_address_id']
		)
	);
}
else{  
	// Create a quote, get quote identifier
	$arrAddresses = array(
		
		array(
			"mode" => "billing",
			'firstname' => $_REQUEST['firstname'],
			'lastname' => $_REQUEST['lastname'],
			'company' => $_REQUEST['company'],
                        'salestaxid' => $_REQUEST['salestaxid'],
			'street' => array($_REQUEST['street']),
			'city' => $_REQUEST['city'],
			'region' => $_REQUEST['region_id'],
			'country_id' => 'US', 
                        'statecounty' => $_REQUEST['statecounty'],
			'postcode' => $_REQUEST['postcode'],
			'telephone' => $_REQUEST['telephone'],
			'fax' => $_REQUEST['fax'],
                        'outofcityarea' => $_REQUEST['outofcityarea'],
                        'save_in_address_book' => $_REQUEST['save_in_address_book'],
                        'use_for_shipping' => $_REQUEST['use_for_shipping'],
		)
	);
}
$quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
$result = $proxy->call($sessionId, "cart_customer.addresses", array($quote_id[0], $arrAddresses));
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
  
  
