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

	'LINK_ACCOUNT' 					=> 'Link Account',
	'LINK_ACCOUNT_Q' 				=> 'Do you want to link your Facebook account to the following UK Rivers Guidebook account?',
		
	'REG_COMPLETE_TITLE' 			=> 'Registration Complete',
	'REG_COMPLETE_TEXT' 			=> 'You have successfully registered on the UK Rivers Guidebook. You will automatically be logged in in a few seconds.',
	'REG_COMPLETE_LNK_TXT' 			=> 'Sign In to the UK Rivers Guidebook',
	'REG_CANCEL' 					=> 'Registration Cancelled',
		
	'LNK_COMPLETE_TEXT' 			=> 'You have successfully link your account on the UK Rivers Guidebook. You will automatically be logged in a few seconds.',
	'LNK_CANCEL_TEXT' 				=> 'The linking of your your account has been cancelled.',
		
	'OAUTH_LNK_REG_FAIL' 			=> 'Account Registration / Linking Failed',
	'OAUTH_LNK_REG_FAIL_TXT' 		=> 'An error occurred that prevented account Registration / Linking, please contact the Administrator for assistance',
	'OAUTH_NO_EMAIL_TXT' 			=> 'No Email returned from Facebook, account cannot be linked or created. Please login to your UK Rivers Guidebook account and link your Facebook account from the User Control Panel.',
	'OAUTH_MULTI_EMAIL' 			=> 'Multiple user accounts area associated with your email, please contact the Administrator for assistance',
	'OAUTH_LNK_FAIL' 				=> 'Account Linking Failure',
	'OAUTH_LNK_ANOTHER_ACC' 		=> 'Your UK Rivers Guidebook Account is already linked to different Facebook account, please contact the Administrator for assistance.',

	'LOG_UKRGB_OAUTH_REG'			=> '<strong>User Registered using "%1$s"</strong><br />» Name "%2$s", Email: “%3$s”, Provider Id: “%4$s”',
	'LOG_UKRGB_OAUTH_LINK'			=> '<strong>User account Linked to "%1$s"</strong><br />» Name "%2$s", Email: “%3$s”, Provider Id: “%4$s”',

    'ACP_UKRGB_FB_PAGE_TOKEN_UPD'	=> 'Page Token Updated',


));