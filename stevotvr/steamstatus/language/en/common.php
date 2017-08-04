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
	'PROFILE'			=> 'Profile',
	'ADD'				=> 'Add',
	'STATUS_OFFLINE'	=> 'Offline',
	'STATUS_ONLINE'		=> 'Online',
	'STATUS_BUSY'		=> 'Busy',
	'STATUS_AWAY'		=> 'Away',
	'STATUS_SNOOZE'		=> 'Snooze',
	'STATUS_LTT'		=> 'Looking to trade',
	'STATUS_LTP'		=> 'Looking to play',
));
