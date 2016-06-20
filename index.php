<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
$input = file_get_contents("php://input");
if($input){
	$data = json_decode($input, true);	
	echo '<pre>';
	print_r($data);
	file_put_contents("tokens.txt",$input);
}else{
	//Make Curl POST to check token
	$curl = new Curl();
	$data = array(
		'token'=>$_ENV['TODOIST_TOKEN'],
		'sync_token'=>'*',
		'resource_types'=>['items']
		);
	$curl->post('https://todoist.com/API/v7/sync',$data);
	$resp =  json_encode($curl->response,true);
	$data = json_decode($resp);
	
	if(isset($data['error_tag'])){
		$url='https://todoist.com/oauth/authorize?';
		$url='client_id='.$_ENV['TODOIST_CLIENT_ID'].'&';
		$url='scope=data:read&';
		$url='state=ALPHA';
		echo '<a href="'.$url.'">AUTHORIZE</a>';
	}
}
?>