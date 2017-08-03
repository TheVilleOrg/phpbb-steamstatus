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
	'STEAM_ID'				=> 'SteamID',
	'ERROR_INVALID_FORMAT'	=> 'The field “SteamID” is not in a recognized format.',
));
