<?php
ob_start();
include('inc/config.php');
$filters = array('county_id' => array());
      if(isset($_REQUEST['county_id']) && $_REQUEST['county_id'] != '')
       {          
         $filters['county_id']= $_REQUEST['county_id'];
       }
try           
{ 
  $result = $proxy->call($sessionId, 'resource_ginger.getcity', array($filters)); 
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