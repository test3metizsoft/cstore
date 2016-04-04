<?php
ob_start();
include('inc/config.php');

$filters = array('customer_param' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['email']) && $_REQUEST['email'] != ''){
    $in = array('email' => array('eq'=>trim($_REQUEST['email'])));
    $filters['customer_param'] = $filters['customer_param'] + $in;
}

if(isset($_REQUEST['password']) && $_REQUEST['password'] != ''){
    $in = array('password' => array('eq'=>trim($_REQUEST['password'])));
    $filters['customer_param'] = $filters['customer_param'] + $in;
}

$filters_check = array(
    'email' => array('like'=>$_REQUEST['email'])
);

$user = $proxy->call($sessionId, 'customer.list', array($filters_check));

if($user):
    header('Content-type: application/json');
    $result_check['Error']='User already Register';
    echo json_encode($result_check);

else:
    try {
        $result = $proxy->call($sessionId,'customer.create',array
        (array('firstname' => $_REQUEST['firstname'],
                'lastname' => $_REQUEST['lastname'],
                'email' => $_REQUEST['email'],
                'password' => $_REQUEST['password'],
                'website_id' => 1,
                'store_id' => 1,
                'group_id' => 1,
            )
        ));
        
        if($result){
            $address = array(
                'firstname'=>$_REQUEST['firstname'],
                'lastname'=>$_REQUEST['lastname'],
                'street'=>$_REQUEST['street'],
                'city' => $_REQUEST['city'],
                'country_id'=>'US',
                'region'=>$_REQUEST['region'],
                'region_id' => $_REQUEST['region_id'],
                'statecounty' => $_REQUEST['statecounty'],
                'postcode' => $_REQUEST['postcode'],
                'telephone' => $_REQUEST['telephone'],
                'fax' => $_REQUEST['fax'],
                'is_default_billing' => 1,
                'is_default_shipping' => 1,
                'outofcityarea' => $_REQUEST['outofcityarea'],
            );
            //echo '<pre>';print_r($address);exit;
            $address = $proxy->call($sessionId,'customer_address.create',
                    array('customerId' => $result, 
                            'addressdata' => $address)
                    );
        }
        $result = json_encode(array('Success'=>$result));
    }
    catch (Exception $e)
    {
        $result['Error']='Please enter valid data';
        $result = json_encode($result);
    }
    header('Content-type: application/json');
    echo $result;
endif;
exit;