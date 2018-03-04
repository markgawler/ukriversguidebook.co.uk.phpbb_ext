<?php
/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2013 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\bbcode\acp;
class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ukrgb\bbcode\acp\main_module',
			'title'		=> 'ACP_UKRGB_BBCODE_TITLE',
			'version'	=> '1.0.1',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_UKRGB_BBCODE',
					'auth'	=> 'ext_ukrgb/bbcode && acl_a_board',
					'cat'	=> array('ACP_UKRGB_BBCODE_TITLE')
				),
			),
		);
	}
}