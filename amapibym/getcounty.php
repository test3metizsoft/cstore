<?php
ob_start();
include('inc/config.php');
$filters = array('state_id' => array());
      if(isset($_REQUEST['state_id']) && $_REQUEST['state_id'] != '')
       {          
         $filters['state_id']= $_REQUEST['state_id'];
       }
try           
{ 
  $result = $proxy->call($sessionId, 'resource_ginger.getcounty', array($filters)); 
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