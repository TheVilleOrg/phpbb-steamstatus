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

use \phpbb\config\config;
use \phpbb\db\driver\driver_interface;
use \stevotvr\steamstatus\entity\steamprofile_interface as entity;
use \stevotvr\steamstatus\exception\out_of_bounds;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Steam Status steamprofile operator for managing steamprofile entities.
 */
class steamprofile implements steamprofile_interface
{
	/* The configuration key for the Steam Web API key */
	const CONFIG_API_KEY = 'stevotvr_steamstatus_api_key';

	/**
	 * @var array Steam profile status options
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
	 * @var \pbpbb\config\config
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
	 * @var string The name of the database table storing Steam profiles
	 */
	protected $table_name;

	/**
	 * @param \pbpbb\config\config                                      $config
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 * @param \phpbb\db\driver\driver_interface                         $db
	 * @param string                                                    $table_name The name of the
	 *                                                                              database table
	 *                                                                              storing Steam
	 *                                                                              profiles
	 */
	public function __construct(config $config, ContainerInterface $container, driver_interface $db, $table_name)
	{
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	public function get_table_name()
	{
		return $this->table_name;
	}

	public function get_from_api(array $steamids)
	{
		$profiles = array();
		if (empty($steamids))
		{
			return $profiles;
		}

		$api_key = $this->config['stevotvr_steamstatus_api_key'];
		if (empty($api_key))
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
