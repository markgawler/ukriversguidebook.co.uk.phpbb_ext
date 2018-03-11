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
				'core.approve_posts_after' => 'coreApprovePostsAfter',
				'core.move_topics_before_query' => 'moveTopicsBeforeQuery',
                'core.viewtopic_modify_post_action_conditions' => 'modify_post_action_conditions',
                'core.posting_modify_cannot_edit_conditions' => 'modify_post_action_conditions',
                'core.permissions' => 'core_permissions',
                'core.posting_modify_message_text' => 'modify_deprecated_bbcodes'
			);
		}
	}

	/* @var \phpbb\auth\auth */
	protected $auth;
	
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

	/* @var string */
	protected $ukrgb_pending_actions_table;

	/* @var string */
	protected $ukrgb_images_table;

	/**
	 * @var  \ukrgb\core\model\facebook_bridge
	 */
	protected $ukrgbFacebook;

	/**
    * @var  \ukrgb\core\model\image
    */
	protected $ukrgbImage;

	/**
	 * @var string $root_path
	 */
	protected $root_path;

	/**
	 * @var string $php_ext
	 */
	protected $php_ext;

	/** @var \phpbb\language\language  $language */
	protected $language;

	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper   $helper
	 * @param \phpbb\template\template  $template
	 * @param \phpbb\config\db 		    $config,
	 * @param \phpbb\user			    $user	Template object
	 * @param \phpbb\request\request    $request
	 * @param \phpbb\db\driver\driver_interface	$db
     * @param \phpbb\auth\auth          $auth
     * @param \phpbb\language\language  $language
	 * @param string                    $root_path
	 * @param string                    $php_ext
	 * @param string 				    $ukrgb_fb_posts_table
	 * @param string 				    $ukrgb_pending_actions_table
     * @param string                    $ukrgb_images_table'
     * param \phpbb\language\language language
	 */
	public function __construct(\phpbb\controller\helper $helper, 
			\phpbb\template\template $template, 
			\phpbb\config\db $config, 
			\phpbb\user $user, 
			\phpbb\request\request $request,
			\phpbb\db\driver\driver_interface	$db,
			\phpbb\auth\auth    $auth,
			\phpbb\language\language $language,
			$root_path,
			$php_ext,
			$ukrgb_fb_posts_table,
			$ukrgb_pending_actions_table,
            $ukrgb_images_table
        )
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
		$this->db = $db;
		$this->auth = $auth;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->ukrgb_fb_posts_table = $ukrgb_fb_posts_table;
        $this->ukrgb_pending_actions_table = $ukrgb_pending_actions_table;
        $this->ukrgb_images_table = $ukrgb_images_table;
        $this->language;
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
	 */
	public function coreSessionKillAfter()
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
        /** @noinspection PhpIncludeInspection */
        require_once $apipath . '/jfusionapi.php';
		return \JFusionAPIInternal::getInstance();
	}
	
	/**
	 * Language setup
	 *
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function loadLanguageOnSetup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
            'ext_name' => 'ukrgb/core',
            'lang_set' => 'core',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
	

	public function corePageHeader()
	{
		$this->template->assign_vars(array(
            'U_OAUTH_FB' => $this->helper->route('ukrgb_oauth_route', array('name' => 'facebook')),
            'U_OAUTH_REG_SUBMIT' => $this->helper->route('ukrgb_oauth_register'),
            'U_OAUTH_LNK_SUBMIT' => $this->helper->route('ukrgb_oauth_link'),
            'IS_BETA_TEST' => $this->isBetaTester()
		));
	}
	
	
	public function coreSubmitPostEnd($event)
    {
        // Facebook Post
        $visibility = $event['post_visibility'];
        $data = $event['data'];
        if ($visibility == ITEM_APPROVED) {
            $mode = $event['mode'];
            //error_log("Posting Mode:" . $mode);

            switch ($mode) {
                case 'post':
                case 'edit':
                    $this->post(
                        $data['forum_id'],
                        $data['topic_id'],
                        $data['post_id'],
                        $data['message'],
                        $mode);
                    break;
                case 'reply':
                    break;
                default:
                    error_log('Unhandled Posting mode: ' . $mode);
            }
        }


        // Image tracking

        $postText = $data['message'];
        strip_bbcode($postText);
        preg_match_all('#https://media\.ukriversguidebook\.co\.uk/uploads/([0-9]+)/([0-9]+-[0-9]+)\.png#', $postText, $matches);

        if (count($matches[0]) > 0) {
            foreach ($matches[2] as $key => $file_key) {
                $user_id = $matches[1][$key];
                $image = new \ukrgb\core\model\image(
                    $this->db, $this->ukrgb_images_table, $file_key, $data['forum_id'], $data['topic_id'], $data['post_id'], 0, $user_id);
                $image->store_forum_data();
            }
        }

    }

    /**
     * @param \Symfony\Component\EventDispatcher\Event $event
     */
    public function modify_deprecated_bbcodes($event)
    {
        $pattern = array(
            '/(\[youtube\])([0-9a-zA-Z\-_]+)(\[\/youtube\])/i',
            '/(\[vimeo\])([0-9]+)(\[\/vimeo\])/i');
        $replacement = array(
            'https://www.youtube.com/watch?v=$2',
            'https://vimeo.com/$2');

        $message = $event['message_parser']->message;
        $count = 0;
        $new_text = preg_replace($pattern, $replacement, $message, -1, $count);
        if ($count != 0){
            $event['message_parser']->message = $new_text;
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
                    $post['post_text'],
                    'post');
			}
		}
	}
	
	/*
	 * Use the core.move_topics_before_query event to detect a topic being removed from the allowed forums, 
	 */
	public function moveTopicsBeforeQuery($event)
	{
		$forum_id = $event['forum_id'];
		if (!$this->isAllowedForum($forum_id)){	
			$this->initFacebookBridge();
			$this->ukrgbFacebook->queueDeleteTopic($event['topic_ids']);
		}
		
	}
	
	protected function post($forumId, $topicId, $postId, $postText, $mode)
	{		
		if ($this->can_post_to_fb($forumId)){
			$this->initFacebookBridge();
			strip_bbcode($postText);
			$this->ukrgbFacebook->queuePost($postText, $forumId, $topicId, $postId, $mode);
		}
	}

	
	protected function can_post_to_fb($forum)
	{
		$auto_post_enabled = $this->config['ukrgb_fb_auto_post'];
		if ($auto_post_enabled) {
			return $this->isAllowedForum($forum);
		}
		return false;
	}
	
	protected function isAllowedForum($forum)
	{
		$allowed_forums = explode(',',  $this->config['ukrgb_fb_subforums']);
		return in_array($forum, $allowed_forums);
	}
	
	public function coreDeletePostsAfter($event)
	{
		$this->initFacebookBridge();
		$this->ukrgbFacebook->queueDeletePost($event['post_ids']);
		
	}
	
	protected function initFacebookBridge()
	{
		if (empty($this->ukrgbFacebook)) {
			$this->ukrgbFacebook = new \ukrgb\core\model\facebook_bridge(
				$this->config,
				$this->db,
				$this->ukrgb_fb_posts_table,
				$this->ukrgb_pending_actions_table);
		}
	}

	public function core_permissions($event)
    {
        $permissions = $event['permissions'];
        $permissions['u_ignore_edit_time'] = array('lang' => 'ACL_U_IGNORE_EDIT_TIME', 'cat' => 'post');
        $event['permissions'] = $permissions;
    }

	public function modify_post_action_conditions($event)
    {
        if ($this->auth->acl_get('u_ignore_edit_time'))
        {
            $event['s_cannot_edit_time'] = 0;
        }
    }

    /**
     * Is the user a beta tester
     * @return bool
     */
	protected function isBetaTester()
	{
		if ($this->config['ukrgb_beta_enabled'])
		{
			if (!function_exists('group_memberships'))
			{
                /** @noinspection PhpIncludeInspection */
                include($this->root_path . 'includes/functions_user.' . $this->php_ext);
			}

			$memberships = array();
			foreach (group_memberships(false, $this->user->data['user_id']) as $grp)
			{
				$memberships[] = $grp["group_id"];
			}
			$groups = explode(',', $this->config['ukrgb_beta_group']);
			return !empty(array_intersect($groups, $memberships));
		} else {
			return false;
		}
	}
}
