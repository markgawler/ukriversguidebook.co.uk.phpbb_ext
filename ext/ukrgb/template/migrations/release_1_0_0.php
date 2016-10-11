<?php

/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2016 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\template\migrations;
class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['ukrgb_jdbuser']);
	}

	public function update_data()
	{
		return array(
				array('config.add', array('ukrgb_jdb', '')),
				array('config.add', array('ukrgb_jdbuser', '')),
				array('config.add', array('ukrgb_jdbpwd', '')),
				array('config.add', array('ukrgb_jdbhost', 'localhost')),
				array('module.add', array(
						'acp',
						'ACP_CAT_DOT_MODS',
						'ACP_UKRGB_TPL_TITLE'
				)),
				array('module.add', array(
						'acp',
						'ACP_UKRGB_TPL_TITLE',
						array(
								'module_basename'	=> '\ukrgb\template\acp\main_module',
								'modes'				=> array('settings'),
						),
				)),
		);
	}
}