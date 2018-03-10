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

	'ACP_UKRGB_BBCODE'					=> 'BBCode Fix',
	'ACP_UKRGB_BBCODE_TITLE'			=> 'UKRGB BBCode fix extension',
    'APC_UKRGB_POST_ID'                 => 'Post ID',
    'APC_UKRGB_BBCODE_ALL'              => 'Fix all Youtube and Vimeo BBCodes',
    'APC_UKRGB_BBCODE_UPD_COUNT_MSG'    => 'Number of posts updated'
));