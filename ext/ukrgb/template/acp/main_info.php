<?php
/**
 *
 * @package phpBB Extension - UKRGB Template
 * @copyright (c) 2013 Mark Gawler
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace ukrgb\template\acp;
class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ukrgb\template\acp\main_module',
			'title'		=> 'ACP_UKRGB_TPL_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_UKRGB_TPL',
					'auth'	=> 'ext_ukrgb/template && acl_a_board',
					'cat'	=> array('ACP_UKRGB_TPL_TITLE')
				),
				'page_banners' => array(
					'title' => 'APC_UKRGB_TPL_BANNER',
					'auth'	=> 'ext_ukrgb/template && acl_a_board',
					'cat'	=> array('ACP_UKRGB_TPL_BANNER_TITLE')
				),
			),
		);
	}
}