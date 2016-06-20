<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
date_default_timezone_set('Asia/Manila');
//Read input
$input = file_get_contents("php://input");
//Read and parse contents
$contents = json_decode(file_get_contents("events.txt"),true);
$data = json_decode($input,true);
if(!is_array($contents)){
	$contents = array();
}
//Add timestamp
$data['epoch']=time();

//Create daily summary for complete item
if($data['event_name']=='item:completed'){
	
	//Request for item details
	$curl = new Curl();
	$get_data = array(
		'token'=>$_ENV['TODOIST_TOKEN'],
		'item_id'=>$data['event_data']['id']
		);
	$curl->get('https://todoist.com/API/v7/get_item',$get_data);
	$item =  json_decode(json_encode($curl->response),true);
	//Build file
	$date =  date('y-m-d',$data['epoch']);
	$time =  date('H:i',$data['epoch']);
	$summary_id =  $date.'-'.$item['project']['id'];
	
	$filename = $summary_id.'.txt';
	$item_content = $item['item']['content'];
	
	$content =  " $time - $item_content ";
	$full_date =  date('M d, Y',$data['epoch']);
	$title = $item['project']['name'].' Daily Summary — '.$full_date;
	
	
	if(true):
	
	$file_content = json_decode(file_get_contents($filename),true);
	
	if(!$file_content){
		$file_content = array(
					'title'=>$title,
					'date'=>$date,
					'content'=>[],
		);
	}
	array_push($file_content['content'],$content);
	
	file_put_contents($filename, json_encode($file_content));
	
	$summary_file =  json_decode(file_get_contents('summary.txt'),true);
	
	if(!$summary_file){
		$summary_file = array();
	}
	
	if(!$summary_file[$date]){
		$summary_file[$date] = array();
	}
	
	$summary_file[$date][$summary_id] = count($file_content['content']);
	
	file_put_contents('summary.txt', json_encode($summary_file));
	
	//Add file info
	$data['file'] =  array('title'=>$filename,'content'=>$content);
	endif;
}

array_push($contents,$data);
//Write new contents
file_put_contents("events.txt",json_encode($contents));

?>

