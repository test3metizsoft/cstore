<?php
ob_start();
include('inc/config.php');
$filters = array('customer_id' => array());
$customer_id = $_REQUEST['customer_id'];

$data = json_decode($_REQUEST['data']);
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
    $filters['customer_id']= $_REQUEST['customer_id'];
}
$arrProducts = '';
    for($i=0;$i<count($data); $i++){
        $arrProducts[$i] = array(
                            "product_id" => $data[$i]->id,
                            "qty" => $data[$i]->qty
                        );
    }
$quote_id = $proxy->call($sessionId, 'resource_ginger.getQuoteid', array($filters));
$cartId = $quote_id[0];
try {
        $result = $proxy->call($sessionId, 'cart_product.add', array($cartId, $arrProducts));
        $result = json_encode(array('Success'=>$result));
    }
    catch (Exception $e)
    { 
        $result['Error']=$e->getMessage();
        $result = json_encode($result);
    }
header('Content-type: application/json');    
echo $result;
exit;
?>