<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\operator;

use phpbb\cache\service;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use stevotvr\steamstatus\entity\steamprofile_interface as entity;
use stevotvr\steamstatus\exception\out_of_bounds;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Steam Status steamprofile operator for managing steamprofile entities.
 */
class steamprofile implements steamprofile_interface
{
	/* How long to cache vanity URL lookup results */
	const VANITY_LOOKUP_CACHE_TIME = 3600;

	/**
	 * Steam profile status options.
	 *
	 * @var array
	 */
	static protected $status_text = array(
		'OFFLINE',
		'ONLINE',
		'BUSY',
		'AWAY',
		'SNOOZE',
		'LTT',
		'LTP',
	);

	/**
	 * @var \phpbb\cache\service
	 */
	private $cache;

	/**
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * The name of the database table storing Steam profiles.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * @param \phpbb\cache\service              $cache
	 * @param \phpbb\config\config              $config
	 * @param ContainerInterface                $container
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string                            $table_name The name of the database table storing
	 *                                                      Steam profiles
	 */
	public function __construct(service $cache, config $config, ContainerInterface $container, driver_interface $db, $table_name)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	public function get_table_name()
	{
		return $this->table_name;
	}

	public function get()
	{
		return $this->container->get('stevotvr.steamstatus.entity');
	}

