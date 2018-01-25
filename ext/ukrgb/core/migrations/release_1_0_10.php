<?php
/**
*
* @package phpBB Extension - UKRGB Core phpBB Extension
* @copyright (c) 2018 Mark Gawler
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\core\migrations;
/**
 * Class release_1_0_10
 * @package ukrgb\core\migrations
 */
class release_1_0_10 extends \phpbb\db\migration\migration
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
		return array('\ukrgb\core\migrations\release_1_0_9');
	}


	public function update_data()
    {
        //Can ignore edit time Limit
        return array(
            array('permission.add', array('u_ignore_edit_time')),
            array('permission.permission_set', array('REGISTERED', 'u_ignore_edit_time', 'group')),
            array('permission.permission_unset', array('ROLE_USER_STANDARD', 'u_ignore_edit_time')),


        );
    }
}