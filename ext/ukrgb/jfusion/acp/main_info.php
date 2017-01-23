<?php
/**
*
* @package phpBB Extension - JFusion phpBB Extension
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ukrgb\jfusion\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ukrgb\jfusion\acp\main_module',
			'title'		=> 'ACP_UKRGB_JFUSION_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'	=> array('title' => 'ACP_UKRGB_JFUSION',
					'auth' => 'ext_ukrgb/jfusion && acl_a_board',
					'cat' => array('ACP_UKRGB_JFUSION_TITLE')),
			),
		);
	}
}
