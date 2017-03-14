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

namespace ukrgb\core\controller;

require_once __DIR__ . '/../vendor/autoload.php';

class facebook
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
	
	/**
	 * phpBB root path
	 *
	 * @var string
	 */
	protected $phpbb_root_path;
	
	/**
	 * PHP file extension
	 *
	 * @var string
	 */
	protected $php_ext;
	
	
	protected $fb;
	protected $fb_helper;
	protected $app_id;
	
	public function __construct(
			\phpbb\config\config $config,
			\phpbb\request\request_interface $request,
			\phpbb\user $user,	
			\phpbb\controller\helper $helper,
			$phpbb_root_path,
			$php_ext)
	{
		$this->config = $config;
		$this->request = $request;
		$this->user = $user;
		$this->helper =$helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		
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
	 * Controller for route /facebook/{action}
	 *
	 * @param string $action
	 * @return Response A Symfony Response object
	 */
	public function handle($mode)
	{
		switch ($mode) {
			case 'callback':
				return $this->callback();
			
			default:
				throw new \exception('UKRGB Admin Unexpected Mode:' . $mode);
				break;
		}
	}

	
	
	public function getRequestPermisionsUrl()
	{
		$permissions = ['manage_pages', 'publish_pages']; // Optional permissions
		$callback_url = generate_board_url(true) . $this->helper->route('ukrgb_facebook', array(mode => 'callback'));
		$loginUrl = $this->fb_helper->getLoginUrl($callback_url, $permissions);

		return htmlspecialchars($loginUrl);
	}
	
	
	public function callback()
	{
		if (!function_exists('group_memberships'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}
		
		if (empty(group_memberships(array($this->config['ukrgb_fb_page_mgr']), $this->user->data['user_id'])))
		{
			return $this->helper->message('APC_NOT_PAGE_MGR', array(), 'ACP_UKRGB_UNAUTH' , 401);
		}
		
		$page_id = $this->config['ukrgb_fb_page_id'];
		
		$this->request->enable_super_globals();
		
		try {
			$accessToken = $this->fb_helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			return $this->helper->message('Graph returned an error: ' . $e->getMessage());
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			return $this->helper->message('Facebook SDK returned an error: ' . $e->getMessage());
		}

		$this->request->disable_super_globals();
		
		if (! isset($accessToken)) {
			if ($this->fb_helper->getError()) {
				$msg = "Error: " . $this->fb_helper->getError() . "<br>" 
						. "Error Code: " . $this->fb_helper->getErrorCode() . "<br>"
						. "Error Reason: " . $this->fb_helper->getErrorReason() . "<br>"
						. "Error Description: " . $this->fb_helper->getErrorDescription() . "<br>";
				return $this->helper->message($msg, array(), 'ACP_UKRGB_UNAUTH' , 401);
			} else {
				return $this->helper->message('APC_UKRGB_BAD_REQUEST', array(), 'ACP_UKRGB_INFO', 400);
			}
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
		if (is_null($this->fb) || empty($this->config['ukrgb_fb_page_token']))
		{
			return array(
					'app_id' => '',
					'app_name' => '',
					'expires_at' => '',
					'valid' => 'False',
					'issued' => '',
					'scope' => '',
			);
		}
		
		$accessToken = new \Facebook\Authentication\AccessToken($this->config['ukrgb_fb_page_token']);
		
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->fb->getOAuth2Client();
		
		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		
		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($this->app_id); // Replace {app-id} with your app id
		// If you know the user ID this access token belongs to, you can validate it here
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();
		
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
		);
	}
	
	public function post($data)
	{
		/*
		$data  = [
				'message' => 'A little test message.',
				'link' => 'http://ukrgb.co.uk',
		];
		*/
		//try {
		$response = $this->fb->post('/me/feed', $data, $this->config['ukrgb_fb_page_token']);
		//$response = $this->fb->post('/'.$config['ukrgb_fb_page_id'].'/feed', $data, $this->config['ukrgb_fb_page_token']);
		//} catch(Facebook\Exceptions\FacebookResponseException $e) {
		//	echo 'Graph returned an error: '.$e->getMessage();
		//	exit;
		//} catch(Facebook\Exceptions\FacebookSDKException $e) {
		//	echo 'Facebook SDK returned an error: '.$e->getMessage();
		//	exit;
		//}
		$graphNode = $response->getGraphNode();
		//var_dump($graphNode);
		//die();
	}
	
	
	

}