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
	'ACP_STEAMSTATUS_TITLE'								=> 'Steam Status Module',
	'ACP_STEAMSTATUS_SETTINGS'							=> 'Settings',
	'ACP_STEAMSTATUS_API_KEY'							=> 'Steam Web API Key',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'					=> 'The Steam Web API requires the use of an API key. You can obtain your key at <a href="http://steamcommunity.com/dev/apikey" target="_blank">http://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_CACHE_TIME'						=> 'Steam profile cache time',
	'ACP_STEAMSTATUS_CACHE_TIME_EXPLAIN'				=> 'The time in seconds to store a Steam profile before querying the API. Increase this value on high traffic sites.',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE'					=> 'Display on profiles',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE_EXPLAIN'			=> 'Enable to have users’ current Steam status displayed on their profile pages.',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC'					=> 'Display on posts',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC_EXPLAIN'			=> 'Enable to have users’ current Steam status displayed in the user info section of each post.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT'				=> 'The Steam Web API Key is in an invalid format.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED'	=> 'There was an error while attempting to verify the Steam Web API Key. Perhaps the Steam Web API is currently unavailable.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID'				=> 'The Steam Web API Key is invalid.',
	'ACP_STEAMSTATUS_SETTINGS_SAVED'					=> 'Settings have been saved successfully!',
));
