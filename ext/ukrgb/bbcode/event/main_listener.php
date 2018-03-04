<?php
/**
*
* @package phpBB Extension - UKRGB Core phpBB Extension
* @copyright (c) 2017 Mark Gawler
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\bbcode\event;

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
	    return array(
	        'core.user_setup' => 'coreUserSetup',
        );
	}

	/* @var \phpbb\language\language */
	protected $language;


    /**
     * main_listener constructor.
     * @param \phpbb\language\language $language
     */
	public function __construct(\phpbb\language\language $language)
	{
		$this->language = $language;
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\Event $event
	 */
	public function coreUserSetup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'ukrgb/bbcode',
			'lang_set' => 'bbcode',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
