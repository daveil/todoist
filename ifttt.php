<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;

//IFTTT
	$event = 'update_todoist';
	$token = $_ENV['IFTTT_TOKEN'];
	$ifttt = new Curl();
	$data = array(
					'value1'=>'Title',
					'value2'=>'Content',
				);
	$data =  json_encode($data);
	$ifttt->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
	
?>