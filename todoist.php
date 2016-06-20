<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
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
	$filename = $date.'-'.$item['project']['id'].'.txt';
	$item_content = $item['item']['content'];
	
	$content =  " $time - $item_content ";
	$full_date =  date('M d, Y',$data['epoch']);
	$title = $item['project']['name'].' Daily Summary - '.$full_date;
	
	$post_data = array(
			'title'=>$title,
			'content'=>$content,
	);
	$curl->post('https://'. $_SERVER['HTTP_HOST'].'/ifttt.php',$post_data);
	
	if(false):
	
	$file_content = json_decode(file_get_contents($filename),true);
	
	if(!$file_content){
		$file_content = array(
					'title'=>$title,
					'date'=>$date,
					'content'=>'',
		);
	}
	$file_content['content'].=$content;
	file_put_contents($filename, json_encode($file_content));
	
	//Add file info
	$data['file'] =  array('title'=>$filename,'content'=>$content);
	endif;
}

array_push($contents,$data);
//Write new contents
file_put_contents("events.txt",json_encode($contents));

?>

