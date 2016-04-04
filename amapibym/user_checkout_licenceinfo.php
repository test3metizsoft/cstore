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

// check if license is existed
$licenseForOrderCreation = null;
if (count($shoppingCartLicenses)) {
    $licenseForOrderCreation = array();
    foreach ($shoppingCartLicenses as $license) {
        $licenseForOrderCreation[] = $license['agreement_id'];
    }
}

// create order
$result = $proxy->call($sessionId,"cart.order",array($quote_id[0], null, $licenseForOrderCreation)); 
print_r($result); exit;
}
   catch (Exception $e)
    {
    echo $e->getMessage();
    }
header('Content-type: application/json');
//echo'<pre>'; print_r($result); echo'</pre>'; exit;
if(!empty($result))
{
    
	//$result['Success']=$result;        
	$result = json_encode($result);
	echo $result;
}
else
{
	$result['Error']='Invalid UserId';
	echo json_encode($result);
}
exit;
?>