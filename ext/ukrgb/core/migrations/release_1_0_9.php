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
 * Class release_1_0_9
 * @package ukrgb\core\migrations
 */
class release_1_0_9 extends \phpbb\db\migration\migration
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
		return array('\ukrgb\core\migrations\release_1_0_8');
	}



    public function update_schema()
    {
        return array(
            'add_columns' => array(
                $this->table_prefix . 'ukrgb_images' => array(
                    'upload_time' => array('INT:11', 0, 'after' => 'file_key'),
                )
            )
        );
    }


    public function revert_schema()
    {
        return array(
            'drop_columns' => array(
                $this->table_prefix . 'ukrgb_images' => array(
                    'upload_time',
                ),
            ),
        );
    }
}