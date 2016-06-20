<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
$curl = new Curl();

if(isset($_POST['title'])&&isset($_POST['content'])){
	//IFTTT
	$event = 'update_todoist';
	$token = $_ENV['IFTTT_TOKEN'];
	$curl->setHeader('Content-Type', 'application/json');
	$data = array(
					'value1'=>$_POST['title'],
					'value2'=>$_POST['content'],
				);
	$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
}else if(isset($_GET['maker'])){
	$time = str_replace('at',' ',$_GET['maker']);
	$time = strtotime($time);
	$summary_id = date('y-m-d',$time);
	echo $summary_id;
	$summary = json_decode(file_get_contents('summary.txt'),true);
	if(isset($summary[$summary_id])){
		$summaries = $summary[$summary_id];
		foreach($summaries as $file=>$count){
			$data = json_decode(file_get_contents($file.'.txt'),true);
			$post_data = array(
				'title'=>$data['title'],
				'content'=>implode(" <br/>",$data['content'])
				);
			$curl->post('https://'. $_SERVER['HTTP_HOST'].'/ifttt.php',$post_data);
			print_r($post_data);
		}
	}
}
?>