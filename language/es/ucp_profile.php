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
	'STEAMSTATUS_TITLE'					=> 'Estado de Steam',
	'STEAMSTATUS_STEAMID'				=> 'SteamID',
	'STEAMSTATUS_STEAMID_EXPLAIN'		=> 'Introduzca su SteamID, SteamID3, SteamID64, o la URL de su perfil en la Comunidad Steam para habilitar su estado Steam en su perfil.',
	'STEAMSTATUS_ERROR_INVALID_FORMAT'	=> 'El campo “SteamID” no está en un formato reconocido.',
	'STEAMSTATUS_ERROR_NAME_NOT_FOUND'	=> 'El nombre introducido en el campo “SteamID” no fue encontrado.',
	'STEAMSTATUS_ERROR_LOOKUP_FAILED'	=> 'No hemos podido buscar su Steam ID en función de su URL. Puede utilizar un formato distinto de URL o intentarlo de nuevo más tarde.',
));
