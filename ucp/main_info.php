<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\ucp;

/**
 * Steam Status UCP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\steamstatus\ucp\main_module',
			'title'		=> 'UCP_STEAMSTATUS_TITLE',
			'modes'		=> array(
				'main'	=> array(
					'title'	=> 'UCP_STEAMSTATUS_TITLE',
					'auth'	=> 'ext_stevotvr/steamstatus && acl_u_steamstatus',
					'cat'	=> array('UCP_PROFILE'),
				),
			),
		);
	}
}
