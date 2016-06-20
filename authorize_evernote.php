<?php
require __DIR__ . '/vendor/autoload.php';
//EVERNOTE

//set this to false to use in production
$sandbox = true;

$oauth_handler = new \Evernote\Auth\OauthHandler($sandbox);

$key      = $_ENV['EVERNOTE_KEY'];
$secret   = $_ENV['EVERNOTE_SECRET'];
$callback = 'https://'. $_SERVER['HTTP_HOST'].'/authorize_evernote.php';

$oauth_data  = $oauth_handler->authorize($key, $secret, $callback);

$oauth_token = $oauth_data['oauth_token'];
print_r($oauth_data);
?>