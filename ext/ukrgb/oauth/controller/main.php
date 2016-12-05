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
//	protected $passwords_manager;
	
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
	//use phpbb\controller\helper;
	
	//public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\passwords\manager $passwords_manager, \phpbb\request\request_interface $request, \phpbb\user $user, $auth_provider_oauth_token_storage_table, $auth_provider_oauth_token_account_assoc, \phpbb\di\service_collection $service_providers, $users_table, \Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container, $phpbb_root_path, $php_ext)
	//public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template,  \phpbb\passwords\manager $passwords_manager, \phpbb\request\request_interface $request, \phpbb\user $user, $auth_provider_oauth_token_storage_table, $auth_provider_oauth_token_account_assoc, \phpbb\di\service_collection $service_providers, $users_table, \Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container, $phpbb_root_path, $php_ext)
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\request\request_interface $request, \phpbb\user $user, $auth_provider_oauth_token_storage_table, $auth_provider_oauth_token_account_assoc, \phpbb\di\service_collection $service_providers, $users_table, \Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container, $phpbb_root_path, $php_ext)
	{ 
		$this->db = $db;
		$this->config = $config;
		$this->helper =$helper;
		$this->template = $template;
		//$this->passwords_manager = $passwords_manager;
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
	 * Demo controller for route /demo/{name}
	 *
	 * @param string $name
	 * @return Response A Symfony Response object
	 */
	public function handle($name)
	{
		$service_name_original = 'facebook';
		$service_name = 'auth.provider.oauth.service.' . $service_name_original;
		
		$storage = new \phpbb\auth\provider\oauth\token_storage($this->db, $this->user, $this->auth_provider_oauth_token_storage_table);
		$query = 'oauth/' . $service_name_original;
		$service_credentials = array(
				'key'		=> $this->config['auth_oauth_facebook_key'],
				'secret'	=> $this->config['auth_oauth_facebook_secret'],
		);

		//$service = $this->get_service($service_name_original, $storage, $service_credentials, $query, array());
		$service = $this->get_service($service_name_original, $storage, $service_credentials, '', array('email'));
				
		if (!$this->request->is_set('code', \phpbb\request\request_interface::GET)) 
		{
			$url = $service->getAuthorizationUri();
			
			header('Location: ' . $url);
			exit;
		}
		
		if (!($service instanceof \OAuth\OAuth2\Service\Facebook))
		{
			throw new \exception('AUTH_PROVIDER_OAUTH_ERROR_INVALID_SERVICE_TYPE');
		}
		// This was a callback request, get the token
		$token = $service->requestAccessToken($this->request->variable('code', ''));
		
		// Send a request with it
		$result = json_decode($service->request('/me?fields=first_name,name,email,verified,id'), true);
		
		if ($this->user->data['user_id'] == 1 || $result['email'] == null || !$result['verified'])
		{
			// user not logged in, is the email already registered
			$users = $this->get_user_by_email($result['email']);
			if (sizeof($users) >1){
				$error_msg = "Multiple user accounts associated with this email, please contact the Administrator for assistance";
			} 
			elseif (sizeof($users) == 0 ) {
				//$error_msg = "No Account with this email address Register a new account";
				return $this->helper->render('ukrgb_registration.html',$result['name']);
			}
			else
			{
				$phpbb_user_id = $users[0]['user_id'];
				$phpbb_username =  $users[0]['username'];
				$error_msg = "phpbb user_id: ".$phpbb_user_id." , phpbb username: ".$phpbb_username;
				
				// Insert into table, they will be able to log in after this
				$data = array(
						'user_id'			=> $phpbb_user_id,
						'provider'			=> $service_name_original,
						'oauth_provider_id'	=> $result['id'],
				);
				
				// Check if account already linked
				$curent_id = $this->get_provider_id($data);
				if ($curent_id)
				{
					if ($curent_id == $result['id'])
					{
						// Account already linked
						//TODO: this could be optimised to not require second call to FB
						$url = 'http://area51.ukriversguidebook.co.uk/forum/ucp.php?mode=login&login=external&oauth_service=facebook';
						header('Location: ' . $url);
						exit;
					} else {
						$error_msg = "Your UK Rivers Account is already linked to different Facebook account: ". $curent_id . ', ' . $result['id'];	
					}
				}
				else
				{
						
					$error_msg = "Linking Account: " . $url;
	
					$this->link_account_perform_link($data);
					
					$url = 'http://area51.ukriversguidebook.co.uk/forum/ucp.php?mode=login&login=external&oauth_service=facebook';
					header('Location: ' . $url);
					exit;
				}
				
			}
			
		} else {
			// Fail conditions
			if ($result['email'] == null)
			{
				$error_msg = "No Email returned, account can't be linked or created";
				
			} else {
				$error_msg = 'The user is logged in. ID: '. $this->user->data['user_id'];
			}
		}
		
		$this->template->assign_vars(array(
				'UKRGB_HELLO_NAME' =>  $result['first_name'], 
				'UKRGB_HELLO_EMAIL' => $result['email'],
				'UKRGB_HELLO_ERROR' => $error_msg,
		));
		return $this->helper->render('oauth_body.html',$result['name']);

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
		$sql = 'INSERT INTO ' . $this->auth_provider_oauth_token_account_assoc . '
			' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);
	}
	
	/**
	 * Get the provider ID from the phpBB database 
	 * 
	 * @param array $data  
	 * @return int or null
	 */
	protected function get_provider_id(array $data)
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
}
