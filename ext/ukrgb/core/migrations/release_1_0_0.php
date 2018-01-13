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
 * Class release_1_0_0
 * @package ukrgb\core\migrations
 */
class release_1_0_0 extends \phpbb\db\migration\migration
{
	/**
	 * Allows you to check if the migration is effectively installed (entirely optional)
	 * @return bool True if this migration is installed, False if this migration is not installed (checked on install)
	 */
	public function effectively_installed()
	{
        return isset($this->config['ukrgb_secret']);
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
				array('config.add', array('ukrgb_secret', '')),
				array('config.add', array('ukrgb_fb_appid', '')),
				array('config.add', array('ukrgb_fb_secret', '')),
				array('config.add', array('ukrgb_fb_page_mgr', '')),
				
				array('module.add', array(
						'acp',
						'ACP_CAT_DOT_MODS',
						'ACP_UKRGB_CORE_TITLE'
				)),
				
				array('module.add', array(
					'acp',
					'ACP_UKRGB_CORE_TITLE',
					array(
							'module_basename'	=> '\ukrgb\core\acp\main_module',
							'modes'				=> array('settings','fb_app_settings'),
					),
				)),
				
		);
	}
}
