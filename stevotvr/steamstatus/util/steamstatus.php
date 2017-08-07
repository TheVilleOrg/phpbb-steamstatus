<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\util;

/**
 * Steam Status utility functions.
 */
class steamstatus
{
	/* Steam profile states */
	const STATE_OFFLINE = 0;
	const STATE_ONLINE = 1;
	const STATE_INGAME = 2;

	/* @var array Steam profile status options */
	static private $status_text = array(
		'OFFLINE',
		'ONLINE',
		'BUSY',
		'AWAY',
		'SNOOZE',
		'LTT',
		'LTP',
	);

	/**
	 * Get Steam profile data for a single profile from the cache.
	 *
	 * @param string				$steamid	The SteamID64 for the profile
	 * @param \phpbb\cache\service	$cache
	 *
	 * @return mixed							The cached data, or false if it doesn't exist
	 */
	static public function get_from_cache($steamid, \phpbb\cache\service $cache)
	{
		return $cache->get('stevotvr_steamstatus_id' . $steamid);
	}

	/**
	 * Get Steam profile data from the Steam Web API.
	 *
	 * @param string				$api_key	The Steam Web API key
	 * @param array					$steamids	The list of SteamIDs for which to get profile data
	 * @param \phpbb\cache\service	$cache
	 *
	 * @return array							An array of associative arrays representing the
	 *                        					Steam profiles
	 */
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
						$cache->put('stevotvr_steamstatus_id' . $player->steamid, $profile);
						$cache->save();
						$profiles[] = $profile['data'];
					}
				}
			}
		}

		return $profiles;
	}

	/**
	 * Get a localized version of the data for a profile.
	 *
	 * @param array						$profile	The raw profile data
	 * @param \phpbb\language\language	$language
	 *
	 * @return array								The localized profile data
	 */
	static public function get_localized_data(array $profile, \phpbb\language\language $language)
	{
		if ($profile['state'] < 2)
		{
			$profile['status'] = $language->lang('STEAMSTATUS_STATUS_' . $profile['status']);
		}
		return $profile;
	}

	/**
	 * Get the state of a profile based on data returned from the Steam Web API.
	 *
	 * @param \stdClass	$profile	The response from the API
	 *
	 * @return int					One of the STATE_* constants
	 */
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

	/**
	 * Get the status of a profile based on data returned from the Steam Web API.
	 *
	 * @param \stdClass	$profile	The response from the API
	 *
	 * @return string				One of the $status_text values or the name of the game being
	 *                       		played
	 */
	static private function get_profile_status(\stdClass $profile)
	{
		if (!empty($profile->gameextrainfo))
		{
			return $profile->gameextrainfo;
		}
		return self::$status_text[$profile->personastate];
	}
}
