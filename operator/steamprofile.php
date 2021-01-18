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

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use stevotvr\steamstatus\entity\steamprofile_interface as entity;
use stevotvr\steamstatus\exception\out_of_bounds;
use stevotvr\steamstatus\operator\http_helper_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Steam Status steamprofile operator for managing steamprofile entities.
 */
class steamprofile implements steamprofile_interface
{
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
	 * @var config
	 */
	protected $config;

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var http_helper_interface
	 */
	protected $http_helper;

	/**
	 * The name of the database table storing Steam profiles.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * @param config                $config
	 * @param ContainerInterface    $container
	 * @param driver_interface      $db
	 * @param http_helper_interface $http_helper
	 * @param string                $table_name The name of the database table storing Steam profiles
	 */
	public function __construct(config $config, ContainerInterface $container, driver_interface $db, http_helper_interface $http_helper, $table_name)
	{
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->http_helper = $http_helper;
		$this->table_name = $table_name;
	}

	/**
	 * @inheritDoc
	 */
	public function get_table_name()
	{
		return $this->table_name;
	}

	/**
	 * @inheritDoc
	 */
	public function get()
	{
		return $this->container->get('stevotvr.steamstatus.entity');
	}

	/**
	 * @inheritDoc
	 */
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
			$url = 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?' . $query;
			$response = $this->http_helper->get($url);
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
							'steam_profileurl'	=> str_replace('http://', 'https://', $player->profileurl),
							'steam_avatarurl'	=> str_replace('http://', 'https://', $player->avatar),
							'steam_state'		=> self::get_profile_state($player),
							'steam_status'		=> self::get_profile_status($player),
							'steam_lastlogoff'	=> $player->lastlogoff ? $player->lastlogoff : 0,
						);
						$profiles[] = $this->container->get('stevotvr.steamstatus.entity')->import($data)->save();
					}
				}
			}
		}

		return $profiles;
	}

	/**
	 * @inheritDoc
	 */
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
			if (preg_match('/^\d+$/', $steamid))
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
}
