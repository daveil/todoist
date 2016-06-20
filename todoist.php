<?php
$data = file_get_contents("php://input");
$contents = file_get_contents("events.txt");
$contents .= time().'   '. $data;
file_put_contents("events.txt",$contents);
?>

