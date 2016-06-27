<?php
require __DIR__ . '/vendor/autoload.php';
use \Dropbox as dbx;
// We can now use $accessToken to make API requests.
$dbxClient = new dbx\Client($_ENV['DROPBOX_TOKEN'], "WTTF");

//Initialize files
if(!file_exists('events.txt'))
	file_put_contents('events.txt',"");
if(!file_exists('summary.txt'))
	file_put_contents('summary.txt',"");

// Load events txt  from Dropbox
$f = fopen("events.txt", "w+b");
$hasEvents = $dbxClient->getFile("/events.txt", $f);
if(!$hasEvents){
	echo 'No events.txt on dropbox';
}
fclose($f);

/* file_put_contents("events.txt",rand());

$f = fopen("events.txt", "rb");
$result = $dbxClient->uploadFile("/events.txt", dbx\WriteMode::force(), $f);
fclose($f); */

/* 
$accountInfo = $dbxClient->getAccountInfo();
echo '<pre>';
print_r($accountInfo);

file_put_contents("working-draft.txt","sample");
$f = fopen("working-draft.txt", "rb");
$result = $dbxClient->uploadFile("/working-draft.txt", dbx\WriteMode::add(), $f);
fclose($f);
echo 'Upload file...<br/>';
print_r($result);

$folderMetadata = $dbxClient->getMetadataWithChildren("/");
print_r($folderMetadata); 
*/

	


?>