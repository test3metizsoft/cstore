<?php
ob_start();
include('inc/config.php');

try {
    $list = $proxy->call($sessionId, 'country.list');
    $result = "United States";
    foreach ($list as $country) {
        /* @var $country Mage_Directory_Model_Country */
        if($country['country_id'] == $_REQUEST['country_id'])
        {
            $result = $country['name']; // Loading name in default locale
            break;
        }
    }
    $result = json_encode(array('Success'=>$result));
}

catch (Exception $e) {
    //echo $e->getMessage();
    $result['Error']='Country name incorrect';
    $result = json_encode($result);
}
header('Content-type: application/json');
echo $result;
exit;
?>
