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
	'STEAMSTATUS_STEAM'	=> 'Steam',

	'STEAMSTATUS_PROFILE'	=> 'Profil',
	'STEAMSTATUS_ADD'		=> 'Ajouter',

	'STEAMSTATUS_AVATAR_ALT'	=> '%s’s avatar',
	'STEAMSTATUS_PROFILE_LINK'	=> 'Afficher le profil Steam de %s',
	'STEAMSTATUS_ADD_LINK'		=> 'Ajouter %s à vos amis Steam',

	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Hors-ligne',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'En Ligne',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Occupé',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Absent',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Roupille',
	'STEAMSTATUS_STATUS_LTT'		=> 'Souhaite échanger',
	'STEAMSTATUS_STATUS_LTP'		=> 'Souhaite jouer',

	'STEAMSTATUS_LOADING'	=> 'Chargement…',
));
