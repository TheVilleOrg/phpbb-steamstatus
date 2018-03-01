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
	'ACP_STEAMSTATUS_TITLE'	=> 'Статус в Steam',

	'ACP_STEAMSTATUS_SETTINGS'	=> 'Настройки',

	'ACP_STEAMSTATUS_API_SETTINGS'		=> 'Steam Web API',
	'ACP_STEAMSTATUS_DISPLAY_SETTINGS'	=> 'Настройки отображения',

	'ACP_STEAMSTATUS_API_KEY'					=> 'Ключ Steam Web API',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'			=> 'The Steam Web API requires the use of an API key. You can obtain your key at <a href="https://steamcommunity.com/dev/apikey" target="_blank">https://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_CACHE_TIME'				=> 'Время кэширования профиля',
	'ACP_STEAMSTATUS_CACHE_TIME_EXPLAIN'		=> 'Сколько времени (в секундах) нужно хранить информацию о профиле в кэше. Увеличьте для снижения траффика на нагруженных сайтах.',
	'ACP_STEAMSTATUS_REFRESH_TIME'				=> 'Интервал перед обновлением статуса профиля',
	'ACP_STEAMSTATUS_REFRESH_TIME_EXPLAIN'		=> 'Как часто (в секундах) нужно обновлять (автоматически) профиль на странице. Для отключения авто-обновления введите в поле 0.',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE'			=> 'Показывать в профилях',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE_EXPLAIN'	=> 'Включите эту опцию что бы показывать текущий статус профиля Steam на страницах профилей пользователей.',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC'			=> 'Показывать в сообщениях',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC_EXPLAIN'	=> 'Включите эту опцию что бы показывать информацию профиля Steam возле каждого сообщения пользователя на форуме.',
	'ACP_STEAMSTATUS_REG_FIELD'					=> 'Показывать поле для ввода при регистрации пользователей',
	'ACP_STEAMSTATUS_REG_FIELD_EXPLAIN'			=> 'Включите эту опцию что бы показывать поле для ввода SteamID на странице регистрации пользователей.',

	'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT'				=> 'Введен ключ Steam Web API неверного формата.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED'	=> 'При попытке проверки ключа Steam Web API произошла ошибка. Возможно, Steam Web API сейчас недоступен.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID'				=> 'Введен неверный ключ Steam Web API.',

	'ACP_STEAMSTATUS_WARN_KEY_REQUIRED'	=> 'Для работы данного расширения необходимо ввести верный ключ в поле “Steam Web API”.',

	'ACP_STEAMSTATUS_SETTINGS_SAVED'	=> 'Настройки были успешно сохранены',
));
