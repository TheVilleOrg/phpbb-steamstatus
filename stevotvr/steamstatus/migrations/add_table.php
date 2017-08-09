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

class add_table extends \phpbb\db\migration\migration
{
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
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'steamstatus',
			),
		);
	}
}
