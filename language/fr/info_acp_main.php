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
	'ACP_STEAMSTATUS_TITLE'	=> 'État Steam',

	'ACP_STEAMSTATUS_SETTINGS'	=> 'Paramètres',

	'ACP_STEAMSTATUS_API_SETTINGS'		=> 'API Steam Web',
	'ACP_STEAMSTATUS_DISPLAY_SETTINGS'	=> 'Options d’affichage',

	'ACP_STEAMSTATUS_API_KEY'					=> 'Clé API Steam Web',
	'ACP_STEAMSTATUS_API_KEY_EXPLAIN'			=> 'L’API Steam Web nécessite l’utilisation d’une clé API. Vous pouvez obtenir votre clé sur <a href="https://steamcommunity.com/dev/apikey" target="_blank">https://steamcommunity.com/dev/apikey</a>.',
	'ACP_STEAMSTATUS_CACHE_TIME'				=> 'Temps de cache du profil Steam',
	'ACP_STEAMSTATUS_CACHE_TIME_EXPLAIN'		=> 'Le temps en secondes pour stocker un profil Steam avant d’interroger l’API. Augmenter cette valeur sur les sites à fort trafic.',
	'ACP_STEAMSTATUS_REFRESH_TIME'				=> 'Intervalle d’actualisation du profil Steam',
	'ACP_STEAMSTATUS_REFRESH_TIME_EXPLAIN'		=> 'Combien de fois en minutes pour actualiser automatiquement les profils Steam sur une page. Régler sur 0 pour désactiver l’actualisation automatique.',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE'			=> 'Afficher sur les profils',
	'ACP_STEAMSTATUS_SHOW_ON_PROFILE_EXPLAIN'	=> 'Activer pour afficher l’état Steam des utilisateurs sur leur page de profil.',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC'			=> 'Afficher sur les messages',
	'ACP_STEAMSTATUS_SHOW_ON_VIEWTOPIC_EXPLAIN'	=> 'Activer pour afficher l’état Steam des utilisateurs dans la section d’informations utilisateur de chaque message.',
	'ACP_STEAMSTATUS_REG_FIELD'					=> 'Afficher le champ d’enregistrement',
	'ACP_STEAMSTATUS_REG_FIELD_EXPLAIN'			=> 'Activer pour afficher le champ SteamID sur le formulaire d’inscription de l’utilisateur.',

	'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT'				=> 'L’API Steam Web est dans un format invalide.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED'	=> 'Une erreur s’est produite lors de la tentative de vérification de la clé API Steam Web. Peut-être que l’API Steam Web est actuellement indisponible.',
	'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID'				=> 'La clé API Steam Web est invalide.',

	'ACP_STEAMSTATUS_WARN_KEY_REQUIRED'	=> 'Vous devez fournir une clé valide dans le champ “Clé API Steam Web” pour que l’extension fonctionne.',

	'ACP_STEAMSTATUS_SETTINGS_SAVED'	=> 'Les paramètres ont été sauvegardés avec succès',
));
