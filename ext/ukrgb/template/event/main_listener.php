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

namespace ukrgb\template\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
				'core.user_setup' => 'core_user_setup',
				'core.viewforum_get_topic_data' => 'core_viewforum_get_topic_data',
		);
	}

	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;
	
	/** @var \phpbb\config\config */
	protected $config;
	
	/** @var \phpbb\user */
	protected $user;
	
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \PDO */
	protected $pdo;
	
	/**
	 * Constructor
	 *
	 * @param \phpbb\template\template             $template          Template object
	 * @param \phpbb\cache\driver\driver_interface $cache             Cache driver interface
	 * @param \phpbb\config\config                 $config	
     * @param \phpbb\user                   $user              User object


	 */
	public function __construct(\phpbb\template\template $template, 
			\phpbb\cache\driver\driver_interface $cache, \phpbb\config\config $config, \phpbb\user $user)
	{
		$this->template = $template;
		$this->cache = $cache;
		$this->config = $config;
		$this->user = $user;
		$this->pdo = null;
	}
	
	/**
	 * Core Setup Event handler
	 *  
	 * @param unknown $event
	 */
	public function core_user_setup($event){
		$this->load_language_on_setup($event);
		
		if (!defined('ADMIN_START'))
		{
			$this->load_banner($event);
			$this->template->assign_vars(array('U_UKRGB_USER_ID' => $this->user->data['user_id'],
			));
		}
	}
	
	/**
	 * core viewforum get topic data Event Handler
	 * 
	 *  @param unknown $event
	 */
	public function core_viewforum_get_topic_data($event){
		// Get the Jommla com_ukrgb component parameters
		$params = $this->load_component_params();
		
		$forum_id = $event['forum_id'];
		if (in_array($forum_id, $params->selected_forums))
		{
			// Set up PayPal button parameters
			if ($params->sandbox)
			{
				$this->template->assign_vars(array(
						'UKRGB_SHOW_PP_DONATE' => true,
						'U_UKRGB_PP_URL' => "https://www.sandbox.paypal.com/cgi-bin/webscr",
						'UKRGB_PP_HOSTED_BUTTON_ID' => $params->sandbox_hosted_button_id,
				));
			}
			else
			{
				$this->template->assign_vars(array(
						'UKRGB_SHOW_PP_DONATE' => true,
						'U_UKRGB_PP_URL' => "https://www.paypal.com/cgi-bin/webscr",
						'UKRGB_PP_HOSTED_BUTTON_ID' => $params->hosted_button_id,
				));
			}
		}
		else 
		{
			$this->template->assign_vars(array(
					'UKRGB_SHOW_PP_DONATE' => false,
			));
		}
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
				'ext_name' => 'ukrgb/template',
				'lang_set' => 'template',
		);
		$event['lang_set_ext'] = $lang_set_ext;
		
	}
	
	/***
	 * Banner setup
	 * - Load Banner data from Joomla DB and store in phpBB cache
	 * 
	 * @param unknown $event
	 */
	public function load_banner($event)
	{
	
		$banner_data = $this->cache->get('_ukrgb_banner_data');
		if ($banner_data == false)
		{
			// get banner data from Joomla database and cache the html.
			
			$pdo = $this->get_joomla_db();
			
			$stmt = $pdo->query('SELECT `name`,`clickurl`,`params`,`custombannercode` FROM `jos_banners` WHERE `state`=1');

			while ($row = $stmt->fetch())
			{
				$params  = json_decode($row['params']);
				$clickurl = $row['clickurl'];
				$name = $row['name'];
				$imageurl = $params->imageurl;
				if ($imageurl){
					$banner_data[] = '<a href="'.$clickurl.'" title="'.$name.'" onclick="trackOutboundLink(\''.$clickurl.'\'); return false;" target="_blank"> <img src="/'.$imageurl.'" alt="'.$params->alt.'"></a>';
				}
				else
				{
					$banner_data[] = $row['custombannercode'];
				}
			}			
			$this->cache->put('_ukrgb_banner_data',$banner_data);
			
		}
		
		$max = count($banner_data)-1;
		
		$html = array("l" => "","r" => "",);
		foreach ($html as $key => $v)
		{
			$index = rand(0,$max);
			$html[$key] = $banner_data[$index];
		}

		$this->template->assign_vars(array(
				'U_UKRGB_BANNER_LEFT' => $html['l'],
				'U_UKRGB_BANNER_RIGHT' => $html['r'],
		));
		
	}
	
	/**
	 * Get the Jommla com_ukrgb component parameters
	 * 
	 * return $params 
	 */
	private function load_component_params()
	{
		$params = $this->cache->get('_ukrgb_com_params');
		if ($params == false)
		{
			$pdo = $this->get_joomla_db();
				
			$stmt = $pdo->query("SELECT `params` FROM `jos_extensions` WHERE `name` = 'com_ukrgb'");
			$row = $stmt->fetch();
			$params = json_decode($row['params']);
			
			$this->cache->put('_ukrgb_com_params',$params);
		}
		return $params;		
	}
	
	
	/**
	 * Create Joomla DB connection object if the object dose not exist.
	 * Return a PDO object.
	 * 
	 * @return \PDO|PDO
	 */
	private function get_joomla_db()
	{
		if ($this->pdo == null)
		{				
			$host = $this->config['ukrgb_jdbhost'];
			$db  =  $this->config['ukrgb_jdb'];
			$user = $this->config['ukrgb_jdbuser'];
			$pass = $this->config['ukrgb_jdbpwd'];
			$charset = 'utf8';
			
				
			$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
				
			$opt = [
					\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
					\PDO::ATTR_EMULATE_PREPARES   => false,
			];
				
			$pdo = new \PDO($dsn, $user, $pass, $opt);
			$this->pdo = $pdo;
		}
		else
		{
			$pdo = $this->pdo;
		}
		
		return $pdo;
		
	}
	
}
