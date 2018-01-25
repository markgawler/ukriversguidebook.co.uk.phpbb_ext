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

	'ACP_UKRGB_ENABLE_BETA'			=> 'Enable Beta Test',
	'ACP_UKRGB_BETA_GROUP'			=> 'Beta test Group ID',

    'ACP_UKRGB_IMAGE_AWS_REGION'    => 'AWS Region',
    'ACP_UKRGB_IMAGE_AWS_KEY'		=> 'AWS Access Key',
    'ACP_UKRGB_IMAGE_AWS_SECRET'    => 'AWS Secret',
    'ACP_UKRGB_IMAGE_SES_QUEUE'     => 'AWS SQS Queue',
    'ACP_UKRGB_IMAGE_S3_BUCKET'     => 'AWS S3 Bucket',
    'ACP_UKRGB_IMAGE_S3_PREFIX'     => 'AWS S3 Bucket prefix',
    'ACP_UKRGB_IMAGE_SQS_READ'      => 'Enable processing of AWS SQS queue',
    'ACP_UKRGB_CRON_FREQ_CLEANUP'   => 'AWS SQS Cron Frequency (s)',

    'APC_UKRGB_ORPHAN_IMAGE_MSG'    => 'Find Orphan Images (images from deleted posts, failed uploads, etc.)',
    'APC_UKRGB_ORPHAN_IMAGE'        => 'Orphan Images found:',
    'APC_UKRGB_ORPHAN_INVALID_IMAGE' => 'Uploads with Invalid file keys found:',

));