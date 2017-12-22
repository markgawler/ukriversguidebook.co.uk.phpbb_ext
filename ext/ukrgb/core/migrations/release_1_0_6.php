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
 * Class release_1_0_6
 * @package ukrgb\core\migrations
 */
class release_1_0_6 extends \phpbb\db\migration\migration
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
		return array('\ukrgb\core\migrations\release_1_0_5');
	}
	
	/**
	 * Add the ukrgb_pending_actions table schema to the database:
	 *	:
	 *		id
	 *		action
	 *		data
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function update_schema()
	{
		error_log('release_1_0_5 - add_tables');
		
		return array(
				'add_tables' => array(
						$this->table_prefix . 'ukrgb_images'	=> array(
								'COLUMNS'	=> array(
										'id' => array('UINT', NULL, 'auto_increment'),
										'file_key'	=> array('VCHAR:18', ''),
										'poster_id'	=> array('INT:10', 0),
                                        'post_id'   => array('INT:10', 0),
                                        'topic_id'  => array('INT:10', 0),
                                        'forum_id'  => array('INT:10', 0),
                                        'in_post'   => array('UINT:1', 0)
								),
								'PRIMARY_KEY' => array('id',),
                                'KEYS' => array(
                                    'file_key_idx' => array('UNIQUE', 'file_key'),
                                ),
						),
				),
		);
	}
	/**
	 * Drop the pages table schema from the database
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
				'drop_tables'	=> array(
						$this->table_prefix . 'ukrgb_images',
				),
		);
	}
	
	
	
}
