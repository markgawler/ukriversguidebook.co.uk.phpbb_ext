<?php
/**
*
* @package phpBB Extension - UKRGB Core phpBB Extension
* @copyright (c) 2017 Mark Gawler
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

	'ACP_UKRGB_FIX'					=> 'Settings',
	'APC_UKRGB_USER_NAME' 			=> 'User name to check email hash:',
	'ACP_UKRGB_FIX_TITLE'			=> 'UKRGB Fix email hash extension',
	'ACP_UKRGB_SETTING_SAVED'		=> 'Settings have been saved successfully!',
	'APC_UKRGB_FIX_CALC'			=> 'Calculated Hash',
	'APC_UKRGB_FIX_DB'				=> 'Database Hash',
	'APC_UKRGB_FIX_CAPTION'			=> 'Email Hash Check',
	'APC_UKRGB_FIX_MATCH'			=> 'Hash Match,',
	'APC_UKRGB_CHECK_ALL'			=> 'Check all email hash',
	'APC_UKRGB_FIX_ALL'				=> 'Fix all email hashes',
	'APC_UKRGB_FIX_COUNT_MSG'		=> 'Mismatch count',
	'APC_UKRGB_FIX_MSG'				=> 'Hashes fixed',
	'APC_UKRGB_FIX_ONE'				=> 'Fix Hash',
	'APC_UKRGB_FIX_FIX'				=> 'Fix',
));