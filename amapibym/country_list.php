<?php
ob_start();
include('inc/config.php');

try {
   $result = $proxy->call($sessionId, 'country.list');
   $result = json_encode(array('Success'=>$result));
}
catch (Exception $e) {
    $result = json_encode(array('Error'=>'Invalid userId'));
}
header('Content-type: application/json');

echo $result;
exit;
?>
