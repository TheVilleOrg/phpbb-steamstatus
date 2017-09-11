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
	'STEAMSTATUS_STEAM'		=> 'Steam',

	'STEAMSTATUS_PROFILE'	=> 'Perfil',
	'STEAMSTATUS_ADD'		=> 'Añadir',

	'STEAMSTATUS_AVATAR_ALT'	=> 'Avatar de %s',
	'STEAMSTATUS_PROFILE_LINK'	=> 'Ver perfil Steam de %s',
	'STEAMSTATUS_ADD_LINK'		=> 'Añadir a %s en amigos de Steam',

	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Desconectado',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'Conectado',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Ocupado',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Lejos',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Dormir',
	'STEAMSTATUS_STATUS_LTT'		=> 'Buscando negociar',
	'STEAMSTATUS_STATUS_LTP'		=> 'Buscando jugar',

	'STEAMSTATUS_LOADING'			=> 'Cargando…',
));
