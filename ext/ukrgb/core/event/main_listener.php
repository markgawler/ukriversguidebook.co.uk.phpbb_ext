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
					'core.user_setup' => 'coreUserSetup',
					'core.auth_login_session_create_before' => 'coreAuthLoginSessionCreateBefore',
					'core.session_kill_after' => 'coreSessionKillAfter',
					'core.page_header' => 'corePageHeader',
					'core.submit_post_end' => 'coreSubmitPostEnd',
					'core.delete_posts_after' => 'coreDeletePostsAfter',
					//'core.delete_topics_after_query' =>' coreDeleteTopicsAfterQuery',
					'core.delete_user_after' => 'coreDeleteUserAfter',
					'core.approve_posts_after' => 'coreApprovePostsAfter',
					'core.approve_topics_after' => 'coreApproveTopicsAfter',
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
	 * Database driver
	 *
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;
	
	/* @var string */
	protected $ukrgb_fb_posts_table;
	
	protected $ukrgbFacebook;
	/**
	* Constructor
	*
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\template\template $template
	* @param \phpbb\config\db 		  $config, 
	* @param \phpbb\user			  $user	Template object
	* @request \phpbb\request 	 	  $request
	* @param \phpbb\db\driver\driver_interface	$db
	* @param
	* @param
	* @param string 				  $ukrgb_fb_posts_table
	* 
	*/
	public function __construct(\phpbb\controller\helper $helper, 
			\phpbb\template\template $template, 
			\phpbb\config\db $config, 
			\phpbb\user $user, 
			\phpbb\request\request $request,
			\phpbb\db\driver\driver_interface	$db,
			$root_path,  
			$php_ext,
			$ukrgb_fb_posts_table)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
		$this->db = $db;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->ukrgb_fb_posts_table = $ukrgb_fb_posts_table;
	}

	
	/**
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function coreAuthLoginSessionCreateBefore($event)
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
	public function coreSessionKillAfter($event)
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
	public function coreUserSetup($event)
	{		
		$this->loadLanguageOnSetup($event);
	}
		
	
	/**
	 * @return \JFusionAPIInternal
	 */
	protected function startJoomla() {
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
	public function loadLanguageOnSetup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
				'ext_name' => 'ukrgb/core',
				'lang_set' => 'core',
		);
		$event['lang_set_ext'] = $lang_set_ext;
		
		$this->user->add_lang_ext('','ucp');
		
	}
	
	public function corePageHeader($event)
	{
		$this->template->assign_vars(array(
				'U_OAUTH_FB' => $this->helper->route('ukrgb_oauth_route', array('name' => 'facebook')),
				'U_OAUTH_REG_SUBMIT' => $this->helper->route('ukrgb_oauth_register'),
				'U_OAUTH_LNK_SUBMIT' => $this->helper->route('ukrgb_oauth_link'),
		));
	}
	
	
	public function coreSubmitPostEnd($event)
	{
		$visibility = $event['post_visibility'];
		if ($visibility == ITEM_APPROVED) {
		 	$mode = $event['mode'];
		 	if ($mode == 'post'){
		 		
		 		$data= $event['data'];
		 		$this->post(
		 				$data['forum_id'], 
		 				$data['topic_id'], 
		 				$data['post_id'], 
		 				$data['forum_name'], 
		 				$data['topic_title'],
		 				$data['message'],
		 				$event['username']);
			}
		}
	}
	
	public function coreApprovePostsAfter($event)
	{
		foreach ($event['post_info'] as $post )
		{
			if ($post['topic_first_post_id'] == $post['post_id']){
				$this->post(
						$post['forum_id'],
						$post['topic_id'],
						$post['post_id'],
						$post['forum_name'],
						$post['post_subject'],
						$post['post_text'],
						$post['username']);
			}
		}
	}
	
	protected function post($forumId, $topicId, $postId, $forumName, $topicTitle, $postText, $username)
	{
		if (empty($this->ukrgbFacebook)) {
			$this->ukrgbFacebook = new \ukrgb\core\model\facebook_bridge(
					$this->config,
					$this->db,
					$this->ukrgb_fb_posts_table);
		}
		strip_bbcode($postText);
		$message = 'Title: ' . $topicTitle. "\nForum: " . $forumName . "\nBy: " . $username . "\n\n" . $postText;
		$this->ukrgbFacebook->post($message, $forumId, $topicId, $postId);
	}
	
	public function coreDeletePostsAfter($event)
	{
		$fb = new \ukrgb\core\model\facebook_bridge($this->config, $this->db, $this->ukrgb_fb_posts_table);
		$fb->delete_post($event['post_ids']);
		
	}
	
/*	public function coreDeleteTopicsAfterQuery()
	{
		error_log('coreDeleteTopicsAfterQuery');
		return true;
	}
	*/

	public function coreDeleteUserAfter()
	{
		error_log('coreDeleteUserAfter');
		return true;
	}
		
	public function coreApproveTopicsAfter($event)
	{
		error_log('coreApproveTopicsAfter:');
	}
	
}
