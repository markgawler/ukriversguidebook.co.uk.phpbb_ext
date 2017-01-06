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


if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
		'REG_COMPLETE_TITLE' => 'Registration Complete',
		'REG_COMPLETE_TEXT' => 'You have successfully registered on the UK Rivers Guidebook. You will automatically be logged in in 5 seconds.',
		'REG_COMPLETE_LNK_TXT' => 'Sign In to the UK Rivers Guidebook',
		));