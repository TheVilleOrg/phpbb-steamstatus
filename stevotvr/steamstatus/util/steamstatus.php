<?php

namespace stevotvr\steamstatus\util;

class steamstatus
{
	static private $status_text = array(
		'OFFLINE',
		'ONLINE',
		'BUSY',
		'AWAY',
		'SNOOZE',
		'LTT',
		'LTP',
	);

	static public function get_from_cache($steamid, $cache)
	{
		return $cache->get('stevotvr_steamstatus_id' . $steamid);
	}

	static public function get_from_api($api_key, $steamids, &$results, $cache)
	{
		if (empty($steamids))
		{
			return;
		}

		$steamids = array_chunk($steamids, 100);
		foreach ($steamids as $chunk)
		{
			$query = http_build_query(array(
				'key'		=> $api_key,
				'steamids'	=> implode(',', $chunk),
			));
			$url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?' . $query;
			$response = @file_get_contents($url);
			if ($response)
			{
				$response = json_decode($response);
				if ($response && $response->response && is_array($response->response->players))
				{
					$now = time();
					foreach ($response->response->players as $player)
					{
						$user = array(
							'time'	=> $now,
							'data'	=> array(
								'steamid'		=> $player->steamid,
								'name'			=> $player->personaname,
								'profile'		=> $player->profileurl,
								'avatar'		=> $player->avatar,
								'state'			=> self::get_profile_state($player),
								'status'		=> self::get_profile_status($player),
								'lastlogoff'	=> $player->lastlogoff,
							),
						);
						// TODO: Fix caching
						$cache->put('stevotvr_steamstatus_id' . $player->steamid, $user);
						$results[] = $user;
					}
				}
			}
		}
	}

	static public function get_localized_data($user, $language)
	{
		$data = $user['data'];
		if ($data['state'] < 2)
		{
			$data['status'] = $language->lang('STEAMSTATUS_STATUS_' . $data['status']);
		}
		return $data;
	}

	static private function get_profile_state($user)
	{
		if (!empty($user->gameextrainfo))
		{
			return 2;
		}
		if ($user->personastate > 0)
		{
			return 1;
		}
		return 0;
	}

	static private function get_profile_status($user)
	{
		if (!empty($user->gameextrainfo))
		{
			return $user->gameextrainfo;
		}
		return self::$status_text[$user->personastate];
	}
}
