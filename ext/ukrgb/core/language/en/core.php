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
		
	'ACP_UKRGB_CORE_TITLE'			=> 'UKRGB Core Extension',
	'ACP_UKRGB_FB_APP_TITLE'		=> 'Facebook App Settings',
    'ACP_UKRGB_IMAGE_TITLE'         => 'Image Settings',

	'ACP_UKRGB_CORE_CFG'			=> 'Core Settings',
	'ACP_UKRGB_FB_CFG'				=> 'Facebook App',
    'ACP_UKRGB_IMAGE_CFG'           => 'Image Settings',

	'ACP_UKRGB_JFUSION_JNAME'		=> 'JFusion Instance Name',
	'ACP_UKRGB_JFUSION_APIPATH'		=> 'JFusion Api Path',
	'ACP_UKRGB_CORE_SETTING_SAVED'	=> 'Settings have been saved successfully!',
	'ACP_UKRGB_SECRET'				=> 'UKRGB Secret',
	'ACP_UKRGB_FB_APPID'			=> 'Facebook App ID',
	'ACP_UKRGB_FB_SECRET'			=> 'Facebook App Secret',
	'ACP_UKRGB_FB_PAGE_MGR'			=> 'Facebook Page Manager',
	'ACP_UKRGB_FB_PAGE_ID'			=> 'Facebook Page ID',	
	'APC_UKRGB_GET_TOKEN'			=> 'Get Facebook Token',
	'APC_UKRGB_AUTO_POST'			=> 'Automatically post new topics',
	'ACP_UKRGB_FB_SUBFORUMS'		=> 'Subforums to Auto Post',
	'ACP_UKRGB_CRON_FREQUENCY'		=> 'Cron Frequency (s)',
	
	'ACP_PAGE_TOKEN'				=> 'Page Token',
	'APC_UKRGB_FIELD'				=> 'Token Field',
	'APC_UKRGB_VALUE'				=> 'Value',
	'ACP_UKRGB_FBPT_APP'			=> 'Application',
	'ACP_UKRGB_FBPT_EXPIRES'		=> 'Expires',
	'ACP_UKRGB_FBPT_VALID'			=> 'Valid',
	'ACP_UKRGB_FBPT_ISSUED'			=> 'Issued',
	'ACP_UKRGB_FBPT_SCOPE'			=> 'Scope',
	'ACP_UKRGB_FBPT_ERROR'			=> 'Error',
	
	'ACP_UKRGB_FB_PAGE_TOKEN_UPD'	=> 'Page Token Updated',
	'APC_UKRGB_BAD_REQUEST'			=> 'Bad Request',
	'APC_NOT_PAGE_MGR'				=> 'Unauthorised, you are not in the Page Managers group',
	'ACP_UKRGB_INFO'				=> 'Information',
	'ACP_UKRGB_UNAUTH'				=> 'Unauthorised',
	
		
	'LOG_UKRGB_OAUTH_REG'			=> '<strong>User Registered using "%1$s"</strong><br />» Name "%2$s", Email: “%3$s”, Provider Id: “%4$s”',
	'LOG_UKRGB_OAUTH_LINK'			=> '<strong>User account Linked to "%1$s"</strong><br />» Name "%2$s", Email: “%3$s”, Provider Id: “%4$s”',

	'ACP_UKRGB_ENABLE_BETA'			=> 'Enable Beta Test',
	'ACP_UKRGB_BETA_GROUP'			=> 'Beta test Group ID',

    'ACP_UKRGB_IMAGE_AWS_REGION'    => 'Region',
    'ACP_UKRGB_IMAGE_AWS_KEY'		=> 'AWS Access Key',
    'ACP_UKRGB_IMAGE_AWS_SECRET'    => 'AWS Secret',
    'ACP_UKRGB_IMAGE_SES_QUEUE'     => 'AWS SQS Queue',
    'ACP_UKRGB_IMAGE_SQS_READ'      => 'Enable processing of AWS SQS queue'
));