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

namespace ukrgb\oauth\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
				'core.user_setup' => 'load_language_on_setup',
				'core.page_header' => 'add_page_header_link',
				
				//'core.submit_post_end' => 'new_post_actions',
		);
	}


	/* @var \phpbb\controller\helper */
	protected $helper;
	
	/* @var \phpbb\template\template */
	protected $template;

	protected $user;
	
	
	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper $helper
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user $user
	 */
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
	}
	
	
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
				'ext_name' => 'ukrgb/oauth',
				'lang_set' => 'oauth',
		);
		$event['lang_set_ext'] = $lang_set_ext;
		
		$this->user->add_lang_ext('','ucp');
	}
	
	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
				'U_OAUTH_FB' => $this->helper->route('ukrgb_oauth_route', array('name' => 'facebook')),
				'U_OAUTH_REG_SUBMIT' => $this->helper->route('ukrgb_oauth_register'),
				
		));
	}
	
	
	/*
	public function new_post_actions($event)
	{
		$mode = $event['mode'];
		if ($mode == 'post'){
			$subject = $event['subject'];
			$data= $event['data'];
			error_log("Subject: " . $subject);
			error_log("Data   : " . $data);
		}
	}
	*/
}
