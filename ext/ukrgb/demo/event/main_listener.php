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

namespace ukrgb\demo\event;

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
				'core.submit_post_end' => 'new_post_actions',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
				'ext_name' => 'ukrgb/demo',
				'lang_set' => 'demo',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
	
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
	
}
