<?php
require __DIR__ . '/vendor/autoload.php';
use \Dropbox as dbx;
// We can now use $accessToken to make API requests.
$dbxClient = new dbx\Client($_ENV['DROPBOX_TOKEN'], "WTTF");
$accountInfo = $dbxClient->getAccountInfo();
echo '<pre>';
print_r($accountInfo);

$f = fopen("working-draft.txt", "rb");
$result = $dbxClient->uploadFile("/working-draft.txt", dbx\WriteMode::add(), $f);
fclose($f);
echo 'Upload file...<br/>';
print_r($result);

$folderMetadata = $dbxClient->getMetadataWithChildren("/");
print_r($folderMetadata);

$f = fopen("working-draft.txt", "w+b");
$fileMetadata = $dbxClient->getFile("/working-draft.txt", $f);
fclose($f);
echo 'Download file...<br/>';
print_r($fileMetadata);
	


?>