	public function get_from_api(array $steamids)
	{
		$profiles = array();

		$api_key = $this->config['stevotvr_steamstatus_api_key'];
		if (empty($api_key))
		{
			throw new out_of_bounds('stevotvr_steamstatus_api_key');
		}

		$steamids = self::get_valid_steamids($steamids);
		$steamids = $this->get_stale_steamids($steamids);
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
						$data = array(
							'steam_steamid'		=> $player->steamid,
							'steam_querytime'	=> $now,
							'steam_name'		=> $player->personaname,
							'steam_profileurl'	=> $player->profileurl,
							'steam_avatarurl'	=> $player->avatar,
							'steam_state'		=> self::get_profile_state($player),
							'steam_status'		=> self::get_profile_status($player),
							'steam_lastlogoff'	=> $player->lastlogoff,
						);
						$profiles[] = $this->container->get('stevotvr.steamstatus.entity')->import($data)->save();
					}
				}
			}
		}

		return $profiles;
	}

	public function get_from_cache($steamid)
	{
		try
		{
			return $this->container->get('stevotvr.steamstatus.entity')->load($steamid);
		}
		catch (out_of_bounds $e)
		{
			return false;
		}
	}

	public function to_steamid64($steamid, &$error)
	{
		$steamid64 = null;
		$matches = array();
		if ($steamid === '')
		{
			$steamid64 = '';
		}
		else if (preg_match('/^STEAM_0:([0-1]):(\d+)$/', $steamid, $matches) === 1)
		{
			$steamid64 = self::add($matches[2] * 2 + $matches[1], '76561197960265728');
		}
		else if (preg_match('/^\[?U:1:(\d+)\]?$/', $steamid, $matches) === 1)
		{
			$steamid64 = self::add($matches[1], '76561197960265728');
		}
		else if (preg_match('/(?:steamcommunity.com\/profiles\/)?(\d{17})\/?$/', $steamid, $matches) === 1)
		{
			$steamid64 = $matches[1];
		}
		else if (preg_match('/(?:steamcommunity.com\/id\/)?(\w+)\/?$/', $steamid, $matches) === 1)
		{
			$cached = $this->cache->get('stevotvr_steamstatus_vanity_' . $matches[1]);
			if ($cached !== false)
			{
				if (strpos($cached, 'S') === 0)
				{
					$error = $cached;
				}
				else
				{
					$steamid64 = $cached;
				}
			}
			else
			{
				$api_key = $this->config['stevotvr_steamstatus_api_key'];
				if (empty($api_key))
				{
					throw new out_of_bounds('stevotvr_steamstatus_api_key');
				}

				$query = http_build_query(array(
					'key'		=> $api_key,
					'vanityurl'	=> $matches[1],
				));
				$url = 'https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?' . $query;
				$result = @file_get_contents($url);
				if ($result)
				{
					$result = json_decode($result);
					if ($result && $result->response && $result->response->success === 1)
					{
						$steamid64 = $result->response->steamid;
					}
					else
					{
						$error = 'STEAMSTATUS_ERROR_NAME_NOT_FOUND';
					}
				}
				else
				{
					$error = 'STEAMSTATUS_ERROR_LOOKUP_FAILED';
				}

				$cache = isset($steamid64) ? $steamid64 : $error;
				$this->cache->put('stevotvr_steamstatus_vanity_' . $matches[1], $cache, self::VANITY_LOOKUP_CACHE_TIME);
			}
		}

		if (!isset($steamid64))
		{
			$error = 'STEAMSTATUS_ERROR_INVALID_FORMAT';
		}

		return $steamid64;
	}

	/**
	 * Get a list of valid SteamID64s from a list of strings.
	 *
	 * @param array $unsafe An array of strings
	 *
	 * @return array An array of valid SteamID64 strings
	 */
	static private function get_valid_steamids(array $unsafe)
	{
		$safe = array();
		foreach ($unsafe as $steamid)
		{
			if (preg_match('/^\d{17}$/', $steamid))
			{
				$safe[] = $steamid;
			}
		}
		return $safe;
	}

	/**
	 * Get a list of stale SteamIDs from a given list.
	 *
	 * @param array $steamids An array of SteamIDs to check against
	 *
	 * @return array An array of stale SteamIDs
	 */
	private function get_stale_steamids(array $steamids)
	{
		$stale = array();
		if (empty($steamids))
		{
			return $stale;
		}

		$age = time() - (int) $this->config['stevotvr_steamstatus_cache_time'];
		$not_stale = array();

		$sql = 'SELECT steam_steamid
					FROM ' . $this->table_name . '
					WHERE ' . $this->db->sql_in_set('steam_steamid', $steamids) . '
						AND steam_querytime > ' . $age;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$not_stale[] = $row['steam_steamid'];
		}
		$this->db->sql_freeresult($result);

		return array_diff($steamids, $not_stale);
	}

	/**
	 * Get the state of a profile based on data returned from the Steam Web API.
	 *
	 * @param \stdClass $profile The response from the API
	 *
	 * @return int One of the \stevotvr\steamstatus\entity\steamprofile_interface::STATE* constants
	 */
	static private function get_profile_state(\stdClass $profile)
	{
		if (!empty($profile->gameextrainfo))
		{
			return entity::STATE_INGAME;
		}
		if ($profile->personastate > 0)
		{
			return entity::STATE_ONLINE;
		}
		return entity::STATE_OFFLINE;
	}

	/**
	 * Get the status of a profile based on data returned from the Steam Web API.
	 *
	 * @param \stdClass	$profile The response from the API
	 *
	 * @return string One of the $status_text values or the name of the game being played
	 */
	static private function get_profile_status(\stdClass $profile)
	{
		if (!empty($profile->gameextrainfo))
		{
			return $profile->gameextrainfo;
		}
		return self::$status_text[$profile->personastate];
	}

	/**
	 * Add two integers as strings. This allows addition of integers of arbitrary lengths on any
	 * system without external dependencies.
	 *
	 * @param string $left  A numeric string
	 * @param string $right A numeric string
	 *
	 * @return string The sum as a numeric string
	 */
	static private function add($left, $right)
	{
		$left = str_pad($left, strlen($right), '0', STR_PAD_LEFT);
		$right = str_pad($right, strlen($left), '0', STR_PAD_LEFT);

		$carry = 0;
		$result = '';
		for ($i = strlen($left) - 1; $i >= 0; --$i)
		{
			$sum = $left[$i] + $right[$i] + $carry;
			$carry = (int) ($sum / 10);
			$result .= $sum % 10;
		}
		if ($carry)
		{
			$result .= '1';
		}

		return strrev($result);
	}
}
