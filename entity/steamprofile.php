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
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * The name of the database table storing Steam profiles.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * @param \phpbb\config\config              $config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\language\language          $language
	 * @param string                            $table_name The name of the database table storing
	 *                                                      Steam profiles
	 */
	public function __construct(config $config, driver_interface $db, language $language, $table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->table_name = $table_name;
	}

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

		return $this;
	}

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

	public function save()
	{
		if (empty($this->data['steam_steamid']))
		{
			throw new out_of_bounds('steam_steamid', 0);
		}

		$data = array(
			'steam_querytime'	=> $this->data['steam_querytime'],
			'steam_name'		=> $this->data['steam_name'],
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

	public function get_steamid()
	{
		return isset($this->data['steam_steamid']) ? (string) $this->data['steam_steamid'] : '';
	}

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

	public function get_querytime()
	{
		return isset($this->data['steam_querytime']) ? (int) $this->data['steam_querytime'] : 0;
	}

	public function is_stale()
	{
		return time() - $this->data['steam_querytime'] > $this->config['stevotvr_steamstatus_cache_time'];
	}

	public function set_querytime($querytime)
	{
		$this->data['steam_querytime'] = (int) $querytime;

		return $this;
	}

	public function get_name()
	{
		return isset($this->data['steam_name']) ? (string) $this->data['steam_name'] : '';
	}

	public function set_name($name)
	{
		$name = (string) $name;

		if (truncate_string($name, 255) !== $name)
		{
			throw new unexpected_value('name', '[too_long]');
		}

		$this->data['steam_name'] = $name;

		return $this;
	}

	public function get_profile()
	{
		return isset($this->data['steam_profileurl']) ? (string) $this->data['steam_profileurl'] : '';
	}

	public function set_profile($profile)
	{
		$profile = (string) $profile;

		if (truncate_string($profile, 255) !== $profile)
		{
			throw new unexpected_value('profile', '[too_long]');
		}

		$this->data['steam_profileurl'] = $profile;

		return $this;
	}

	public function get_avatar()
	{
		return isset($this->data['steam_avatarurl']) ? (string) $this->data['steam_avatarurl'] : '';
	}

	public function set_avatar($avatar)
	{
		$avatar = (string) $avatar;

		if (truncate_string($avatar, 255) !== $avatar)
		{
			throw new unexpected_value('avatar', '[too_long]');
		}

		$this->data['steam_avatarurl'] = $avatar;

		return $this;
	}

	public function get_state()
	{
		return isset($this->data['steam_state']) ? (int) $this->data['steam_state'] : 0;
	}

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

	public function get_status()
	{
		return isset($this->data['steam_status']) ? (string) $this->data['steam_status'] : '';
	}

	public function get_localized_status()
	{
		if (!isset($this->data['steam_status']))
		{
			return '';
		}

		if ($this->data['steam_state'] === self::STATE_INGAME)
		{
			return (string) $this->data['steam_status'];
		}

		return $this->language->lang('STEAMSTATUS_STATUS_' . $this->data['steam_status']);
	}

	public function set_status($status)
	{
		$status = (string) $status;

		if (truncate_string($status, 255) !== $status)
		{
			throw new unexpected_value('status', '[too_long]');
		}

		$this->data['steam_status'] = $status;

		return $this;
	}

	public function get_lastlogoff()
	{
		return isset($this->data['steam_lastlogoff']) ? (int) $this->data['steam_lastlogoff'] : 0;
	}

	public function set_lastlogoff($lastlogoff)
	{
		$this->data['steam_lastlogoff'] = (int) $lastlogoff;

		return $this;
	}

	static protected function is_valid_steamid64($steamid64)
	{
		if (is_string($steamid64))
		{
			return preg_match('/^\d{17}$/', $steamid64) === 1;
		}

		return false;
	}
}
