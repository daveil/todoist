<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;

//IFTTT
	$event = 'update_todoist';
	$token = $_ENV['IFTTT_TOKEN'];
	$curl = new Curl();
	$curl->setHeader('Content-Type', 'application/json');
	$data = array(
					'value1'=>'Title',
					'value2'=>'Content',
				);
	$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
	echo '<pre>';
	print_r($curl);
	print_r($curl->response);exit;
?>