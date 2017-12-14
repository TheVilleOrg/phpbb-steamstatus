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
	'STEAMSTATUS_TITLE'	=> 'Status do Steam',

	'STEAMSTATUS_STEAMID'			=> 'SteamID',
	'STEAMSTATUS_STEAMID_EXPLAIN'	=> 'Digite seu SteamID, SteamID3, SteamID64 ou Steam Community Profile URL para permitir que o status do Steam seja exibido em seu perfil.',

	'STEAMSTATUS_ERROR_INVALID_FORMAT'	=> 'O campo "SteamID" não está em um formato reconhecido.',
	'STEAMSTATUS_ERROR_NAME_NOT_FOUND'	=> 'O nome inserido no campo "SteamID" não foi encontrado.',
	'STEAMSTATUS_ERROR_LOOKUP_FAILED'	=> 'Não foi possível procurar o seu ID de Steam com base no seu URL de vaidade. Você pode usar um formato diferente do seu URL de vaidade ou tentar novamente mais tarde.',
));
