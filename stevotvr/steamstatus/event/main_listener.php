<?php

namespace stevotvr\steamstatus\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'							=> 'load_language_on_setup',
			'core.ucp_profile_validate_profile_info'	=> 'validate_id',
			'core.ucp_profile_info_modify_sql_ary'		=> 'modify_id',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'stevotvr/steamstatus',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function validate_id($event)
	{
		global $request, $config;

		$steam_id = trim($request->variable('pf_steam_id', ''));
		if($steam_id)
		{
			$steam_id64 = null;
			$matches = array();
			if(preg_match('/^STEAM_0:([0-1]):(\d+)$/', $steam_id, $matches) === 1)
			{
				// TODO: Add fallback for when bcmath is unavailable
				$steam_id64 = \bcadd($matches[2] * 2 + $matches[1], '76561197960265728');
			}
			elseif(preg_match('/^\[?U:1:(\d+)\]?$/', $steam_id, $matches) === 1)
			{
				// TODO: Add fallback for when bcmath is unavailable
				$steam_id64 = \bcadd($matches[1], '76561197960265728');
			}
			elseif(preg_match('/(?:steamcommunity.com\/profiles\/)?(\d{17})\/?$/', $steam_id, $matches) === 1)
			{
				$steam_id64 = $matches[1];
			}
			elseif(preg_match('/(?:steamcommunity.com\/id\/)?(\w+)\/?$/', $steam_id, $matches) === 1)
			{
				// TODO: Check HTTP response code
				$url = sprintf('https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key=%s&vanityurl=%s', $config['stevotvr_steamstatus_key'], $matches[1]);
				$result = file_get_contents($url);
				if($result)
				{
					$result = json_decode($result);
					if($result && $result->response && $result->response->success)
					{
						$steam_id64 = $result->response->steamid;
					}
				}
			}
			if(!$steam_id64)
			{
				$error = $event['error'];
				$error[] = 'ERROR_INVALID_FORMAT';
				$event['error'] = $error;
			}
			else
			{
				$data = $event['data'];
				$data['pf_steam_id'] = $steam_id64;
				$event['data'] = $data;
			}
		}
	}

	public function modify_id($event)
	{
		if(sizeof($event['data']['pf_steam_id'])) {
			$cp_data = $event['cp_data'];
			$cp_data['pf_steam_id'] = $event['data']['pf_steam_id'];
			$event['cp_data'] = $cp_data;
		}
	}
}
