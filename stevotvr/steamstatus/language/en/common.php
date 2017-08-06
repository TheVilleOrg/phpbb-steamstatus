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
	'STEAMSTATUS_PROFILE'			=> 'Profile',
	'STEAMSTATUS_ADD'				=> 'Add',
	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Offline',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'Online',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Busy',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Away',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Snooze',
	'STEAMSTATUS_STATUS_LTT'		=> 'Looking to trade',
	'STEAMSTATUS_STATUS_LTP'		=> 'Looking to play',
));
