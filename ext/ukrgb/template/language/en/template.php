<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
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
    	'UKRGB_YEAR'                    => date('Y'),
		'UKRGB_BACKTOTOP' 			    => 'Back to Top',
		'UKRGB_SIGNIN'					=> 'Sign In',
		'UKRGB_REGISTER'				=> 'Create an account',
		'UKRGB_FORGOT_UNAME'			=> 'Forgot Username?',
		'UKRGB_FORGOT_PASS'				=> 'Forgot Password?',
		
		'ACP_UKRGB_TPL_TITLE'			=> 'UKRGB Template Module',
		'ACP_UKRGB_TPL'					=> 'Settings',
		'ACP_UKRGB_SETTING_SAVED'		=> 'Settings have been saved successfully!',
		'APC_UKRGB_JDB' 			    => 'Joomla Database Name',
		'APC_UKRGB_JDBUSER' 			=> 'Joomla Database User name',
		'APC_UKRGB_JDBPWD' 				=> 'Joomla Database Password',
		'APC_UKRGB_JDBHOST' 			=> 'Joomla Database Host',
		//'ACP_UKRGB_JDB_SETUP' 			=> 'Joomla Database Settings',
		
));