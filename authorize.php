<?php	
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
if(isset($_GET['my'])){
	if($_GET['my']=='evernote'){
		print_r($_GET);
	}
}
if(isset($_GET['code'])&& isset($_GET['state'])){
	if($_GET['state']=='ALPHA'){
		$curl = new Curl();
		$data = array(
			'client_id'=>$_ENV['TODOIST_CLIENT_ID'],
			'client_secret'=>$_ENV['TODOIST_CLIENT_SECRET'],
			'code'=>$_GET['code'],
			'redirect_uri'=>'https://'. $_SERVER['HTTP_HOST'],
			);
		$curl->post('https://todoist.com/oauth/access_token',$data);
		$resp =  json_encode($curl->response,true);
		$data =  json_decode($resp,true);
		print_r($data);
		file_put_contents("tokens.txt",json_encode($data));
	}
}
?>