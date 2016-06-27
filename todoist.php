<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
use \Dropbox as dbx;

date_default_timezone_set('Asia/Manila');
//Read input
$input = file_get_contents("php://input");
// Initialize Dropbox client
$dbxClient = new dbx\Client($_ENV['DROPBOX_TOKEN'], "WTTF");
//Initialize files
if(!file_exists('events.txt'))
	file_put_contents('events.txt',"");
if(!file_exists('summary.txt'))
	file_put_contents('summary.txt',"");

// Load events txt  from Dropbox
$f = fopen("events.txt", "w+b");
$hasEvents = $dbxClient->getFile("/events.txt", $f);
fclose($f);

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
	// Load summary txt from Dropbox
	$f = fopen("summary.txt", "w+b");
	$hasSummary = $dbxClient->getFile("/summary.txt", $f);
	fclose($f);
	
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
	$time =  date('h:i A',$data['epoch']);
	$summary_id =  $date.'-'.$item['project']['id'];
	
	$filename = $summary_id.'.txt';
	$item_content = $item['item']['content'];
	
	$content =  " $time — $item_content ";
	$full_date =  date('M d, Y',$data['epoch']);
	$project = $item['project']['name'];
	$title = $project.' Daily Summary — '.$full_date;
	
	if(!file_exists($filename))
		file_put_contents($filename,"");
	
	$f = fopen($filename, "w+b");
	$hasFile = $dbxClient->getFile("/logs/".$filename, $f);
	fclose($f);
	if(true):
	
	$file_content = json_decode(file_get_contents($filename),true);
	
	$f = fopen($filename, "rb");
	$dbxClient->uploadFile("/logs/".$filename, $hasFile?dbx\WriteMode::force():dbx\WriteMode::add(), $f);
	fclose($f);
	
	if(!$file_content){
		$file_content = array(
					'project'=>$project,
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
	$f = fopen("summary.txt", "rb");
	$result = $dbxClient->uploadFile("/summary.txt", $hasSummary?dbx\WriteMode::force():dbx\WriteMode::add(), $f);
	fclose($f);
	//Add file info
	$data['file'] =  array('title'=>$filename,'content'=>$content);
	endif;
}

array_push($contents,$data);
//Write new contents
file_put_contents("events.txt",json_encode($contents));
$f = fopen("events.txt", "rb");
$result = $dbxClient->uploadFile("/events.txt", $hasEvents?dbx\WriteMode::force():dbx\WriteMode::add(), $f);
fclose($f);

?>

