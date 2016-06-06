<?php
require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;
$curl = new Curl();
$data = array(
	'token'=>$_ENV['TODOIST_TOKEN'],
	'sync_token'=>'*',
	'resource_types'=>['items']
	);
$curl->post('https://todoist.com/API/v7/sync',$data);
$resp =  json_encode($curl->response,true);
echo $resp."\n";
?>