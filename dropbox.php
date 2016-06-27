<?php
require __DIR__ . '/vendor/autoload.php';
use \Dropbox as dbx;
// We can now use $accessToken to make API requests.
$dbxClient = new dbx\Client($_ENV['DROPBOX_TOKEN'], "WTTF");

$f = fopen("events.txt", "w+b");
$fileMetadata = $dbxClient->getFile("/events.txt", $f);
fclose($f);

file_put_contents("events.txt","");

$f = fopen("events.txt", "rb");
$result = $dbxClient->uploadFile("/events.txt", dbx\WriteMode::force(), $f);
fclose($f);

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