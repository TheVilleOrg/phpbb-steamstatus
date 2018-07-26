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
	'ACP_STEAMSTATUS_TITLE'	=> 'Steam Status',

	'ACP_STEAMSTATUS_SETTINGS'	=> 'Configurações',

	'ACP_STEAMSTATUS_API_SETTINGS'		=> 'Steam Web API',
	'ACP_STEAMSTATUS_DISPLAY_SETTINGS'	=> 'Opções de exibição',

	'ACP_STEAMSTATUS_API_KEY'					=> 'Steam Web API Key',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'			=> 'A Steam Web API requer o uso de uma chave API. Você pode obter sua chave em <a href="https://steamcommunity.com/dev/apikey" target="_blank">https://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_CACHE_TIME'				=> 'Tempo do cache do perfil do Steam',
	'ACP_STEAMSTATUS_CACHE_TIME_EXPLAIN'		=> 'O tempo em segundos para armazenar um perfil do Steam antes de consultar a API. Aumente esse valor em sites de alto tráfego.',
	'ACP_STEAMSTATUS_REFRESH_TIME'				=> 'Intervalo de atualização do perfil da Steam',
	'ACP_STEAMSTATUS_REFRESH_TIME_EXPLAIN'		=> 'Com que frequência em minutos para atualizar automaticamente perfis da Steam em uma página. Defina para 0 para desativar a atualização automática.',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE'			=> 'Exibir nos perfis',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE_EXPLAIN'	=> 'Ative para ter os status do steam atual dos usuários exibidos em suas páginas de perfil.',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC'			=> 'Exibir nos posts',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC_EXPLAIN'	=> 'Ative para ter os status do steam atual dos usuários exibidos na seção de informações do usuário de cada post.',

	'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT'				=> 'A chave Steam Web API está em um formato inválido.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED'	=> 'Ocorreu um erro ao tentar verificar a chave Steam Web API. Talvez a Steam Web API esteja indisponível no momento.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID'				=> 'A chave Steam Web API é inválida.',

	'ACP_STEAMSTATUS_WARN_KEY_REQUIRED'	=> 'Você deve fornecer uma chave válida no campo "Steam Web API Key" para que esta extensão funcione.',

	'ACP_STEAMSTATUS_SETTINGS_SAVED'	=> 'As configurações foram salvas com sucesso',
));
