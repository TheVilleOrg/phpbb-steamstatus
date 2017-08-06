<?php

namespace stevotvr\steamstatus\util;

class steamstatus
{
	const STATE_OFFLINE = 0;
	const STATE_ONLINE = 1;
	const STATE_INGAME = 2;

	static private $status_text = array(
		'OFFLINE',
		'ONLINE',
		'BUSY',
		'AWAY',
		'SNOOZE',
		'LTT',
		'LTP',
	);

	static public function get_from_cache($steamid, \phpbb\cache\service $cache)
	{
		return $cache->get('stevotvr_steamstatus_id' . $steamid);
	}

	static public function get_from_api($api_key, array $steamids, \phpbb\cache\service $cache)
	{
		$profiles = array();
		if (empty($steamids))
		{
			return $profiles;
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
						$profile = array(
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
						$cache->put('stevotvr_steamstatus_id' . $player->steamid, $profile);
						$profiles[] = $profile['data'];
					}
				}
			}
		}

		return $profiles;
	}

	static public function get_localized_data(array $profile, \phpbb\language\language $language)
	{
		if ($profile['state'] < 2)
		{
			$profile['status'] = $language->lang('STEAMSTATUS_STATUS_' . $profile['status']);
		}
		return $profile;
	}

	static private function get_profile_state(\stdClass $profile)
	{
		if (!empty($profile->gameextrainfo))
		{
			return self::STATE_INGAME;
		}
		if ($profile->personastate > 0)
		{
			return self::STATE_ONLINE;
		}
		return self::STATE_OFFLINE;
	}

	static private function get_profile_status(\stdClass $profile)
	{
		if (!empty($profile->gameextrainfo))
		{
			return $profile->gameextrainfo;
		}
		return self::$status_text[$profile->personastate];
	}
}
