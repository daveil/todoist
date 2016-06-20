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
	$item =  json_decode($curl->response,true);
	//Build file
	$date =  date('m d, Y',$data['epoch']);
	$title = $item['project']['name'].' Daily Summary -'.$date.'.txt';
	$item_content = $item['item']['content'];
	$content =  "* $item_content \n";
	file_put_contents($title, $content, FILE_APPEND | LOCK_EX);
	//Add file info
	$data['file'] =  array('title'=>$title,'content'=>$content);
}

array_push($contents,$data);
//Write new contents
file_put_contents("events.txt",json_encode($contents));

?>

