<?php	
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
if(isset($_GET['my'])){
	if($_GET['my']=='evernote'){

			$sandbox = false;
			$china   = false;

			$oauth_handler = new \Evernote\Auth\OauthHandler($sandbox, false, $china);

			$key      = $_ENV['EVERNOTE_KEY'];
			$secret   = $_ENV['EVERNOTE_SECRET'];
			$callback = 'https://wttf.herokuapp.com/evernote.php';

			try {
				$oauth_data  = $oauth_handler->authorize($key, $secret, $callback);

				echo "\nOauth Token : " . $oauth_data['oauth_token'];

				// Now you can use this token to call the api
				$client = new \Evernote\Client($oauth_data['oauth_token']);

			} catch (Evernote\Exception\AuthorizationDeniedException $e) {
				//If the user decline the authorization, an exception is thrown.
				echo "Declined";
			}
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