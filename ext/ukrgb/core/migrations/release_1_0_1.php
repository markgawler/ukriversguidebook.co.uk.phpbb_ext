<?php
/**
*
* @package phpBB Extension - UKRGB Core phpBB Extension
* @copyright (c) 2017 Mark Gawler
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\core\migrations;

/**
 * Class release_1_0_1
 * @package ukrgb\core\migrations
 */
class release_1_0_1 extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	static public function depends_on()
	{
		error_log('release_1_0_1 - depends_on');
	
		return array('\ukrgb\core\migrations\release_1_0_0');
	}
	
	/**
	 * Allows you to check if the migration is effectively installed (entirely optional)
	 * @return bool True if this migration is installed, False if this migration is not installed (checked on install)
	 */
	public function effectively_installed()
	{
		error_log('release_1_0_1 - effectively_installed');
		
		return isset($this->config['ukrgb_fb_page_token']);
	}


	/**
	 * Updates data by returning a list of instructions to be executed
	 *
	 * @return array Array of data update instructions
	 */
	public function update_data()
	{
		error_log('release_1_0_1 - update data');
		
		return array(
				
				array('config.add', array('ukrgb_fb_page_token', '')),
				array('config.add', array('ukrgb_fb_page_id', '')),
		
				
		);
	}
}
