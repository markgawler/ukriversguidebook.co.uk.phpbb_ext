<?php
/**
 *
 * UKRGB Core extension admin controller.
 *
 * @copyright (c) Mark Gawler 2017
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace ukrgb\core\controller;

require_once __DIR__ . '/../vendor/autoload.php';

class admin
{
	
	/**
	 * phpBB config
	 *
	 * @var \phpbb\config\config
	 */
	protected $config;
	
	
	/**
	 * phpBB request object
	 *
	 * @var \phpbb\request\request_interface
	 */
	protected $request;
	
	/**
	 * phpBB user
	 *
	 * @var \phpbb\user
	 */
	protected $user;
	
	protected $fb;
	protected $fb_helper;
	protected $app_id;
	
	public function __construct(
			\phpbb\config\config $config,
			\phpbb\request\request_interface $request,
			\phpbb\user $user,	
			\phpbb\controller\helper $helper)
	{
		$this->config = $config;
		$this->request = $request;
		$this->user = $user;
		$this->helper =$helper;
		
		$this->app_id = $this->config['ukrgb_fb_appid'];
		$app_secret = $this->config['ukrgb_fb_secret'];
		
		
		if (!empty($this->app_id) && !empty($app_secret)){
			$this->fb = new \Facebook\Facebook([
					'app_id' => $this->app_id,
					'app_secret' => $app_secret,
					'default_graph_version' => 'v2.8',
					'persistent_data_handler' => new \ukrgb\core\facebook_storage\Storage(),
			]);
			$this->fb_helper = $this->fb->getRedirectLoginHelper();
		}
	}
	

	/**
	 * Controller for route /oauth/{action}
	 *
	 * @param string $action
	 * @return Response A Symfony Response object
	 */
	public function handle_fb($mode)
	{
		switch ($mode) {
			case 'callback':
				//return $this->helper->message('Admin handler called, mode:' . $mode);
				return $this->callback();
//			case 'request_permisions':
//				return $this->helper->message('Admin handler called, mode:' . $mode);
			
			default:
				throw new \exception('UKRGB Admin Unexpected Mode:' . $mode);
				break;
		}
	}

	
	
	public function get_request_permisions_url()
	{
		$permissions = ['manage_pages', 'publish_pages']; // Optional permissions
		$callback_url = generate_board_url(true) . $this->helper->route('ukrgb_admin_fb', array(mode => 'callback'));
		$loginUrl = $this->fb_helper->getLoginUrl($callback_url, $permissions);

		return htmlspecialchars($loginUrl);
	}
	
	
	public function callback()
	{
		$page_id = $this->config['ukrgb_fb_page_id'];
		
		$this->request->enable_super_globals();
		
		try {
			$accessToken = $this->fb_helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		$this->request->disable_super_globals();
		
		if (! isset($accessToken)) {
			if ($helper->getError()) {
				header('HTTP/1.0 401 Unauthorized');
				echo "Error: " . $this->fb_helper->getError() . "\n";
				echo "Error Code: " . $this->fb_helper->getErrorCode() . "\n";
				echo "Error Reason: " . $this->fb_helper->getErrorReason() . "\n";
				echo "Error Description: " . $this->fb_helper->getErrorDescription() . "\n";
			} else {
				header('HTTP/1.0 400 Bad Request');
				echo 'Bad request';
			}
			exit;
		}
		// Logged in
	
		
		// get page token
		$this->fb->setDefaultAccessToken($accessToken);
		$response = $this->fb->sendRequest('GET', $page_id, ['fields' => 'access_token'])->getDecodedBody();
		$this->config->set('ukrgb_fb_page_token', $response['access_token']);
		
		return $this->helper->message('ACP_UKRGB_FB_PAGE_TOKEN_UPD');
	
	}
	
	public function getTokenMetaData()
	{
		//var_dump($this->config['ukrgb_fb_page_token']);
		$accessToken = new \Facebook\Authentication\AccessToken($this->config['ukrgb_fb_page_token']);
		
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->fb->getOAuth2Client();
		
		if ($accessToken->isLongLived()) {
			echo '<p> This is a long lived access token</p>';
		}
		
		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		//echo '<h3>Metadata</h3>';
		//var_dump($tokenMetadata);
		//echo ('<br>');
		//var_dump($accessToken);
		
		// Validation (these will throw FacebookSDKException's when they fail)
		//$tokenMetadata->validateAppId($this->app_id); // Replace {app-id} with your app id
		// If you know the user ID this access token belongs to, you can validate it here
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();
		//die();
		
		if ($tokenMetadata->getExpiresAt() == 0){
			$expiresAt = 'Never';
		}else{
			$expiresAt = $tokenMetadata->getExpiresAt();
		}
	
		$issuedAt = $tokenMetadata->getIssuedAt();
	
		return array(
				'app_id' => $tokenMetadata->getAppId(),
				'app_name' => $tokenMetadata->getApplication(),
				'expires_at' => $expiresAt,
				'valid' => ($tokenMetadata->getIsValid()) ? 'True' : 'False',
				'issued' => $issuedAt->format("Y/m/d H:i:s") . ' (' .  $issuedAt->getTimezone()->getName() . ')',
				'scope' => implode(', ', $tokenMetadata->getScopes()),			
		);
	}
	
	public function post()
	{
		$link_data  = [
				'message' => 'A little test message.',
				'link' => 'http://ukrgb.co.uk',
		];
		
		try {
			$response = $this->fb->post('/me/feed', $link_data, $pageAccessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: '.$e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: '.$e->getMessage();
			exit;
		}
		$graphNode = $response->getGraphNode();
		var_dump($graphNode);
	}
	
	
	

}