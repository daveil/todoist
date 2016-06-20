<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
//TODOIST 

	//Make Curl POST to check token
	$curl = new Curl();
	$data = array(
		'token'=>$_ENV['TODOIST_TOKEN'],
		'sync_token'=>'*',
		'resource_types'=>['items']
		);
	$curl->post('https://todoist.com/API/v7/sync',$data);
	$resp =  json_encode($curl->response,true);
	$data = json_decode($resp,true);
	if(isset($data['error_tag'])){
		$url='https://todoist.com/oauth/authorize?';
		$url.='client_id='.$_ENV['TODOIST_CLIENT_ID'].'&';
		$url.='scope=data:read&';
		$url.='state=ALPHA';
		echo '<a href="'.$url.'">TODOIST AUTHORIZE</a>';
	}else{
		echo 'TODOIST AUTHORIZE';
		
	}
	$url = 'https://sandbox.evernote.com/oauth?';
	$url .='oauth_callback=https://'. $_SERVER['HTTP_HOST'].'/authorize.php?my=evernote&';
	$url .='oauth_consumer_key='.$_ENV['EVERNOTE_KEY'];
	
	echo '<a href="'.$url.'">EVERNOTE AUTHORIZE</a>'
?>
