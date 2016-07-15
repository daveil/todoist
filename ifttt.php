<?php
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;
use \Dropbox as dbx;
date_default_timezone_set('Asia/Manila');
$curl = new Curl();

if(isset($_POST['title'])&&isset($_POST['content'])&&isset($_POST['project'])){
	//IFTTT
	// Copy todoist to evernote
	$event = 'copy_to_evernote';
	$token = $_ENV['IFTTT_TOKEN'];
	$curl->setHeader('Content-Type', 'application/json');
	$data = array(
					'value1'=>$_POST['title'],
					'value2'=>$_POST['content'],
					'value3'=>$_POST['project'],
				);
	$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
	
	
	if($_POST['project']=='ISMS' || $_POST['project']=='ERB' || $_POST['project']=='TSSi' ){
		//Send email to team for daily updates
		$event = 'email_team';
		$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
		//Update consolidated update list for reference
		$event = 'updates_list';
		$items=explode('*',$_POST['content']);
		//Append tasks
		foreach($items as $item){
			if(!$item) continue;
			$item =  str_replace('<br/>','',$item);
			$item =  explode('—',$item);
			$time = $item[0];
			$task = $item[1];
			$data = array(
					'value1'=>date('M d',time()). ' '.$time,
					'value2'=>'',
					'value3'=>$task,
			);
			$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
		}
		//Append task count
		if(count($items)>0){
			$data = array(
						'value1'=>date('M d h:i A',time()),
						'value2'=>$_POST['project'],
						'value3'=>count($items). 'task(s) completed',
				);
			$curl->post('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$token,$data);
		}
	}
}else if(isset($_GET['maker'])){
	// Initialize Dropbox client
	$dbxClient = new dbx\Client($_ENV['DROPBOX_TOKEN'], "WTTF");
	if(!file_exists('summary.txt'))
		file_put_contents('summary.txt',"");
	
	// Load summary txt from Dropbox
	$f = fopen("summary.txt", "w+b");
	$hasSummary = $dbxClient->getFile("/summary.txt", $f);
	fclose($f);
	
	$time = str_replace('at',' ',$_GET['maker']);
	$time = strtotime($time);
	$summary_id = date('y-m-d',$time);
	$summary = json_decode(file_get_contents('summary.txt'),true);
	if(isset($summary[$summary_id])){
		$summaries = $summary[$summary_id];
		$fileAvailable = array();
		//Clone Dropbox files
		foreach($summaries as $file=>$count){
			$filename = $file.'.txt';
			$f = fopen($filename, "w+b");
			$fileMeta = $dbxClient->getFile('/logs/'.$filename, $f);
			$fileAvailable[$file]=(bool)$fileMeta;
			fclose($f);
		}
		//Load contents
		foreach($summaries as $file=>$count){
			if(!$fileAvailable[$file]) continue;
			$filename = $file.'.txt';
			$data = json_decode(file_get_contents($filename),true);
			$contents='';
			foreach($data['content'] as $content){
				$contents .= '* '.$content."<br/>";
			}
			$post_data = array(
				'title'=>$data['title'],
				'content'=>$contents,
				'project'=>$data['project']
				);
			$curl->post('https://'. $_SERVER['HTTP_HOST'].'/ifttt.php',$post_data);
		}
		//Delete files to Dropbox
		foreach($summaries as $file=>$count){
			if(!$fileAvailable[$file]) continue;
			$filename = $file.'.txt';
			$dbxClient->delete('/logs/'.$filename);
		}
		//Clean up contents of events txt to save space
		file_put_contents('events.txt',"");
		$f = fopen("events.txt", "rb");
		$dbxClient->uploadFile("/events.txt", dbx\WriteMode::force(), $f);
		fclose($f);
	}
}
?>