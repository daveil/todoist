<?php
# Include the Dropbox SDK libraries
require __DIR__ . '/vendor/autoload.php';
use \Dropbox as dbx;

function getWebAuth()
{
	$jsonArr = array(
		'key'=>$_ENV['DROPBOX_KEY'],
		'secret'=>$_ENV['DROPBOX_SECRET']
	);
   $appInfo = dbx\AppInfo::loadFromJson($jsonArr);
   $clientIdentifier = "my-app/1.0";
   $redirectUri = "https://wttf.herokuapp.com/dropbox.php?authroized";
   $csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
   return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore, "WTTF");
}

// ----------------------------------------------------------
// In the URL handler for "/dropbox-auth-start"
if(!isset($_GET['authroized'])){
	$authorizeUrl = getWebAuth()->start();
	header("Location: $authorizeUrl");
}
else{
	// ----------------------------------------------------------
	// In the URL handler for "/dropbox.php?finish"

	try {
	   list($accessToken, $userId, $urlState) = getWebAuth()->finish($_GET);
	   assert($urlState === null);  // Since we didn't pass anything in start()
	}
	catch (dbx\WebAuthException_BadRequest $ex) {
	   error_log("/dropbox.php?finish: bad request: " . $ex->getMessage());
	   // Respond with an HTTP 400 and display error page...
	}
	catch (dbx\WebAuthException_BadState $ex) {
	   // Auth session expired.  Restart the auth process.
	   header('Location: /dropbox.php');
	}
	catch (dbx\WebAuthException_Csrf $ex) {
	   error_log("/dropbox.php?finish: CSRF mismatch: " . $ex->getMessage());
	   // Respond with HTTP 403 and display error page...
	}
	catch (dbx\WebAuthException_NotApproved $ex) {
	   error_log("/dropbox.php?finish: not approved: " . $ex->getMessage());
	}
	catch (dbx\WebAuthException_Provider $ex) {
	   error_log("/dropbox.php?finish: error redirect from Dropbox: " . $ex->getMessage());
	}
	catch (dbx\Exception $ex) {
	   error_log("/dropbox.php?finish: error communicating with Dropbox API: " . $ex->getMessage());
	}

	// We can now use $accessToken to make API requests.
	$client = dbx\Client($accessToken, "WTTF");
	$accountInfo = $dbxClient->getAccountInfo();

	print_r($accountInfo);

	$f = fopen("working-draft.txt", "rb");
	$result = $dbxClient->uploadFile("/working-draft.txt", dbx\WriteMode::add(), $f);
	fclose($f);
	print_r($result);

	$folderMetadata = $dbxClient->getMetadataWithChildren("/");
	print_r($folderMetadata);

	$f = fopen("working-draft.txt", "w+b");
	$fileMetadata = $dbxClient->getFile("/working-draft.txt", $f);
	fclose($f);
	print_r($fileMetadata);
	
}

?>