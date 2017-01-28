<?php
/**
*
* @package phpBB Extension - UKRGB phpBB Extension
* @copyright (c) 2017 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\core\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ukrgb\core\acp\main_module',
			'title'		=> 'ACP_UKRGB_CORE_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'	=> array('title' => 'ACP_UKRGB_CORE',
					'auth' => 'ext_ukrgb/core && acl_a_board',
					'cat' => array('ACP_UKRGB_CORE_TITLE')),
			),
		);
	}
}
