<?php
ob_start();
include('inc/config.php');
   
$filters = array('user_detail' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
     $in = array('entity_id' => array('eq'=>trim($_REQUEST['customer_id'])));    
     $filters['user_detail'] = $filters['user_detail'] + $in;
   }
   try 
  {
       //$result = $proxy->call($sessionId,'customer.info',array($filters));
       $result = $proxy->call($sessionId,'resource_ginger.getUserinfo',array($filters));
              //echo '<pre>';print_r($result);echo '</pre>'; exit;
  } 
    
    catch (Exception $e) 
	{
            echo $e->getMessage();
        }       
  header('Content-type: application/json');    
   if(!empty($result))
{
    echo '{"Success":';	
//$result['Success']=$result;
	$result = json_encode($result);
	echo $result;
        echo '}';
}
else
{
	$result['Error']='Customer not found';
	echo json_encode($result);
}
exit;
?>