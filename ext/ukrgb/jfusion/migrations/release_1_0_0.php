<?php
/**
*
* @package phpBB Extension - JFusion phpBB Extension
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\jfusion\migrations;

/**
 * Class release_1_0_0
 * @package ukrgb\jfusion\migrations
 */
class release_1_0_0 extends \phpbb\db\migration\migration
{
	/**
	 * Allows you to check if the migration is effectively installed (entirely optional)
	 * @return bool True if this migration is installed, False if this migration is not installed (checked on install)
	 */
	public function effectively_installed()
	{
		return isset($this->config['ukrgb_jfusion_apipath']);
	}


	/**
	 * Updates data by returning a list of instructions to be executed
	 *
	 * @return array Array of data update instructions
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('ukrgb_jfusion_apipath', '')),
			array('config.add', array('ukrgb_jfusion_jname', '')),
				
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_UKRGB_JFUSION_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_UKRGB_JFUSION_TITLE',
				array(
					'module_basename'	=> '\ukrgb\jfusion\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
