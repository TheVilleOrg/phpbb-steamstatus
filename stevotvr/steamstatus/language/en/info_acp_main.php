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
	'ACP_STEAMSTATUS_TITLE'				=> 'Steam Status Module',
	'ACP_STEAMSTATUS_SETTINGS'			=> 'Settings',
	'ACP_STEAMSTATUS_API_KEY'			=> 'Steam Web API Key',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'	=> 'The Steam Web API requires the use of an API key. You can obtain your key at <a href="http://steamcommunity.com/dev/apikey" target="_blank">http://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_API_KEY_ERROR_FORMAT'	=> 'The Steam Web API Key is in an invalid format.',
	'ACP_STEAMSTATUS_API_KEY_VALIDATION_FAILED'	=> 'There was an error while attempting to verify the Steam Web API Key. Perhaps the Steam Web API is currently unavailable.',
	'ACP_STEAMSTATUS_API_KEY_INVALID'	=> 'The Steam Web API Key is invalid.',
	'ACP_STEAMSTATUS_SETTINGS_SAVED'	=> 'Settings have been saved successfully!',
));
