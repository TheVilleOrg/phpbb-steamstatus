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
	'STEAMSTATUS_STEAM'				=> 'Steam',
	'STEAMSTATUS_AVATAR_ALT'		=> 'Avatar de %s',
	'STEAMSTATUS_PROFILE'			=> 'Perfil',
	'STEAMSTATUS_ADD'				=> 'Añadir',
	'STEAMSTATUS_PROFILE_LINK'		=> 'Ver perfil Steam de %s',
	'STEAMSTATUS_ADD_LINK'			=> 'Añadir a %s en amigos de Steam',
	'STEAMSTATUS_STATUS_OFFLINE'	=> 'Desconectado',
	'STEAMSTATUS_STATUS_ONLINE'		=> 'Conectado',
	'STEAMSTATUS_STATUS_BUSY'		=> 'Ocupado',
	'STEAMSTATUS_STATUS_AWAY'		=> 'Lejos',
	'STEAMSTATUS_STATUS_SNOOZE'		=> 'Dormir',
	'STEAMSTATUS_STATUS_LTT'		=> 'Buscando negociar',
	'STEAMSTATUS_STATUS_LTP'		=> 'Buscando jugar',
	'STEAMSTATUS_LOADING'			=> 'Cargando…',
));
