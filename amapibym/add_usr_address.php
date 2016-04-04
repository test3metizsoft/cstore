<?php
include('inc/config.php');

try {
    
    $result = $proxy->call($sessionId,'customer_address.create',
    array('customerId' => $_REQUEST['customer_id'], 
        'addressdata' => array('firstname' => $_REQUEST['firstname'],
                            'lastname' => $_REQUEST['lastname'],
                            'street' => array($_REQUEST['street']),
                            'city' => $_REQUEST['city'],
                            'region' => $_REQUEST['region'],
                            'country_id' => $_REQUEST['country_id'], 
                            'region_id' => $_REQUEST['region_id'],
                            'postcode' => $_REQUEST['postcode'],
                            'telephone' => $_REQUEST['telephone'],
                            'fax' => $_REQUEST['fax'],
                            'is_default_billing' => $_REQUEST['is_default_billing'],
                            'is_default_shipping' => $_REQUEST['is_default_shipping'],
                            'outofcityarea' => $_REQUEST['outofcityarea'],
                        )
                    )
            );
    $result = json_encode(array('Success'=>$result));
}

catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
header('Content-type: application/json');
echo $result;
exit;
?>
