<?php

namespace stevotvr\steamstatus\migrations;

class steamid_profile_field extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_steam_id'	=> array('VCHAR:17', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_steam_id',
				),
			),
		);
	}
}
