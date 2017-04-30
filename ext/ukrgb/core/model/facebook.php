<?php
/**
 *
 * UKRGB Core extension facebook controller.
 *
 * @copyright (c) Mark Gawler 2017
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace ukrgb\core\model;

require_once __DIR__ . '/../vendor/autoload.php';

class facebook
{
	protected $fb;
	protected $appId;
	
	public function __construct($appId, $appSecret)
	{
		$this->appId = $appId;
		
		if (!empty($appId) && !empty($appSecret)){
			$this->fb = new \Facebook\Facebook([
					'app_id' => $appId,
					'app_secret' => $appSecret,
					'default_graph_version' => 'v2.8',
					'persistent_data_handler' => new \ukrgb\core\facebook_storage\Storage(),
			]);
		}
	}
	
	public function getLoginUrl($callbackUrl, $permissions)
	{
		$helper = $this->fb->getRedirectLoginHelper();
	
		return $helper->getLoginUrl($callbackUrl, $permissions);
	}
	
	public function getAccessToken()
	{
		$helper = $this->fb->getRedirectLoginHelper();
		$code = 200;
		try {
			$token = $helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$error = 'Graph returned an error: ' . $e->getMessage();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$error = 'Facebook SDK returned an error: ' . $e->getMessage();
		}
		
		if (! isset($token)) {
			if ($helper->getError()) {
				$error = "Error: " . $helper->getError() . "<br>"
						. "Error Code: " . $helper->getErrorCode() . "<br>"
						. "Error Reason: " . $helper->getErrorReason() . "<br>"
						. "Error Description: " . $helper->getErrorDescription() . "<br>";
				$code = 401;
			} else {
				$error = 'APC_UKRGB_BAD_REQUEST';
				$code = 400;
			}
		}
		
		return (object) [
				token => $token,
				error => $error,
				responce_code => $code,
		];
	}
	
	public function getPageToken($accessToken, $pageId)
	{
		// get page token
		$this->fb->setDefaultAccessToken($accessToken);
		$response = $this->fb->sendRequest('GET', $pageId, ['fields' => 'access_token'])->getDecodedBody();
		return $response['access_token'];	
	}
	
	public function getTokenMetaData($pageToken)
	{
		if (is_null($this->fb) || empty($pageToken))
		{
			return array(
					'app_id' => '',
					'app_name' => '',
					'expires_at' => '',
					'valid' => 'False',
					'issued' => '',
					'scope' => '',
					'error' => '',
			);
		}
	
		$accessToken = new \Facebook\Authentication\AccessToken($pageToken);
	
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->fb->getOAuth2Client();
	
		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		
		try {
			// Validation (these will throw FacebookSDKException's when they fail)
			$tokenMetadata->validateAppId($this->appId); // Replace {app-id} with your app id
			// If you know the user ID this access token belongs to, you can validate it here
			//$tokenMetadata->validateUserId('123');
			$tokenMetadata->validateExpiration();
		} catch (\Facebook\Exceptions\FacebookSDKException $e) {
			$error = $e->getMessage();
		}
	
		if ($tokenMetadata->getExpiresAt() == 0){
			$expiresAt = 'Never';
		}else{
			$expAt = $tokenMetadata->getExpiresAt();
			$expiresAt = $expAt->format("Y/m/d H:i:s")  . ' (' .  $expAt->getTimezone()->getName() . ')';
		}
	
		$issuedAt = $tokenMetadata->getIssuedAt();
		if ($issuedAt)
		{
			$issued = $issuedAt->format("Y/m/d H:i:s") . ' (' .  $issuedAt->getTimezone()->getName() . ')';
		} else {
			$issued ="Not Issued";
		}
		return array(
				'app_id' => $tokenMetadata->getAppId(),
				'app_name' => $tokenMetadata->getApplication(),
				'expires_at' => $expiresAt,
				'valid' => ($tokenMetadata->getIsValid()) ? 'True' : 'False',
				'issued' => $issued,
				'scope' => implode(', ', $tokenMetadata->getScopes()),
				'error' => $error,
		);
	}
	
	public function post($postData, $pageToken, $pageId)
	{

		try {
			$response =  $this->fb->post('/' . $pageId . '/feed', $postData, $pageToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			error_log ('Post, FacebookResponseException: '.$e->getMessage());
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			error_log ('Post, FacebookSDKException: '.$e->getMessage());
		}
		return $response;
	}
	
	public function deletePost($graphNode, $pageToken)
	{
		try {
			$response = $this->fb->delete('/' . $graphNode , array(), $pageToken);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			// this can happen if the post has already been removed from the Facebook Page.
			error_log ('Delete Post, FacebookResponseException: '.$e->getMessage());
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			error_log ('Delete Post, FacebookSDKException: '.$e->getMessage());
		}
		return $response;
	} 
	
	public function update($postData, $pageToken, $nodeId)
	{
		try {
			$response =  $this->fb->post('/' . $nodeId, $postData, $pageToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			error_log ('Post, FacebookResponseException: '.$e->getMessage());
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			error_log ('Post, FacebookSDKException: '.$e->getMessage());
		}
		return $response;
	}

}