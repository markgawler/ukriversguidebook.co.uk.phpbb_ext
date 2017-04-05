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
 * Class release_1_0_5
 * @package ukrgb\core\migrations
 */
class release_1_0_5 extends \phpbb\db\migration\migration
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
		return array('\ukrgb\core\migrations\release_1_0_4');		
	}
	
	
	/**
	 * Updates data by returning a list of instructions to be executed
	 *
	 * @return array Array of data update instructions
	 */
	public function update_data()
	{
		error_log('release_1_0_5 - update data');
	
		return array(
				array('config.add', array('ukrgb_delayed_action_gc', 300)),
				array('config.add', array('ukrgb_delayed_action_last_gc', '0', 1)),			
		);
	}
	
	
}
