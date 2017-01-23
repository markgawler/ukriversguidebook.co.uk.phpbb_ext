<?php
/**
*
* @package phpBB Extension - JFusion phpBB Extension
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\jfusion\event;

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
			);
		}
	}

	/* @var \phpbb\config\db */
	protected $config;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\request\request */
	protected $request;
	
	/**
	* Constructor
	*
	* @param \phpbb\config\db	$config		Controller helper object
	* @param \phpbb\user			$user	Template object
	*/
	public function __construct(\phpbb\config\db $config, \phpbb\user $user, \phpbb\request\request $request, $root_path,  $php_ext)
	{
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
		error_log('-- UKRGB Jfusion - login ');
		
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
			/*
			if (empty($username) || empty($password))
			{
				if (empty($username))
				{
					error_log('No username ');
					$username = $event['login']['user_row']['username'];
				}
				if (empty($password))
				{
					error_log('No password ');
				}					
			}
			
			else 
			{
			*/
				//detect if the session should be remembered
				if (!empty($event['autologin'])) {
					$remember = 1;
				} else {
					$remember = 0;
				}
				error_log('-- UKRGB Jfusion - Call ');
				
				$joomla->setActivePlugin($this->config['ukrgb_jfusion_jname']);
		
				$joomla->login($username, $password, $remember);
			//}					
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
		error_log('-- UKRGB Jfusion -  logout');
		
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
		if (defined('ADMIN_START'))
		{
			$this->load_language_on_setup($event);
		}
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
				'ext_name' => 'ukrgb/jfusion',
				'lang_set' => 'jfusion',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
	
}
