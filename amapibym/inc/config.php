<?php
//$proxy = new SoapClient('http://gingerfresh.com/api/soap/?wsdl');
//$sessionId = $proxy->login('gingerfresh', 'Sp1derman');

//$baseUrl = 'http://www.gingerfresh.com/';
//$baseUrl = 'http://gingerfresh.com/';
//$baseUrl = 'http://gingerfresh.com/index.php/';
//$baseUrl = 'http://gingerfresh.com/index.php/';
$baseUrl = 'http://cstoremaster.com/';

$proxy = new SoapClient($baseUrl.'api/soap/?wsdl');

// $proxy = new SoapClient('http://designpoint.in/staging/magento/gingerfresh/api/soap/?wsdl');

$sessionId = $proxy->login('cstoreapi', 'cstoreapi');




?>