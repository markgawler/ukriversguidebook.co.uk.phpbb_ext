<?php
require_once __DIR__ . '/vendor/autoload.php';

if(!session_id()) {
	session_start();
}

echo "<br>Login Callback";

$fb = new Facebook\Facebook([
		'app_id' => '1043783392380450',
		'app_secret' => '3f76fdc222c86114dd1866b57046f7c8',
		'default_graph_version' => 'v2.6',
    	'persistent_data_handler'=>'session',
]);
$page_id = "1611755389152467";

$helper = $fb->getRedirectLoginHelper();
try {
	$accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
	// When Graph returns an error
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}

if (isset($accessToken)) {
	// Logged in!
	$_SESSION['facebook_access_token'] = (string) $accessToken;

	//var_dump($accessToken);
	
	// Now you can redirect to another page and use the
	// access token from $_SESSION['facebook_access_token']
	echo "<br>Logged in";
	
	
	/*
	 * Post Link
	 */
	$linkData = [
			'link' => 'http://www.ukriverguidebook.co.uk',
			'message' => 'Automated post from SDK',
	];
	
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->post('/'.$page_id.'/feed', $linkData, $accessToken);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	$graphNode = $response->getGraphNode();
	
	echo 'Posted with id: ' . $graphNode['id'];
	
	// End Post Link
	
	
	
	
}
echo "<br>End Login Callback";
