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
				
		);
	}

	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;
	
	/** @var \phpbb\config\config */
	protected $config;
	
	/**
	 * Constructor
	 *
	 * @param \phpbb\template\template             $template          Template object
	 * @param \phpbb\cache\driver\driver_interface $cache             Cache driver interface
	 * @param \phpbb\config\config                 $config			

	 */
	public function __construct(\phpbb\template\template $template, \phpbb\cache\driver\driver_interface $cache, \phpbb\config\config $config)
	{
		$this->template = $template;
		$this->cache = $cache;
		$this->config = $config;
	}
	
	
	public function core_user_setup($event){
		$this->load_language_on_setup($event);
		$this->load_banner($event);
	}
	
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
				'ext_name' => 'ukrgb/template',
				'lang_set' => 'template',
		);
		$event['lang_set_ext'] = $lang_set_ext;
		
	}
	
	public function load_banner($event)
	{
		$banner_data = $this->cache->get('_ukrgb_banner_da1ta');
		
		if ($banner_data == false)
		{
			// get banner data from Joomla database
			$host = $this->config['ukrgb_jdbhost'];
			$db   =  $this->config['ukrgb_jdb'];
			$user = $this->config['ukrgb_jdbuser'];
			$pass = $this->config['ukrgb_jdbpwd'];
			$charset = 'utf8';

			error_log('jdb Host:'.$host);
			error_log('jdb User:'.$user);
				
			/*
			$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
			$opt = [
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES   => false,
			];
			$pdo = new PDO($dsn, $user, $pass, $opt);
			
			*/
			
			
			$banner_data = array(
					'l' => 'Banner Left' . date('H:m:s'),
					'r' => 'Ranner Right'. date('H:m:s')
			);
			$this->cache->put('_ukrgb_banner_data',$banner_data);
			
		}
				
		$this->template->assign_vars(array(
				'U_UKRGB_BANNER_LEFT' => $banner_data['l'],
				'U_UKRGB_BANNER_RIGHT' => $banner_data['r'],
		));
		
	}

}
