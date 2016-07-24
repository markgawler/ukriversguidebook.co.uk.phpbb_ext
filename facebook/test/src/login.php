<?php

require_once __DIR__ . '/vendor/autoload.php';

if(!session_id()) {
	session_start();
}

$fb = new Facebook\Facebook([
		'app_id' => '1043783392380450',
		'app_secret' => '3f76fdc222c86114dd1866b57046f7c8',
		'default_graph_version' => 'v2.6',
]);

echo "Hello World<br>";


# login.php
//$fb = new Facebook\Facebook([/* . . . */]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'public_profile','manage_pages','publish_pages']; // optional
//$permissions = ['email', 'public_profile', 'publish_actions']; // optional
$loginUrl = $helper->getLoginUrl('http://fb.ukriversguidebook.co.uk/login-callback.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';





echo "<br>Goodbye  World";
