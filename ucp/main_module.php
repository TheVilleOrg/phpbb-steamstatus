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
 * Steam Status UCP module.
 */
class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$db = $phpbb_container->get('dbal.conn');
		$openid = $phpbb_container->get('stevotvr.steamstatus.operator.openid');
		$template = $phpbb_container->get('template');
		$user = $phpbb_container->get('user');

		$this->tpl_name = 'ucp_steamstatus_body';
		$this->page_title = 'UCP_STEAMSTATUS_TITLE';

		$openid->set_return_url(generate_board_url() . '/' .  $this->u_action);
		$openid_mode = $openid->get_mode();
		if ($openid_mode && $openid_mode !== 'cancel')
		{
			if ($openid->validate())
			{
				$steamid64 = $openid->get_id();
				if (!empty($steamid64))
				{
					$user_id = $user->data['user_id'];
					$sql = 'UPDATE ' . USERS_TABLE . "
							SET user_steamid = '" . $db->sql_escape($steamid64) . "'
							WHERE user_id = " . (int) $user_id;
					$db->sql_query($sql);
				}
			}

			redirect($this->u_action);
		}

		$template->assign_vars(array(
			'STEAMSTATUS_STEAMID'	=> $user->data['user_steamid'],

			'U_STEAMSTATUS_OPENID'	=> $openid->get_url(),
		));
	}
}
