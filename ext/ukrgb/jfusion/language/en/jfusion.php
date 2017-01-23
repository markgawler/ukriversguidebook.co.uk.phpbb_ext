<?php
/**
*
* @package phpBB Extension - JFusion phpBB Extension
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_UKRGB_JFUSION_TITLE'			=> 'UKRGB JFusion Extension',
	'ACP_UKRGB_JFUSION'					=> 'Settings',
	'ACP_UKRGB_JFUSION_JNAME'			=> 'Instance Name',
	'ACP_UKRGB_JFUSION_APIPATH'			=> 'Api Path',
	'ACP_UKRGB_JFUSION_SETTING_SAVED'	=> 'Settings have been saved successfully!',
		
));