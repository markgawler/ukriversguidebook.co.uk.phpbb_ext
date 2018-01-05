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
 * Class release_1_0_8
 * @package ukrgb\core\migrations
 */
class release_1_0_8 extends \phpbb\db\migration\migration
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
		return array('\ukrgb\core\migrations\release_1_0_7');
	}

    public function update_data()
    {
        return array(
            array('config.add', array('ukrgb_cleanup_gc', 300)),
            array('config.add', array('ukrgb_cleanup_last_gc', '0', 1)),
        );
    }
	
}
