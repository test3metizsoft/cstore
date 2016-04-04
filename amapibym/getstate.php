<?php
ob_start();
include('inc/config.php');
try           
{  
  $result = $proxy->call($sessionId, 'region.list', 'US');
  $result = json_encode(array('Success'=>$result));
}
catch (Exception $e)
{ 
    //echo $e->getMessage();
    $result['Error'] = $e->getMessage();
    $result = json_encode($result);
}
header('Content-type: application/json');
echo $result;
exit;
?>