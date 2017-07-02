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
		return isset($this->config['ukrgb_jdbuser']);
	}

	public function update_data()
	{
		error_log('release_1_0_1');
		return array(
			array('module.remove', array(
				'acp',
				'ACP_UKRGB_TPL_TITLE')
			),

			array('module.add', array(
				'acp',
				'ACP_UKRGB_TPL_TITLE',
				array(
						'module_basename'	=> '\ukrgb\template\acp\main_module',
						'modes'				=> array('settings','page_banners'),
				),
			)),

		);
	}
}