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
	
	/**
	 * Constructor
	 *
	 * @param \phpbb\template\template             $template          Template object
	 * @param \phpbb\cache\driver\driver_interface $cache             Cache driver interface

	 */
	public function __construct(\phpbb\template\template $template, \phpbb\cache\driver\driver_interface $cache)
	{
		$this->template = $template;
		$this->cache = $cache;
		
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
		$banner_data = $this->cache->get('_ukrgb_banner_data');
		
		if ($banner_data == false)
		{
			$banner_data = array(
					'l' => 'Banner Left' . date('H:m:s'),
					'r' => 'Ranner Right'. date('H:m:s')
			);
			$this->cache->put('_ukrgb_banner_data',$banner_data);
			
		}
		
		//error_log ($banner_data['l']);
		
		$this->template->assign_vars(array(
				'U_UKRGB_BANNER_LEFT' => $banner_data['l'],
				'U_UKRGB_BANNER_RIGHT' => $banner_data['r'],
		));
		
	}

}
