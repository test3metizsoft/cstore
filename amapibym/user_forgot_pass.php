<?php
ob_start();
include('inc/config.php');
$email = $_REQUEST['email'];
$customer = $_REQUEST['customer_id'];
 $number = rand(0,1000000);
 //echo $number; exit;
 
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
try 
  {   
    $user = $proxy->call($sessionId, 'customer.list',$filters);
   //echo '<pre>'; print_r($user[0]['customer_id']);  echo '</pre>';  exit;
    $result = $proxy->call($sessionId, 'customer.update', array('customerId' => $user[0]['customer_id'], 
    'customerData' => array('password_hash' => md5($number)),$filters));

     // $result = $proxy->call($sessionId, 'resource_ginger.getupdateCustomer',array($filters));         
   }  
catch (Exception $e) 
   {
     // echo $e->getMessage();
    }
header('Content-type: application/json');
//$result = json_encode($result);
if(!empty($result))
{
	echo '{"Success":';
        echo json_encode($result);
	//print_r($result);  
	echo "}";
}
else
{
    //echo '{"Error": Invalid EmailId';
	 echo '{"Error": Please enter your registered Email ID';
        //$result['Error']='';
	//echo json_encode($result);
        echo "}";
}
/* ******For Email *******   */
$msg = "Your new password is: ".$number;

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail($email,"My subject",$msg);
exit;  
?>
