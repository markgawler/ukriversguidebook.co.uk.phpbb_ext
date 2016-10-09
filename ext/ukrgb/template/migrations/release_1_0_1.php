<?php
/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2016 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\template\migrations;
class release_1_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'user_acme');
	}
	static public function depends_on()
	{
		return array('\ukrgb\template\migrations\release_1_0_0');
	}
	
	public function update_data()
	{
		return array(
				array('config.add', array('ukrgb_jdb', ''))		
		);
	}
	
}
