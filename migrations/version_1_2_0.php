<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\migrations;

use phpbb\db\migration\migration;

/**
 * Steam Status migration for version 1.2.0.
 */
class version_1_2_0 extends migration
{
	/**
	 * @inheritDoc
	 */
	static public function depends_on()
	{
		return array('\stevotvr\steamstatus\migrations\version_1_0_0');
	}

	/**
	 * @inheritDoc
	 */
	public function update_data()
	{
		return array(
			array('config.remove', array('stevotvr_steamstatus_reg_field')),
			array('config.add', array('stevotvr_steamstatus_https', 1)),

			array('permission.add', array('u_steamstatus', true, 'u_')),

			array('module.add', array(
				'ucp',
				'UCP_PROFILE',
				array(
					'module_basename'   => '\stevotvr\steamstatus\ucp\main_module',
					'modes'             => array('main'),
				),
			)),
		);
	}
}
