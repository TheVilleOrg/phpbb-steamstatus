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
 * Steam Status migration for version 1.1.0.
 */
class version_1_1_0 extends migration
{
	static public function depends_on()
	{
		return array('\stevotvr\steamstatus\migrations\version_1_0_0');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('stevotvr_steamstatus_reg_field', 0)),
		);
	}
}
