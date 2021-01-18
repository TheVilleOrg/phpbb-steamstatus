<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\migrations;

use phpbb\db\migration\migration;

/**
 * Steam Status migration for version 1.3.2.
 */
class version_1_3_2 extends migration
{
	/**
	 * @inheritDoc
	 */
	static public function depends_on()
	{
		return array('\stevotvr\steamstatus\migrations\version_1_3_0');
	}

	/**
	 * @inheritDoc
	 */
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'purge_cache'))),
		);
	}

	/**
	 * Purge all cached Steam profiles.
	 */
	public function purge_cache()
	{
		$this->db->sql_query('DELETE FROM ' . $this->table_prefix . 'steamstatus');
	}
}
