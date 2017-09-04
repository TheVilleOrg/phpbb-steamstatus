<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\migrations;

use phpbb\db\migration\migration;

/**
 * Steam Status migration for version 1.0.0.
 */
class version_1_0_0 extends migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'steamstatus'	=> array(
					'COLUMNS'		=> array(
						'steam_steamid'		=> array('VCHAR:17', ''),
						'steam_querytime'	=> array('TIMESTAMP', 0),
						'steam_name'		=> array('VCHAR_UNI', ''),
						'steam_profileurl'	=> array('VCHAR', ''),
						'steam_avatarurl'	=> array('VCHAR', ''),
						'steam_state'		=> array('USINT', 0),
						'steam_status'		=> array('VCHAR_UNI', ''),
						'steam_lastlogoff'	=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'steam_steamid',
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_steamid'	=> array('VCHAR:17', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'steamstatus',
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_steamid',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('stevotvr_steamstatus_api_key', '')),
			array('config.add', array('stevotvr_steamstatus_cache_time', 60)),
			array('config.add', array('stevotvr_steamstatus_refresh_time', 1)),
			array('config.add', array('stevotvr_steamstatus_show_on_profile', 1)),
			array('config.add', array('stevotvr_steamstatus_show_on_viewtopic', 1)),
			array('config.add', array('stevotvr_steamstatus_cron_last_run', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_STEAMSTATUS_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_STEAMSTATUS_TITLE',
				array(
					'module_basename'	=> '\stevotvr\steamstatus\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
