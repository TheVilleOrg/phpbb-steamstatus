<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\acp;

/**
 * Steam Status ACP module.
 */
class main_module
{
	public $u_action;

	public $tpl_name;

	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$config = $phpbb_container->get('config');
		$db = $phpbb_container->get('dbal.conn');
		$language = $phpbb_container->get('language');
		$request = $phpbb_container->get('request');
		$template = $phpbb_container->get('template');

		$this->tpl_name = 'acp_steamstatus_body';
		$this->page_title = 'ACP_STEAMSTATUS_TITLE';

		add_form_key('stevotvr_steamstatus_settings');

		$error = array();

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('stevotvr_steamstatus_settings'))
			{
				trigger_error('FORM_INVALID');
			}

			$api_key = $request->variable('steamstatus_api_key', '');
			if ($api_key !== $config['stevotvr_steamstatus_api_key'] && self::validate_key($api_key, $error))
			{
				$config->set('stevotvr_steamstatus_api_key', $api_key);
			}

			$show_on_profile = $request->variable('steamstatus_show_on_profile', '');
			if (strlen($show_on_profile)) {
				$config->set('stevotvr_steamstatus_show_on_profile', $show_on_profile ? 1 : 0);
			}

			$show_on_viewtopic = $request->variable('steamstatus_show_on_viewtopic', '');
			if (strlen($show_on_viewtopic)) {
				$config->set('stevotvr_steamstatus_show_on_viewtopic', $show_on_viewtopic ? 1 : 0);
			}

			if (!count($error)) {
				trigger_error($language->lang('ACP_STEAMSTATUS_SETTINGS_SAVED') . adm_back_link($this->u_action));
			}
		}

		$error = array_map(array($language, 'lang'), $error);
		$template->assign_vars(array(
			'STEAMSTATUS_API_KEY'			=> $config['stevotvr_steamstatus_api_key'],
			'STEAMSTATUS_SHOW_ON_PROFILE'	=> $config['stevotvr_steamstatus_show_on_profile'],
			'STEAMSTATUS_SHOW_ON_VIEWTOPIC'	=> $config['stevotvr_steamstatus_show_on_viewtopic'],
			'ERROR_MSG'						=> implode('<br />', $error),
			'U_ACTION'						=> $this->u_action,
			'S_ERROR'						=> count($error) > 0,
		));
	}

	/**
	 * Validate a given Steam Web API key. This method checks for proper format and then calls the
	 * Steam Web API to verify that the key grants access to the methods used by the extension.
	 *
	 * @param string $api_key	The API key to validate
	 * @param string &$error	A variable to hold the error message (if any)
	 *
	 * @return bool				The key is valid
	 */
	static private function validate_key($api_key, &$error)
	{
		if (!strlen($api_key))
		{
			return true;
		}

		if (!preg_match('/^[A-Z\d]+$/', $api_key))
		{
			$error[] = 'ACP_STEAMSTATUS_ERROR_API_KEY_FORMAT';
			return false;
		}

		$query = http_build_query(array(
			'key'	=> $api_key,
		));
		$url = 'http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?' . $query;
		$ctx = stream_context_create(array(
			'http'	=> array(
				'ignore_errors'	=> '1',
			),
		));
		$stream = fopen($url, 'r', false, $ctx);
		if (!$stream)
		{
			$error[] = 'ACP_STEAMSTATUS_API_KEY_VALIDATION_FAILED';
			return false;
		}

		try
		{
			$meta = stream_get_meta_data($stream);
			$http_response = (int)substr($meta['wrapper_data'][0], strpos($meta['wrapper_data'][0], ' ') + 1, 3);
			if ($http_response === 403)
			{
				$error[] = 'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID';
				return false;
			}
			if ($http_response !== 200)
			{
				$error[] = 'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED';
				return false;
			}

			$result = json_decode(stream_get_contents($stream));
			if (!$result || !$result->apilist || !$result->apilist->interfaces)
			{
				$error[] = 'ACP_STEAMSTATUS_ERROR_API_KEY_VALIDATION_FAILED';
				return false;
			}

			foreach ($result->apilist->interfaces as $interface)
			{
				if ($interface->name === 'ISteamUser')
				{
					foreach ($interface->methods as $method)
					{
						if ($method->name === 'GetPlayerSummaries' && $method->version === 2)
						{
							return true;
						}
					}
				}
			}
		}
		finally
		{
			fclose($stream);
		}

		$error[] = 'ACP_STEAMSTATUS_ERROR_API_KEY_INVALID';
		return false;
	}
}
