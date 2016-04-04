<?php
ob_start();
  include('inc/config.php');

  $filters = array('customer_id' => array());
      if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
       {          
         $filters['customer_id']= $_REQUEST['customer_id'];
       }
		
try{

 $quote_id = $proxy->call($sessionId, 'resource_ginger.getcartid', array($filters)); 
// get list of shipping methods
$result = $proxy->call($sessionId, 'cart_payment.list', array($quote_id[0]));
$i=0;
foreach($result as $method)
{
	if(!empty($method['ccTypes'])){
	
		foreach($method['ccTypes'] as $key => $value)
		{
			// echo "<br>Key:".$method['ccTypes']['code'] = $key;
			// echo "<br>Value:".$method['ccTypes']['name'] = $value;
			$result[$i]['creditcardTypes'][] = array("code" => $key, "name" => $value);
		}
                
	}
		unset($result[$i]['ccTypes']);
	$i++;
}
}
catch (Exception $e) {
   //$error = $e->getMessage();
}
header('Content-type: application/json');

if(!empty($result))
{   
	$results['Success']=$result;
	$result = json_encode($results);
	echo $result;
}
else
{
	$result['Error']= "Payment Methods not retrieved. Please try again!";
	echo json_encode($result);
}
exit;
?>
  
  
