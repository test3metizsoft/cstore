<?php
ob_start();
include('inc/config.php');

$password = $_REQUEST['password'];

$email = $_REQUEST['email'];
$data = json_decode($_REQUEST['data']);
    //echo '<pre>';print_r($data);exit;
$filters = array(
    'email' => array('like' => $_REQUEST['email'])
);

$user = $proxy->call($sessionId, 'customer.list', array($filters));

function validateHash($password, $hash) {
    $hashArr = explode(':', $hash);

    switch (count($hashArr)) {
        case 1:
            return md5($password) === $hash;
        case 2:
            return md5($hashArr[1] . $password) === $hashArr[0];
    }
}

header('Content-type: application/json');

if (validateHash($password, $user[0]['password_hash'])) {
    //$result = json_encode(array('Success' => $user));
    $quote_id = $proxy->call($sessionId, 'resource_ginger.getLogin', array($user));
    
    $customer_id = $user[0]['customer_id'];
    
    $result = json_encode(array('Success' => $user));
    $data = json_decode($_REQUEST['data']);
    if(count($data) > 0){
        if (isset($customer_id) && $customer_id != '') {
            $filters['customer_id'] = $customer_id;
        }
        $arrProducts = '';
        for ($i = 0; $i < count($data); $i++) {
            if($data[$i]->id){
                $arrProducts[$i] = array(
                    "product_id" => $data[$i]->id,
                    "qty" => $data[$i]->qty
                );
            }
        }
        $quote_id = $proxy->call($sessionId, 'resource_ginger.getQuoteid', array($filters));
        $cartId = $quote_id[0];

        try {
            $result = $proxy->call($sessionId, 'cart_product.add', array($cartId, $arrProducts));
            $result = json_encode(array('Success' => $user));
        } catch (Exception $e) {
            $result['Error'] = $e->getMessage();
            $result = json_encode($result);
        }
    }
    
}
echo $result;
exit;
?>