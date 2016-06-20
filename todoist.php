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
array_push($contents,$data);
//Write new contents
file_put_contents("events.txt",json_encode($contents));
//Create daily summary for complete item
if($data['event_name']=='item:completed'){
	//Request for item details
	$curl = new Curl();
	$data = array(
		'token'=>$_ENV['TODOIST_TOKEN'],
		'item_id'=>$data['event_data']['id']
		);
	$curl->post('https://todoist.com/API/v7/get_item',$data);
	$item =  json_decode($curl->response,true);
	//Build file
	$date =  date('m d, Y',$data['epoch']);
	$title = $item['project']['name']. 'Daily Summary -'.$date.'txt';
	$content =  "* $item['item']['content'] \n";
	file_put_contents($title, $content, FILE_APPEND | LOCK_EX);
}
?>

