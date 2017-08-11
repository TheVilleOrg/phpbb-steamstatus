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

$lang = array_merge($lang, array(
	'STEAMSTATUS_STEAM'				=> 'Steam',
	'STEAMSTATUS_AVATAR_ALT'		=> '%s’s avatar',
	'STEAMSTATUS_PROFILE'			=> 'Profile',
	'STEAMSTATUS_ADD'				=> 'Add',
	'STEAMSTATUS_PROFILE_LINK'		=> 'View %s’s Steam profile',
	'STEAMSTATUS_ADD_LINK'			=> 'Add %s to Steam Friends',
	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Offline',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'Online',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Busy',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Away',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Snooze',
	'STEAMSTATUS_STATUS_LTT'		=> 'Looking to trade',
	'STEAMSTATUS_STATUS_LTP'		=> 'Looking to play',
	'STEAMSTATUS_LOADING'			=> 'Loading…',
));
