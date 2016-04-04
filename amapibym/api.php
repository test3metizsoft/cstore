<?php
include('inc/config.php');
$filters = array('customer_id' => trim($_REQUEST['customer_id']));

try
{        
	//$result = $proxy->call($sessionId,'resource_ginger.getWishList',array($filters));
	$result = $proxy->call($sessionId, 'resource_ginger.getcartid');
	echo'<pre>'; print_r($result);echo'</pre>';exit;
}
catch (Exception $e)
{
    echo $e->getMessage();
}
?>