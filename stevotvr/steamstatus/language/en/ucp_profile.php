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
	'ERROR_NAME_NOT_FOUND'	=> 'The name entered into the field “SteamID” was not found.',
	'ERROR_LOOKUP_FAILED'	=> 'We were unable to look up your Steam ID based on your vanity URL. You can use a format other than your vanity URL or try again later.',
));
