<?php
/**
*
* @package phpBB Extension - UKRGB Core phpBB Extension
* @copyright (c) 2017 Mark Gawler
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\core\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		if (defined('IN_MOBIQUO')){
			return array();
		}else{
			return array(
				'core.user_setup' => 'core_user_setup',
				'core.auth_login_session_create_before' => 'auth_login_session_create_before',
				'core.session_kill_after' => 'session_kill_after',
				'core.page_header' => 'add_page_header_link',
				'core.submit_post_end' => 'new_post_actions',
								
			);
		}
	}
	
	/* @var \phpbb\controller\helper */
	protected $helper;
	
	/* @var \phpbb\template\template */
	protected $template;
	/* @var \phpbb\config\db */
	protected $config;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\request\request */
	protected $request;
	

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\template\template $template
	* @param \phpbb\config\db		  $config		Controller helper object
	* @param \phpbb\user			  $user	Template object
	* @request \phpbb\request 	 	  $request
	*/
	public function __construct(\phpbb\controller\helper $helper, 
			\phpbb\template\template $template, 
			\phpbb\config\db $config, 
			\phpbb\user $user, 
			\phpbb\request\request $request, 
			$root_path,  
			$php_ext)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;		
	}

	
	/**
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function auth_login_session_create_before($event)
	{		        	
		global $JFusionActive;
		//error_log('-- UKRGB Jfusion - login ');
		
		if (isset($event['login']) && isset($event['login']['status']) && $event['login']['status'] == LOGIN_SUCCESS && !$event['admin'] && empty($JFusionActive))
		{		
			$joomla = $this->startJoomla();
				
			//backup phpbb globals
			$joomla->backupGlobal();
			$this->request->enable_super_globals();

			$username = $event['username']; // This is empty when using Oauth login (Facebook)				
			
			// The password in $event['login']['user_row']['user_password'] is hashed, use password from request
			// instead, but this still dosn't work for Oauth logins (Facebook).
			$password = $this->request->untrimmed_variable('password', '', false, \phpbb\request\request_interface::POST);
			if (empty($username) || empty($password))
			{
				if (empty($username))
				{
					//error_log('No username ');
					$username = $event['login']['user_row']['username'];
				}
				if (empty($password))
				{
					error_log('No password ');
				}					
			}
			//detect if the session should be remembered
			if (!empty($event['autologin'])) {
				$remember = 1;
			} else {
				$remember = 0;
			}
			
			$joomla->setActivePlugin($this->config['ukrgb_jfusion_jname']);
	
			$joomla->login($username, $password, $remember);
			//backup phpbb globals
			$joomla->restoreGlobal();
			$this->request->disable_super_globals();
		}
	}
	
	/**
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function session_kill_after($event)
	{		
		//error_log('-- UKRGB Jfusion -  logout');
		
		//check to see if JFusion is not active
		global $JFusionActive;
		if (empty($JFusionActive))
		{
			$joomla = $this->startJoomla();
		
			//backup phpbb globals
			$joomla->backupGlobal();
			$this->request->enable_super_globals();
		
			//define that the phpBB3 JFusion plugin needs to be excluded
			$joomla->setActivePlugin($this->config['ukrgb_jfusion_jname']);
		
			$joomla->logout();
			
			//backup phpbb globals
			$joomla->restoreGlobal();
			$this->request->disable_super_globals();
		}
	}
	
	/**
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function core_user_setup($event)
	{		
		$this->load_language_on_setup($event);
	}
		
	
	/**
	 * @return \JFusionAPIInternal
	 */
	function startJoomla() {
		define('_JFUSIONAPI_INTERNAL', true);
		$apipath = $this->config['ukrgb_jfusion_apipath'];
		require_once $apipath . '/jfusionapi.php';
		return \JFusionAPIInternal::getInstance();
	}
	
	/**
	 * Language setup
	 *
	 * @param unknown $event
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
				'ext_name' => 'ukrgb/core',
				'lang_set' => 'core',
		);
		$event['lang_set_ext'] = $lang_set_ext;
		
		$this->user->add_lang_ext('','ucp');
		
	}
	
	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
				'U_OAUTH_FB' => $this->helper->route('ukrgb_oauth_route', array('name' => 'facebook')),
				'U_OAUTH_REG_SUBMIT' => $this->helper->route('ukrgb_oauth_register'),
				'U_OAUTH_LNK_SUBMIT' => $this->helper->route('ukrgb_oauth_link'),
				
		));
	}
	
	
	public function new_post_actions($event)
	{
		
	 	$mode = $event['mode'];
	 	if ($mode == 'post'){
	 		$ukrgbFacebook = new \ukrgb\core\controller\facebook(
	 				$this->config,
	 				$this->request,
	 				$this->user,
	 				$this->helper,
	 				$this->phpbb_admin_path,
	 				$this->phpEx);
	 		
	 		$data= $event['data'];
	 		
	 		$body = $data['message'];
	 		strip_bbcode($body);
	 		
	 		$subject = $data['topic_title'] . "\n" . $data['forum_name']. "\n\n" . $body;
		 	
		 	$link = generate_board_url(false) . '/viewtopic.php?f=' . $data['forum_id'] .'&t=' . $data['topic_id'];	
		 	
		 	$postData  = [
		 			'message' => $subject,
		 			'link' => $link,
		 	];
		 	//var_dump($postData);
		 	//die();
		 	
		 	//decode_message
		 	
		 	$ukrgbFacebook->post($postData);
		 			 	
		 	/*
		 	topic_title"]=>
		 	string(14) "Test2- subject"
		 			["topic_first_post_id"]=>
		 			int(0)
		 			["topic_last_post_id"]=>
		 			int(0)
		 			["topic_time_limit"]=>
		 			int(0)
		 			["topic_attachment"]=>
		 			int(0)
		 			["post_id"]=>
		 			int(794386)
		 			["topic_id"]=>
		 			int(125951)
		 			["forum_id"]=>
		 			int(12)
		 			["icon_id"]=>
		 			int(0)
		 			["poster_id"]=>
		 			int(215)
		 			["enable_sig"]=>
		 			bool(true)
		 			["enable_bbcode"]=>
		 			bool(true)
		 			["enable_smilies"]=>
		 			bool(false)
		 			["enable_urls"]=>
		 			bool(true)
		 			["enable_indexing"]=>
		 			bool(true)
		 			["message_md5"]=>
		 			string(32) "4b46efb0c1ed9e010d30e76866b669cd"
		 			["post_checksum"]=>
		 			string(0) ""
		 			["post_edit_reason"]=>
		 			string(0) ""
		 			["post_edit_user"]=>
		 			int(0)
		 			["forum_parents"]=>
		 			string(53) "a:1:{i:29;a:2:{i:0;s:16:"Community Forums";i:1;i:0;}}"
		 			["forum_name"]=>
		 			string(26) "Courses, Trips and Guiding"
		 			["notify"]=>
 					bool(true)
 					["notify_set"]=>
 					int(0)
 					["poster_ip"]=>
 					string(12) "82.10.151.60"
 					["post_edit_locked"]=>
 					int(0)
 					["bbcode_bitfield"]=>
 					string(0) ""
 					["bbcode_uid"]=>
 					string(8) "2w9nvls9"
 					["message"]=>
 					string(21) "this is the post body"
 					["attachment_data"]=>
 					array(0) {
				 	}
				 	["filename_data"]=>
				 	array(1) {
				 		["filecomment"]=>
				 		string(0) ""
				 	}
				 	["topic_status"]=>
				 	int(0)
				 	["topic_visibility"]=>
				 	bool(false)
				 	["post_visibility"]=>
				 	bool(false)
			*/
			
			
			
			
		}
	}
	
	
}
