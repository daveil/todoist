<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();
$curl->get('http://www.example.com/');
echo $curl->response;
?>