<?php	
$url = 'https://todoist.com/oauth/access_token?';
$url = 'client_id='.$_ENV['TODOIST_CLIENT_ID'].'&';
$url = 'client_secret='.$_ENV['TODOIST_CLIENT_SECRET'].'&';
$url = 'code='.$_GET['code'].'&';
$url = 'redirect_uri=https://'. $_SERVER['HTTP_HOST'].'/index.php';
header("Location: $url");
?>