<?php
ob_start();
include('inc/config.php');

$filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }


try     
{
 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 

// get list of licenses
$result = $proxy->call($sessionId, "cart.licenseAgreement", array($quote_id[0]));
print_r($result);  
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
	$result['Error']='0';
	echo json_encode($result);
}
exit;
?>
  
  
