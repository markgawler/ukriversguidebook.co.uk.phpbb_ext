<?php
/**
 *
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace ukrgb\oauth\controller;

use OAuth\Common\Consumer\Credentials;
use OAuth\OAuth2\Service\Facebook;

use Symfony\Component\HttpFoundation\Response;


class main
{
	/**
	 * Database driver
	 *
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;
	
	/**
	 * phpBB config
	 *
	 * @var \phpbb\config\config
	 */
	protected $config;
	
	/**
	 * phpBB passwords manager
	 *
	 * @var \phpbb\passwords\manager
	 */
	protected $passwords_manager;
	
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
	 * OAuth token table
	 *
	 * @var string
	 */
	protected $auth_provider_oauth_token_storage_table;
	
	/**
	 * OAuth account association table
	 *
	 * @var string
	 */
	protected $auth_provider_oauth_token_account_assoc;
	
	/**
	 * All OAuth service providers
	 *
	 * @var \phpbb\di\service_collection Contains \phpbb\auth\provider\oauth\service_interface
	 */
	protected $service_providers;
	
	/**
	 * Users table
	 *
	 * @var string
	 */
	protected $users_table;
	
	/**
	 * Cached current uri object
	 *
	 * @var \OAuth\Common\Http\Uri\UriInterface|null
	 */
	protected $current_uri;
	
	/**
	 * DI container
	 *
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $phpbb_container;
	
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
	
	/**
	 * OAuth Authentication Constructor
	 *
	 * @param	\phpbb\db\driver\driver_interface	$db
	 * @param	\phpbb\config\config	$config
	 * @param   \phpbb\helper\helper $helper
 	 * @param   \phpbb\template\template $template
	 * @param	\phpbb\passwords\manager	$passwords_manager
	 * @param	\phpbb\request\request_interface	$request
	 * @param	\phpbb\user		$user
	 * @param	string			$auth_provider_oauth_token_storage_table
	 * @param	string			$auth_provider_oauth_token_account_assoc
	 * @param	\phpbb\di\service_collection	$service_providers Contains \phpbb\auth\provider\oauth\service_interface
	 * @param	string			$users_table
	 * @param	\Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container DI container
	 * @param	string			$phpbb_root_path
	 * @param	string			$php_ext
	 */
	
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\passwords\manager $passwords_manager, \phpbb\request\request_interface $request, \phpbb\user $user, $auth_provider_oauth_token_storage_table, $auth_provider_oauth_token_account_assoc, \phpbb\di\service_collection $service_providers, $users_table, \Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container, $phpbb_root_path, $php_ext)
	{ 
		$this->db = $db;
		$this->config = $config;
		$this->helper =$helper;
		$this->template = $template;
		$this->passwords_manager = $passwords_manager;
		$this->request = $request;
		$this->user = $user;
		$this->auth_provider_oauth_token_storage_table = $auth_provider_oauth_token_storage_table;
		$this->auth_provider_oauth_token_account_assoc = $auth_provider_oauth_token_account_assoc;
		$this->service_providers = $service_providers;
		$this->users_table = $users_table;
		$this->phpbb_container = $phpbb_container;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function get_service_credentials()
	{
		return array(
			'key'		=> $this->config['auth_oauth_facebook_key'],
			'secret'	=> $this->config['auth_oauth_facebook_secret'],
		);
	}
	
	/**
	 * Controller for route /oauth/{action}
	 *
	 * @param string $action
	 * @return Response A Symfony Response object
	 */
	public function handle($name)
	{		
		switch ($name) {
			case "facebook":
				return $this->authenticate($name);
				break;
			case "register":
				return $this->register();
				break;
			case "link":
				return $this->link_account();
				break;
			default:
				throw new \exception('UKRGB Oauth Unexpected Name:');
				break;
		}
	}
	
	
	protected function register(array $data = array())
	{
		if ($this->request->is_set('cancel'))
		{
			meta_refresh(3, generate_board_url());
			return $this->helper->message('REG_CANCEL');	
		}
		
		
		include_once($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
		
		$timezone = $this->config['board_timezone'];
		$submit = $this->request->is_set('submit');
		
		if ($submit){
			
			$data = array(
					'username' 			=> utf8_normalize_nfc($this->request->variable('username','', true)),
					'email'				=> strtolower(request_var('email', '')),
					'provider' 			=> $this->request->variable('provider','', true),
					'oauth_unique_id' 		=> $this->request->variable('unique_id','', true),
					'lang'				=> basename(request_var('lang', $this->user->lang_name)),
					'tz'				=> request_var('tz', $timezone),
			);
		}
		// Make password at least 8 characters long, make it longer if admin wants to.
		// gen_rand_string() however has a limit of 12 or 13.
		$user_password = gen_rand_string_friendly(max(8, mt_rand((int) $this->config['min_pass_chars'], (int) $this->config['max_pass_chars'])));
			
		$data = array_merge($data, array(
				'new_password'		=> $user_password,
				'password_confirm'	=> $user_password,
				'lang'				=> basename(request_var('lang', $this->user->lang_name)),
				'tz'				=> request_var('tz', $timezone),
		));

		$error = validate_data($data, array(
				'username'			=> array(
						array('string', false, $this->config['min_name_chars'], $this->config['max_name_chars']),
						array('username', '')),
				'email'				=> array(
						array('string', false, 6, 60),
						array('user_email')),
				'tz'				=> array('timezone'),
				'lang'				=> array('language_iso_name'),
		));
			
		if ($submit){			
			if (!check_form_key('ukrgb_registration'))
			{
				$error[] = $this->user->lang['FORM_INVALID'];
			}
		}
			
		// Replace "error" strings with their real, localised form
		$error = array_map(array($this->user, 'lang'), $error);
			
		if ($submit && !sizeof($error))
		{					
			// Which group by default?
			$group_name = 'REGISTERED';
			
			$sql = 'SELECT group_id
				FROM ' . GROUPS_TABLE . "
				WHERE group_name = '" . $this->db->sql_escape($group_name) . "'
					AND group_type = " . GROUP_SPECIAL;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			
			if (!$row)
			{
				trigger_error('NO_GROUP');
			}
			
			$group_id = $row['group_id'];
			
			$user_type = USER_NORMAL;
			$user_actkey = '';
			$user_inactive_reason = 0;
			$user_inactive_time = 0;
			
			$user_row = array(
					'username'				=> $data['username'],
					'user_password'			=> $this->passwords_manager->hash($data['new_password']),
					'user_email'			=> $data['email'],
					'group_id'				=> (int) $group_id,
					'user_timezone'			=> $data['tz'],
					'user_lang'				=> $data['lang'],
					'user_type'				=> $user_type,
					'user_actkey'			=> $user_actkey,
					'user_ip'				=> $user->ip,
					'user_regdate'			=> time(),
					'user_inactive_reason'	=> $user_inactive_reason,
					'user_inactive_time'	=> $user_inactive_time,
			);
			
			if ($this->config['new_member_post_limit'])
			{
				$user_row['user_new'] = 1;
			}

			// Register user...
			$user_id = user_add($user_row, $cp_data);
			
			// This should not happen, because the required variables are listed above...
			if ($user_id === false)
			{
				trigger_error('NO_USER', E_USER_ERROR);
			}
			
			// Insert provider data int into table, user will be able to log in after this
			$provider_data = array(
					'user_id'			=> $user_id,
					'provider'			=> $data['provider'],
					'oauth_unique_id'	=> $data['oauth_unique_id'],
			);
			$this->link_account_perform_link($provider_data);
				
			$url = generate_board_url() . '/ucp.php?mode=login&login=external&oauth_service=facebook';
			
			$this->template->assign_vars(array(
					'MESSAGE_TITLE' => $this->user->lang('REG_COMPLETE_TITLE'),
					'MESSAGE_TEXT' => $this->user->lang('REG_COMPLETE_TEXT'),
					'MESSAGE_LNK' => $url,
					'MESSAGE_LNK_TXT' => $this->user->lang('REG_COMPLETE_LNK_TXT'),
			));
			meta_refresh(5, $url);
			return $this->helper->render('ukrgb_message.html',$this->user->lang('REG_COMPLETE_TITLE'));
			
		}

		$s_hidden_fields = array(
				'email'				=> strtolower($data['email']),
				'lang'				=> $this->user->lang_name,
				'tz'				=> $this->config['board_timezone'],
				'provider' 			=> $data['provider'],
				'unique_id'		=> $data['oauth_unique_id'],
		);
		add_form_key('ukrgb_registration');
				
		$this->template->assign_vars(array(
				'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',
				'USERNAME' => utf8_normalize_nfc($data['username'], '', true),
				'L_USERNAME_EXPLAIN'		=> $this->user->lang($this->config['allow_name_chars'] . '_EXPLAIN',
						$this->user->lang('CHARACTERS', (int) $this->config['min_name_chars']),
						$this->user->lang('CHARACTERS', (int) $this->config['max_name_chars'])),
				'S_HIDDEN_FIELDS'	=> build_hidden_fields($s_hidden_fields),
		));
		
		return $this->helper->render('registration.html');	
	}
	
	protected function link_account(array $data = array() )
	{
		// Link to an existing account with matching email
		if (empty($data)){
			if (!check_form_key('ukrgb_link_account'))
			{
				$error[] = $this->user->lang['FORM_INVALID'];
				$this->template->assign_vars(array(
						'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',
				));
			}
			$data = array(
					'username'			=> $this->request->variable('username','', true),
					'user_id'			=> $this->request->variable('user_id','', true),
					'provider'			=> $this->request->variable('provider','', true),
					'oauth_unique_id'	=> $this->request->variable('oauth_unique_id','', true),
					'email'				=> $this->request->variable('email','', true),
			);	
		}
		else 
		{
			add_form_key('ukrgb_link_account');
		}
		
		if (empty($error) && $this->request->is_set('confirm')){
			// Link the account
			$this->link_account_perform_link($data);
			$url = generate_board_url() . '/ucp.php?mode=login&login=external&oauth_service=facebook';
			meta_refresh(3, $url);
			return $this->helper->message('LNK_COMPLETE_TEXT');
				
		}
		if ($this->request->is_set('cancel')){
			meta_refresh(3, generate_board_url());
			return $this->helper->message('LNK_CANCEL_TEXT');
		}
		
		// Display form
		// Link to an existing account with matching email
		$this->template->assign_vars(array(
				'USERNAME' =>  $data['username'],
				'EMAIL' =>  $data['email'],
				'S_HIDDEN_FIELDS'	=> build_hidden_fields($data),
		));
		return $this->helper->render('link_account.html',$this->user->lang('LINK_ACCOUNT'));
	}
	
	
	protected function authenticate($name)
	{
		$service_name_original = 'facebook';
		$service_name = 'auth.provider.oauth.service.' . $service_name_original;
		
		// Get the service credentials for the given service
		$service_credentials = $this->service_providers[$service_name]->get_service_credentials();
		
		$storage = new \phpbb\auth\provider\oauth\token_storage($this->db, $this->user, $this->auth_provider_oauth_token_storage_table);
		$query = 'oauth/' . $service_name_original;
		$service = $this->get_service($service_name_original, $storage, $service_credentials, '', array('email'));
				
		if (!$this->request->is_set('code', \phpbb\request\request_interface::GET)) 
		{			
			header('Location: ' . $service->getAuthorizationUri());
			exit;
		}
		$this->service_providers[$service_name]->set_external_service_provider($service);
		$result = $this->perform_auth_login($service);
		
		// Check to see if this provider is already associated with an account
		$data = array(
				'provider'	=> $service_name_original,
				'oauth_provider_id'	=> $result['id']
		);
		
		$sql = 'SELECT user_id FROM ' . $this->auth_provider_oauth_token_account_assoc . '
				WHERE ' . $this->db->sql_build_array('SELECT', $data);
		$sqlresult = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($sqlresult);
		$this->db->sql_freeresult($sqlresult);
		
		if ($row)
		{
			// Account already linked, this is a simple login by oauth provider account
			//TODO: this could be optimised to not require second call to FB
			$url = generate_board_url() .'/ucp.php?mode=login&login=external&oauth_service=facebook';
			header('Location: ' . $url);
			exit;
			
		}
		else 
		{
			// Link or register
			if (!empty($result['email']) && $result['verified'])
			{
				// user not logged in, is the email already registered
				$users = $this->get_user_by_email($result['email']);
				if (sizeof($users) >1){
					//Multiple accounts with this email
					$msg_title = "OAUTH_LNK_FAIL";
					$error_msg = "OAUTH_MULTI_EMAIL";
				}
				elseif (sizeof($users) == 0 ) {
					// No Account with this email address Register a new account.
					$data = array (
							'username'			=> utf8_normalize_nfc($result['name']),
							'provider'			=> $service_name_original,
							'oauth_unique_id'	=> $result['id'],
							'email'				=> $result['email'],
					);
					return $this->register($data);
				}
				else
				{
					// One account exists with this email, link the account to the oauth provider account
					$phpbb_user_id = $users[0]['user_id'];
					$phpbb_username =  $users[0]['username'];
				
					// Insert into table, they will be able to log in after this
					$data = array(
							'username'			=> $phpbb_username,
							'user_id'			=> $phpbb_user_id,
							'provider'			=> $service_name_original,
							'oauth_unique_id'	=> $result['id'],
							'email'				=> $result['email'],
					);
						
					// Check if account already linked
					if ($this->get_unique_id($data))
					{
						// UKRGB account linked to different account with this oauth provider.
						$msg_title = "OAUTH_LNK_FAIL";
						$error_msg = 'OAUTH_LNK_ANOTHER_ACC';
					
					}
					return $this->link_account($data);				
				}
			}
			else 
			{
				// Fail conditions
				if (empty($result['email']))
				{
					$msg_title = "OAUTH_LNK_REG_FAIL";
					$error_msg = 'OAUTH_NO_EMAIL';
						
				} else {
					$msg_title = "OAUTH_LNK_REG_FAIL";
					$error_msg = 'OAUTH_LNK_REG_FAIL_TXT';
				}
			}

		}
		
		$this->template->assign_vars(array(
				'MESSAGE_TITLE' =>  $this->user->lang($msg_title),
				'MESSAGE_TEXT' =>  $this->user->lang($error_msg),
		));
		return $this->helper->render('ukrgb_message.html',$result['name']);

	}

	
	/**
	 * Returns the cached current_uri object or creates and caches it if it is
	 * not already created. In each case the query string is updated based on
	 * the $query parameter.
	 *
	 * @param	string	$service_name	The name of the service
	 * @param	string	$query			The query string of the current_uri
	 *									used in redirects
	 * @return	\OAuth\Common\Http\Uri\UriInterface
	 */
	protected function get_current_uri($service_name, $query)
	{
		if ($this->current_uri)
		{
			$this->current_uri->setQuery($query);
			return $this->current_uri;
		}
	
		$uri_factory = new \OAuth\Common\Http\Uri\UriFactory();
		$super_globals = $this->request->get_super_global(\phpbb\request\request_interface::SERVER);
		if (!empty($super_globals['HTTP_X_FORWARDED_PROTO']) && $super_globals['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			$super_globals['HTTPS'] = 'on';
			$super_globals['SERVER_PORT'] = 443;
		}
		$current_uri = $uri_factory->createFromSuperGlobalArray($super_globals);
		$current_uri->setQuery($query);
	
		$this->current_uri = $current_uri;
		return $current_uri;
	}
	
	
	/**
	 * Returns a new service object
	 *
	 * @param	string	$service_name			The name of the service
	 * @param	\phpbb\auth\provider\oauth\token_storage $storage
	 * @param	array	$service_credentials	{@see \phpbb\auth\provider\oauth\oauth::get_service_credentials}
	 * @param	string	$query					The query string of the
	 *											current_uri used in redirection
	 * @param	array	$scopes					The scope of the request against
	 *											the api.
	 * @return	\OAuth\Common\Service\ServiceInterface
	 * @throws	\Exception
	 */
	protected function get_service($service_name, \phpbb\auth\provider\oauth\token_storage $storage, array $service_credentials, $query, array $scopes = array())
	{
		$current_uri = $this->get_current_uri($service_name, $query);
	
		// Setup the credentials for the requests
		$credentials = new Credentials(
				$service_credentials['key'],
				$service_credentials['secret'],
				$current_uri->getAbsoluteUri()
				);

		$service_factory = new \OAuth\ServiceFactory();
		$service = $service_factory->createService($service_name, $credentials, $storage, $scopes);
	
		if (!$service)
		{
			throw new \Exception('AUTH_PROVIDER_OAUTH_ERROR_SERVICE_NOT_CREATED');
		}
	
		return $service;
	}
	
	/**
	 * 
	 * @param string $email The email of the user account
	 * @return string 
	 */
	protected function get_user_by_email ($email)
	{
		$data = array(
				'user_email' => $email,
		);
		$sql = 'SELECT user_id, username FROM phpbb_users WHERE ' . $this->db->sql_build_array('SELECT', $data);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		return $rows;
		
	}
	
	/**
	 * Performs the query that inserts an account link
	 *
	 * @param	array	$data	This array is passed to db->sql_build_array
	 */
	protected function link_account_perform_link(array $data)
	{
		
		$submit_data = array (
				'user_id' => $data['user_id'],
				'provider' => $data['provider'],
				'oauth_provider_id' => $data['oauth_unique_id']
		);
	
		$sql = 'INSERT INTO ' . $this->auth_provider_oauth_token_account_assoc . '
			' . $this->db->sql_build_array('INSERT', $submit_data);
		$this->db->sql_query($sql);
	}
	
	/**
	 * Get the provider ID from the phpBB database 
	 * 
	 * @param array $data  
	 * @return int or null
	 */
	protected function get_unique_id(array $data)
	{
		$select_data = array (
				'user_id' => $data['user_id'],
				'provider' => $data['provider'],
		);
		$sql = 'SELECT oauth_provider_id FROM '. $this->auth_provider_oauth_token_account_assoc .' WHERE ' . $this->db->sql_build_array('SELECT', $select_data);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		return $row['oauth_provider_id'];
	}
	
	protected function perform_auth_login($service)
	{
		if (!($service instanceof \OAuth\OAuth2\Service\Facebook))
		{
			throw new \exception('AUTH_PROVIDER_OAUTH_ERROR_INVALID_SERVICE_TYPE');
		}
		// This was a callback request, get the token
		$token = $service->requestAccessToken($this->request->variable('code', ''));
		
		// Send a request with it
		return json_decode($service->request('/me?fields=first_name,name,email,verified,id'), true);
	}
}
