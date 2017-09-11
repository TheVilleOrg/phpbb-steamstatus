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
	'ACP_STEAMSTATUS_TITLE'		=> 'Módulo de Estado Steam',

	'ACP_STEAMSTATUS_SETTINGS'	=> 'Ajustes',

	'ACP_STEAMSTATUS_API_SETTINGS'		=> 'Steam Web API',
	'ACP_STEAMSTATUS_DISPLAY_SETTINGS'	=> 'Display options',

	'ACP_STEAMSTATUS_API_KEY'					=> 'Clave de la API Web de Steam',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'			=> 'La clave de la API Web de Steam requiere el uso de una clave API. Puede obtener su clave en <a href="http://steamcommunity.com/dev/apikey" target="_blank">http://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_CACHE_TIME'				=> 'Tiempo de caché del perfil de Steam',
	'ACP_STEAMSTATUS_CACHE_TIME_EXPLAIN'		=> 'El tiempo en segundos para almacenar un perfil de Steam antes de consultar la API. Aumente este valor en sitios de alto tráfico.',
	'ACP_STEAMSTATUS_REFRESH_TIME'				=> 'Intervalo de actualización del perfil de Steam',
	'ACP_STEAMSTATUS_REFRESH_TIME_EXPLAIN'		=> 'Con qué frecuencia en minutos se actualizará automáticamente los perfiles de Steam en una página. Establezca 0 para deshabilitar la actualización automática.',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE'			=> 'Mostrar en perfiles',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE_EXPLAIN'	=> 'Habilitar para que se muestre el estado actual de Steam de los usuarios en sus páginas de perfil.',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC'			=> 'Mostrar en mensajes',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC_EXPLAIN'	=> 'Habilitar para que se muestre el estado actual de Steam en la sección de información del usuario en cada mensaje.',
	'ACP_STEAMSTATUS_REG_FIELD'					=> 'Mostrar campo durante el registro',
	'ACP_STEAMSTATUS_REG_FIELD_EXPLAIN'			=> 'Habilitar para mostrar el campo de SteamID en el formulario de registro.',

	'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT'				=> 'La clave de la API Web de Steam está en un formato no válido.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED'	=> 'Se ha producido un error al intentar verificar la Clave de API de Steam Web. Tal vez la Web API de Steam no esté disponible actualmente.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID'				=> 'La clave de la API Web de Steam no es válida.',

	'ACP_STEAMSTATUS_WARN_KEY_REQUIRED'	=> 'Debe proporcionar una clave válida en el campo “Clave de la API Web de Steam” para que esta extensión funcione.',

	'ACP_STEAMSTATUS_SETTINGS_SAVED'					=> '¡Ajustes guardados correctamente!',
));
