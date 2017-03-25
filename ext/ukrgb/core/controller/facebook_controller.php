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

class facebook_controller
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
		
		$appId = $config['ukrgb_fb_appid'];
		$appSecret = $config['ukrgb_fb_secret'];
		
		$this->fb = new \ukrgb\core\model\facebook($appId, $appSecret);
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

	/*
	 * Admin Panel
	 */
	public function getRequestPermisionsUrl()
	{
		$permissions = ['manage_pages', 'publish_pages']; // Optional permissions
		$callbackUrl = generate_board_url(true) . $this->helper->route('ukrgb_facebook', array(mode => 'callback'));
		$loginUrl = $this->fb->getLoginUrl($callbackUrl, $permissions);

		return htmlspecialchars($loginUrl);
	}
	

	/*
	 * Admin Panel
	 */
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
		
		$this->request->enable_super_globals();
		$result = $this->fb->getAccessToken();
		$this->request->disable_super_globals();

		if (empty($result->error)) {
			$accessToken = $result->token;
		} else {
			if ($result->code == 401){
				$heading = 'ACP_UKRGB_UNAUTH';
			}else{
				$heading = 'ACP_UKRGB_INFO';
			}
			return $this->helper->message($result->error, array(), $heading, $result->code);
		}
				
		// Logged in
		$this->config->set('ukrgb_fb_page_token', $this->fb->getPageToken($accessToken, $this->config['ukrgb_fb_page_id']));

		return $this->helper->message('ACP_UKRGB_FB_PAGE_TOKEN_UPD');
	}
	
	/*
	 * Admin Panel
	 */
	public function getTokenMetaData()
	{
		return $this->fb->getTokenMetaData($this->config['ukrgb_fb_page_token']); 
	}
}
