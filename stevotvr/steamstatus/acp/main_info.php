<?php

namespace stevotvr\steamstatus\acp;

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
