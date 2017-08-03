<?php

namespace stevotvr\steamstatus\acp;

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container, $template, $request, $config, $db;
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
			if(self::validate_key($key, $error))
			{
				$config->set('stevotvr_steamstatus_key', $key);
				$sql_arr = array(
					'field_active'	=> strlen($key) ? 1 : 0,
				);
				$sql = 'UPDATE ' . PROFILE_FIELDS_TABLE . '
						SET ' . $db->sql_build_array('UPDATE', $sql_arr) . '
						WHERE field_ident = \'steam_id\'';
				$db->sql_query($sql);

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

	static private function validate_key($key, &$error)
	{
		if(!strlen($key))
		{
			return true;
		}

		if(!preg_match('/^[A-Z\d]+$/', $key))
		{
			$error[] = 'ACP_STEAMSTATUS_API_KEY_ERROR_FORMAT';
			return false;
		}

		$query = http_build_query(array(
			'key'	=> $key,
		));
		$url = 'http://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/?' . $query;
		$ctx = stream_context_create(array(
			'http'	=> array(
				'ignore_errors'	=> '1',
			),
		));
		$stream = fopen($url, 'r', false, $ctx);
		if(!$stream)
		{
			$error[] = 'ACP_STEAMSTATUS_API_KEY_VALIDATION_FAILED';
			return false;
		}

		try
		{
			$meta = stream_get_meta_data($stream);
			$http_response = (int)substr($meta['wrapper_data'][0], strpos($meta['wrapper_data'][0], ' ') + 1, 3);
			if($http_response === 403)
			{
				$error[] = 'ACP_STEAMSTATUS_API_KEY_INVALID';
				return false;
			}
			if($http_response !== 200)
			{
				$error[] = 'ACP_STEAMSTATUS_API_KEY_VALIDATION_FAILED';
				return false;
			}

			$result = json_decode(stream_get_contents($stream));
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
		}
		finally
		{
			fclose($stream);
		}

		$error[] = 'ACP_STEAMSTATUS_API_KEY_INVALID';
		return false;
	}
}
