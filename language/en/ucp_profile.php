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
	'STEAMSTATUS_TITLE'	=> 'Steam status',

	'STEAMSTATUS_STEAMID'			=> 'SteamID',
	'STEAMSTATUS_STEAMID_EXPLAIN'	=> 'Enter your SteamID, SteamID3, SteamID64, or Steam Community profile URL to enable your Steam status to be displayed on your profile.',

	'STEAMSTATUS_ERROR_INVALID_FORMAT'	=> 'The field “SteamID” is not in a recognized format.',
	'STEAMSTATUS_ERROR_NAME_NOT_FOUND'	=> 'The name entered into the field “SteamID” was not found.',
	'STEAMSTATUS_ERROR_LOOKUP_FAILED'	=> 'We were unable to look up your Steam ID based on your vanity URL. You can use a format other than your vanity URL or try again later.',
));
