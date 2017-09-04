<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'STEAMSTATUS_STEAM'	=> 'Steam',

	'STEAMSTATUS_PROFILE'	=> 'Profile',
	'STEAMSTATUS_ADD'		=> 'Add',

	'STEAMSTATUS_AVATAR_ALT'	=> '%s’s avatar',
	'STEAMSTATUS_PROFILE_LINK'	=> 'View %s’s Steam profile',
	'STEAMSTATUS_ADD_LINK'		=> 'Add %s to Steam Friends',

	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Offline',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'Online',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Busy',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Away',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Snooze',
	'STEAMSTATUS_STATUS_LTT'		=> 'Looking to trade',
	'STEAMSTATUS_STATUS_LTP'		=> 'Looking to play',

	'STEAMSTATUS_LOADING'	=> 'Loading…',
));
