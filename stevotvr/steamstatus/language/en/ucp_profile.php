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
	'STEAMSTATUS_STEAMID'				=> 'SteamID',
	'STEAMSTATUS_STEAMID_EXPLAIN'		=> 'Enter your SteamID, SteamID3, SteamID64, or Steam Community profile URL to enable your Steam status to be displayed on your profile.',
	'STEAMSTATUS_ERROR_INVALID_FORMAT'	=> 'The field “SteamID” is not in a recognized format.',
	'STEAMSTATUS_ERROR_NAME_NOT_FOUND'	=> 'The name entered into the field “SteamID” was not found.',
	'STEAMSTATUS_ERROR_LOOKUP_FAILED'	=> 'We were unable to look up your Steam ID based on your vanity URL. You can use a format other than your vanity URL or try again later.',
));
