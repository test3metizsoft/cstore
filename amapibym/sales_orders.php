<?php
include('inc/config.php');

$filters = array('sales_flat_order' => array(), 'display_param' => array(), 'sort' => array('DESC'));
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '')
{
   $in = array('customer_id' => array('eq'=>trim($_REQUEST['customer_id'])));
   $filters['sales_flat_order'] = $filters['sales_flat_order'] + $in;

}
   try
    {
        $result = $proxy->call($sessionId,'order.list',$filters);
        $i=0;
        $orderlist = array();
        foreach ($result as $order){
            $orderlist[$i]['increment_id'] = $order['increment_id'];
            $orderlist[$i]['order_id'] = $order['order_id'];
            $orderlist[$i]['created_at'] = $order['created_at'];
            $orderlist[$i]['status'] = $order['status'];
            $orderlist[$i]['subtotal'] = $order['subtotal'];
            $orderlist[$i]['grand_total'] = $order['grand_total'];
            $i++;
	}
       (!empty($orderlist)) ? rsort($orderlist) : array();
        $result = json_encode(array('Success'=>$orderlist));
        
    }
    catch (Exception $e){
        $result = json_encode(array('Error'=>'No orders found'));
    }
header('Content-type: application/json');
echo $result;exit;  