<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'STEAM_TITLE'			=> 'Steam',
	'STEAM_ID'				=> 'SteamID',
	'STEAM_ID_EXPLAIN'		=> 'Enter your SteamID, SteamID3, SteamID64, or Steam Community profile URL to enable your Steam status to be displayed on your profile.',
	'ERROR_INVALID_FORMAT'	=> 'The field “SteamID” is not in a recognized format.',
	'ERROR_NAME_NOT_FOUND'	=> 'The name entered into the field “SteamID” was not found.',
	'ERROR_LOOKUP_FAILED'	=> 'We were unable to look up your Steam ID based on your vanity URL. You can use a format other than your vanity URL or try again later.',
	'PREVIEW'				=> 'Preview',
	'PROFILE'				=> 'Profile',
	'ADD'					=> 'Add',
	'STATUS_OFFLINE'		=> 'Offline',
	'STATUS_ONLINE'			=> 'Online',
	'STATUS_BUSY'			=> 'Busy',
	'STATUS_AWAY'			=> 'Away',
	'STATUS_SNOOZE'			=> 'Snooze',
	'STATUS_LTT'			=> 'Looking to trade',
	'STATUS_LTP'			=> 'Looking to play',
));
