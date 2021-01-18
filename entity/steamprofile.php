<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\entity;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use stevotvr\steamstatus\exception\invalid_argument;
use stevotvr\steamstatus\exception\out_of_bounds;
use stevotvr\steamstatus\exception\unexpected_value;

/**
 * Steam Status entity representing a Steam profile.
 */
class steamprofile implements steamprofile_interface
{
	/**
	 * The profile data.
	 *
	 * @var array
	 *       steam_steamid
	 *       steam_querytime
	 *       steam_name
	 *       steam_profileurl
	 *       steam_avatarurl
	 *       steam_state
	 *       steam_status
	 *       steam_lastlogoff
	 */
	protected $data;

	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * The name of the database table storing Steam profiles.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * @param config           $config
	 * @param driver_interface $db
	 * @param language         $language
	 * @param string           $table_name The name of the database table storing Steam profiles
	 */
	public function __construct(config $config, driver_interface $db, language $language, $table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->table_name = $table_name;
	}

	/**
	 * @inheritDoc
	 */
	public function load($steamid64)
	{
		if (!self::is_valid_steamid64($steamid64))
		{
			throw new unexpected_value('steamid64', $steamid64);

		}

		$sql = 'SELECT *
				FROM ' . $this->table_name . '
				WHERE steam_steamid = ' . $this->db->sql_escape($steamid64);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			throw new out_of_bounds('steamid64', $steamid64);
		}

		$this->data['steam_name'] = base64_decode($this->data['steam_name']);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function import(array $data)
	{
		$this->data = array();

		$fields = array(
			'steam_steamid'		=> 'set_steamid',
			'steam_querytime'	=> 'integer',
			'steam_name'		=> 'set_name',
			'steam_profileurl'	=> 'set_profile',
			'steam_avatarurl'	=> 'set_avatar',
			'steam_state'		=> 'set_state',
			'steam_status'		=> 'set_status',
			'steam_lastlogoff'	=> 'integer',
		);

		foreach ($fields as $field => $type)
		{
			if (!isset($data[$field]))
			{
				throw new invalid_argument($field);
			}

			if (method_exists($this, $type))
			{
				$this->$type($data[$field]);
				continue;
			}

			$value = $data[$field];
			settype($value, $type);
			$this->data[$field] = $value;
		}

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function save()
	{
		if (empty($this->data['steam_steamid']))
		{
			throw new out_of_bounds('steam_steamid', 0);
		}

		$data = array(
			'steam_querytime'	=> $this->data['steam_querytime'],
			'steam_name'		=> base64_encode($this->data['steam_name']),
			'steam_profileurl'	=> $this->data['steam_profileurl'],
			'steam_avatarurl'	=> $this->data['steam_avatarurl'],
			'steam_state'		=> $this->data['steam_state'],
			'steam_status'		=> $this->data['steam_status'],
			'steam_lastlogoff'	=> $this->data['steam_lastlogoff'],
		);
		$sql = 'UPDATE ' . $this->table_name . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE steam_steamid = ' . $this->db->sql_escape($this->data['steam_steamid']);
		$this->db->sql_query($sql);
		if ($this->db->sql_affectedrows() < 1)
		{
			$data['steam_steamid'] = $this->data['steam_steamid'];
			$sql = 'INSERT INTO ' . $this->table_name . '
					' . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
		}

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_steamid()
	{
		return isset($this->data['steam_steamid']) ? (string) $this->data['steam_steamid'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_steamid($steamid)
	{
		$steamid = (string) trim($steamid);

		if (!self::is_valid_steamid64($steamid))
		{
			throw new unexpected_value('steamid', $steamid);
		}

		$this->data['steam_steamid'] = $steamid;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_querytime()
	{
		return isset($this->data['steam_querytime']) ? (int) $this->data['steam_querytime'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function is_stale()
	{
		return time() - $this->data['steam_querytime'] > $this->config['stevotvr_steamstatus_cache_time'];
	}

	/**
	 * @inheritDoc
	 */
	public function set_querytime($querytime)
	{
		$this->data['steam_querytime'] = (int) $querytime;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return isset($this->data['steam_name']) ? (string) $this->data['steam_name'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_name($name)
	{
		$name = truncate_string((string) $name, 32);

		$this->data['steam_name'] = $name;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_profile()
	{
		return isset($this->data['steam_profileurl']) ? (string) $this->data['steam_profileurl'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_profile($profile)
	{
		$profile = truncate_string((string) $profile, 255);

		$this->data['steam_profileurl'] = $profile;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_avatar()
	{
		return isset($this->data['steam_avatarurl']) ? (string) $this->data['steam_avatarurl'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_avatar($avatar)
	{
		$avatar = truncate_string((string) $avatar, 255);

		$this->data['steam_avatarurl'] = $avatar;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_state()
	{
		return isset($this->data['steam_state']) ? (int) $this->data['steam_state'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function set_state($state)
	{
		$state = (int) $state;

		if ($state < steamprofile_interface::STATE_OFFLINE || $state > steamprofile_interface::STATE_INGAME)
		{
			throw new out_of_bounds('state', $state);
		}

		$this->data['steam_state'] = $state;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_status()
	{
		return isset($this->data['steam_status']) ? (string) $this->data['steam_status'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function get_localized_status()
	{
		if (!isset($this->data['steam_status']))
		{
			return '';
		}

		if ((int) $this->data['steam_state'] === self::STATE_INGAME)
		{
			return (string) $this->data['steam_status'];
		}

		return $this->language->lang('STEAMSTATUS_STATUS_' . $this->data['steam_status']);
	}

	/**
	 * @inheritDoc
	 */
	public function set_status($status)
	{
		$status = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", (string) $status);
		$status = truncate_string($status, 255);

		$this->data['steam_status'] = $status;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_lastlogoff()
	{
		return isset($this->data['steam_lastlogoff']) ? (int) $this->data['steam_lastlogoff'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function set_lastlogoff($lastlogoff)
	{
		$this->data['steam_lastlogoff'] = (int) $lastlogoff;

		return $this;
	}

	/**
	 * Check whether a SteamID64 is valid.
	 *
	 * @param string $steamid64 The value to check
	 *
	 * @return boolean Whether the value is a valid SteamID64
	 */
	static protected function is_valid_steamid64($steamid64)
	{
		if (is_string($steamid64))
		{
			return preg_match('/^\d{17}$/', $steamid64) === 1;
		}

		return false;
	}
}
