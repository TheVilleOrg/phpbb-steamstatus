<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 * Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 1.1.3] (https://github.com/phpBBTraducoes)
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

	'STEAMSTATUS_PROFILE'	=> 'Perfil',
	'STEAMSTATUS_ADD'		=> 'Adicionar',

	'STEAMSTATUS_AVATAR_ALT'	=> 'Avatar de %s',
	'STEAMSTATUS_PROFILE_LINK'	=> 'Ver o perfil Steam de %s',
	'STEAMSTATUS_ADD_LINK'		=> 'Adicionar %s aos amigos da Steam',

	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Offline',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'Online',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Ocupado',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Ausente',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Soneca',
	'STEAMSTATUS_STATUS_LTT'		=> 'Procurando para barganhar',
	'STEAMSTATUS_STATUS_LTP'		=> 'Olhando para jogar',

	'STEAMSTATUS_LOADING'	=> 'Carregando…',
));
