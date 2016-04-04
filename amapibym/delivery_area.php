<?php

include('inc/config.php');

try {
        $result = $proxy->call($sessionId, 'sales_order_shipment.info','100000001');
        echo '<pre>';print_r($result);echo '</pre>'; exit;;
		exit;
	
	}

catch (Exception $e) {
    echo $e->getMessage();
}
header('Content-type: application/json');
$result = json_encode($result);
if($result != 'null')
{
	echo '{"Success":';
	print_r($result);  
	echo "}";
}
else
{
	echo "Error :0";
	//print_r($result);  
	//echo "}";
}


?>
		