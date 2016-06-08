<?php
$data = file_get_contents("php://input");
$events = json_decode($data, true);
$data = json_encode($event, true);
file_put_contents("events.txt",$data);
?>

