<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\acp;

/**
 * Steam Status ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\steamstatus\main_module',
			'title'		=> 'ACP_STEAMSTATUS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_STEAMSTATUS_SETTINGS',
					'auth'	=> 'ext_stevotvr/steamstatus && acl_a_board',
					'cat'	=> array('ACP_STEAMSTATUS_TITLE'),
				),
			),
		);
	}
}
