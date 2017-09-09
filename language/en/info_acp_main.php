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
	'ACP_STEAMSTATUS_TITLE'	=> 'Steam Status',

	'ACP_STEAMSTATUS_SETTINGS'	=> 'Settings',

	'ACP_STEAMSTATUS_API_SETTINGS'		=> 'Steam Web API',
	'ACP_STEAMSTATUS_DISPLAY_SETTINGS'	=> 'Display options',

	'ACP_STEAMSTATUS_API_KEY'					=> 'Steam Web API Key',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'			=> 'The Steam Web API requires the use of an API key. You can obtain your key at <a href="http://steamcommunity.com/dev/apikey" target="_blank">http://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_CACHE_TIME'				=> 'Steam profile cache time',
	'ACP_STEAMSTATUS_CACHE_TIME_EXPLAIN'		=> 'The time in seconds to store a Steam profile before querying the API. Increase this value on high traffic sites.',
	'ACP_STEAMSTATUS_REFRESH_TIME'				=> 'Steam profile refresh interval',
	'ACP_STEAMSTATUS_REFRESH_TIME_EXPLAIN'		=> 'How often in minutes to automatically refresh Steam profiles on a page. Set to 0 to disable auto-refresh.',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE'			=> 'Display on profiles',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE_EXPLAIN'	=> 'Enable to have users’ current Steam status displayed on their profile pages.',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC'			=> 'Display on posts',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC_EXPLAIN'	=> 'Enable to have users’ current Steam status displayed in the user info section of each post.',
	'ACP_STEAMSTATUS_REG_FIELD'					=> 'Show registration field',
	'ACP_STEAMSTATUS_REG_FIELD_EXPLAIN'			=> 'Enable to show the SteamID field in the user registration form.',

	'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT'				=> 'The Steam Web API Key is in an invalid format.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED'	=> 'There was an error while attempting to verify the Steam Web API Key. Perhaps the Steam Web API is currently unavailable.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID'				=> 'The Steam Web API Key is invalid.',

	'ACP_STEAMSTATUS_WARN_KEY_REQUIRED'	=> 'You must provide a valid key in the “Steam Web API Key” field for this extension to work.',

	'ACP_STEAMSTATUS_SETTINGS_SAVED'	=> 'Settings have been saved successfully',
));
