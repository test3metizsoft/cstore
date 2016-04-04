<?php
include('inc/config.php');

try {        
    $result = $proxy->call($sessionId,'resource_ginger.getList');
    $result = json_encode(array('Success'=>$result));
} 			   
catch (Exception $e) {
    $result = json_encode(array('Error'=>$e->getMessage()));
}
header('Content-type: application/json');
echo $result;
exit;

    
   