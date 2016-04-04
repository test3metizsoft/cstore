<?php
ob_start();
include('inc/config.php');

$result = '';
$id = $_REQUEST['id'];
try {
    $result = $proxy->call($sessionId, 'catalog_product.delete', $id);
    $result = array('Success'=>$result);
}
catch(Exception $e) {
    $result = array('Error'=>$e->getMessage());
}
echo json_encode($result);