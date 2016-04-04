<?php
ob_start();
include('inc/config.php');

$city = $_REQUEST['city'];
if($city !== ''):
    $filters = array(
        'city' => array('like'=>$city)
    );
endif;

if($city == ''){
    $result = $proxy->call( $sessionId, 'resource_ginger.getcityitems');
}
else if($city == 'North Jersey' || $city == 'north jersey' || $city == 'NORTH JERSEY')
{
    $result = $proxy->call($sessionId, 'resource_ginger.getcitynorthjerse');
}
else if($city == 'New York' || $city == 'new york' || $city == 'NEW YORK')
{
    $result = $proxy->call($sessionId, 'resource_ginger.getcitynewyork');
}
else if($city == 'Central Jersey' || $city == 'central jersey' || $city == 'CENTRAL JERSEY')
{
    $result = $proxy->call( $sessionId, 'resource_ginger.getcityjersey');
}

header('Content-type: application/json');
if(!empty($result))
{
    $result = json_encode(array('Success'=>$result));
} else {
    $result = json_encode(array('Error'=>'Invalid City'));
}
echo $result;
exit;
?>