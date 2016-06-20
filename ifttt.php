<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;

if(isset($_POST['title'])&&isset($_POST['content'])){
	//IFTTT
	$event = 'update_todoist';
	$token = $_ENV['IFTTT_TOKEN'];
	$curl = new Curl();
	$curl->setHeader('Content-Type', 'application/json');
	$data = array(
					'value1'=>$_POST['title'],
					'value2'=>$_POST['content'],
				);
	$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
}

?>