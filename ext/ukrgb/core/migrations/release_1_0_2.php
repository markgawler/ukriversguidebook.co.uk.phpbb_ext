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
 * Class release_1_0_2
 * @package ukrgb\core\migrations
 */
class release_1_0_2 extends \phpbb\db\migration\migration
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
		error_log('release_1_0_2 - depends_on');
		
		return array('\ukrgb\core\migrations\release_1_0_1');		
	}
	
	

	/**
	 * Allows you to check if the migration is effectively installed (entirely optional)
	 * @return bool True if this migration is installed, False if this migration is not installed (checked on install)
	 */
	public function effectively_installed()
	{
		error_log('release_1_0_2 - effectively_installed');
	
		return isset($this->config['ukrgb_fb_post_topics']);
	}


	/**
	 * Updates data by returning a list of instructions to be executed
	 *
	 * @return array Array of data update instructions
	 */
	public function update_data()
	{
		error_log('release_1_0_2 - update data');
		return array(
		
				array('config.add', array('ukrgb_fb_post_topics', '0')),
		);
	}
	
	/**
	 * Add the pages table schema to the database:
	 *	pages:
	 *		post_id
	 *		topic_id
	 *		graph_node
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function update_schema()
	{
		error_log('release_1_0_2 - add_tables');
		
		return array(
				'add_tables'	=> array(
						$this->table_prefix . 'ukrgb_fb_posts'	=> array(
								'COLUMNS'	=> array(
										'post_id'		=> array('UINT', 0),
										'topic_id'		=> array('UINT', 0),
										'graph_node'	=> array('VCHAR:40', ''),
								),
								'PRIMARY_KEY'	=> array('post_id', 'topic_id'),
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
						$this->table_prefix . 'ukrgb_fb_posts',
				),
		);
	}
	
	
	
}
