<?php
$input = file_get_contents("php://input");
$contents = json_decode(file_get_contents("events.txt"),true);
$data = json_decode($input,true);
if(!is_array($contents)){
	$contents = array();
}
$data['epoch']=time();
array_push($contents,$data);
file_put_contents("events.txt",json_encode($contents));
?>

