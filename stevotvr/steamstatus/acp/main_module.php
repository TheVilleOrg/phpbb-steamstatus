<?php

namespace stevotvr\steamstatus\acp;

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container, $template, $request, $config;
		$language = $phpbb_container->get('language');

		$this->tpl_name = 'acp_steamstatus_body';
		$this->page_title = 'ACP_STEAMSTATUS_TITLE';

		add_form_key('stevotvr_steamstatus_settings');

		$error = array();

		if($request->is_set_post('submit'))
		{
			if(!check_form_key('stevotvr_steamstatus_settings'))
			{
				trigger_error('FORM_INVALID');
			}

			$key = trim($request->variable('stevotvr_steamstatus_key', ''));
			if($this->validate_key($key, $error))
			{
				$config->set('stevotvr_steamstatus_key', $key);

				trigger_error($language->lang('ACP_STEAMSTATUS_SETTINGS_SAVED') . adm_back_link($this->u_action));
			}
		}

		$error = array_map(array($language, 'lang'), $error);
		$template->assign_vars(array(
			'ERROR_MSG'					=> implode('<br />', $error),
			'STEVOTVR_STEAMSTATUS_KEY'	=> $config['stevotvr_steamstatus_key'],
			'U_ACTION'					=> $this->u_action,
			'S_ERROR'					=> sizeof($error) > 0,
		));
	}

	private function validate_key($key, &$error)
	{
		if(!preg_match('/^[A-Z\d]+$/', $key))
		{
			$error[] = 'ACP_STEAMSTATUS_API_KEY_ERROR_FORMAT';
			return false;
		}

		// TODO: Check HTTP response code
		$url = sprintf('http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?key=%s', $key);
		$result = file_get_contents($url);
		if(!$result)
		{
			$error[] = 'ACP_STEAMSTATUS_API_KEY_VALIDATION_FAILED';
			return false;
		}

		$result = json_decode($result);
		if(!$result || !$result->apilist || !$result->apilist->interfaces)
		{
			$error[] = 'ACP_STEAMSTATUS_API_KEY_VALIDATION_FAILED';
			return false;
		}

		foreach($result->apilist->interfaces as $interface)
		{
			if($interface->name === 'ISteamUser')
			{
				foreach($interface->methods as $method)
				{
					if($method->name === 'GetPlayerSummaries' && $method->version === 2)
					{
						return true;
					}
				}
			}
		}

		$error[] = 'ACP_STEAMSTATUS_API_KEY_INVALID';
		return false;
	}
}
