<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\cron\task;

use phpbb\config\config;
use phpbb\cron\task\base;
use phpbb\db\driver\driver_interface;

/**
 * Steam Status main cron task.
 */
class main extends base
{
	/* The interval of the cron task in seconds */
	const INTERVAL = 2592000;

	/* The minimum age of a cache entry to be pruned */
	const MIN_PRUNE_AGE = 86400;

	/**
	 * @var \phpbb\config\config
	 */
	protected $config;

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
	 * @param \phpbb\config\config              $config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string                            $table_name The name of the database table storing
	 *                                                      Steam profiles
	 */
	public function __construct(config $config, driver_interface $db, $table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	public function run()
	{
		$cache_ids = array();
		$sql = 'SELECT steam_steamid
				FROM ' . $this->table_name . '
				WHERE steam_querytime < ' . (time() - self::MIN_PRUNE_AGE);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$cache_ids[] = $row['steam_steamid'];
		}
		$this->db->sql_freeresult($result);

		$user_ids = array();
		$sql = 'SELECT user_steamid
				FROM ' . USERS_TABLE . "
				WHERE user_steamid <> ''";
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_ids[] = $row['user_steamid'];
		}
		$this->db->sql_freeresult($result);

		$cache_ids = array_diff($cache_ids, $user_ids);
		if (count($cache_ids) > 0)
		{
			$sql = 'DELETE FROM ' . $this->table_name . '
					WHERE ' . $this->db->sql_in_set('steam_steamid', $cache_ids);
			$this->db->sql_query($sql);
		}

		$this->config->set('stevotvr_steamstatus_cron_last_run', time());
	}

	public function should_run()
	{
		return (time() - (int) $this->config['stevotvr_steamstatus_cron_last_run']) > self::INTERVAL;
	}
}
