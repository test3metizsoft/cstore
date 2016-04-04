<?php
include('inc/config.php');

$filters = array('admin_user' => array(), 'display_param' => array(), 'sort' => array());
if(isset($_REQUEST['email']) && $_REQUEST['email'] != '')
{
    $in = array('email' => array('like'=>trim($_REQUEST['email']).'%')); 
   $filters['admin_user'] = $filters['admin_user'] + $in;

}
if(isset($_REQUEST['password']) && $_REQUEST['password'] != '')
{
    $in = array('password_hash' => array('like'=>trim($_REQUEST['password']).'%')); 
   $filters['admin_user'] = $filters['admin_user'] + $in;

}

$products = $proxy->call($sessionId, 'customer.list',$filters);
echo '<pre>';print_r($products);echo '</pre>'; exit;
?>