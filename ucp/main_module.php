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

use phpbb\json_response;

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
		$request = $phpbb_container->get('request');

		$this->tpl_name = 'ucp_steamstatus_body';
		$this->page_title = 'UCP_STEAMSTATUS_TITLE';

		if ($request->variable('action', '') === 'disconnect')
		{
			$this->disconnect();
		}

		$openid_mode = $openid->get_mode();
		if ($openid_mode && $openid_mode !== 'cancel')
		{
			$openid->set_return_url(generate_board_url() . '/' .  $this->u_action);
			if ($openid->validate())
			{
				$steamid64 = $openid->get_id();
				if (!empty($steamid64))
				{
					$sql = 'UPDATE ' . USERS_TABLE . "
							SET user_steamid = '" . $db->sql_escape($steamid64) . "'
							WHERE user_id = " . (int) $user->data['user_id'];
					$db->sql_query($sql);
				}
			}

			redirect($this->u_action);
		}

		$template->assign_vars(array(
			'STEAMSTATUS_STEAMID'	=> $user->data['user_steamid'],

			'U_ACTION'				=> $this->u_action,
			'U_STEAMSTATUS_OPENID'	=> $openid->get_url(),
		));
	}

	private function disconnect()
	{
		global $phpbb_container;
		$language = $phpbb_container->get('language');
		$db = $phpbb_container->get('dbal.conn');
		$user = $phpbb_container->get('user');
		$request = $phpbb_container->get('request');

		if (!confirm_box(true))
		{
			$hidden_fields = build_hidden_fields(array(
				'action'	=> 'disconnect',
			));
			confirm_box(false, $language->lang('UCP_STEAMSTATUS_DISCONNECT_CONFIRM'), $hidden_fields);
			return;
		}

		$sql = 'UPDATE ' . USERS_TABLE . "
				SET user_steamid = ''
				WHERE user_id = " . (int) $user->data['user_id'];
		$db->sql_query($sql);

		if ($request->is_ajax())
		{
		    $json_response = new json_response();
		    $json_response->send(array(
				'REFRESH_DATA'	=> array(
					'url'	=> $this->u_action,
				),
			));
		}

		redirect($this->u_action);
	}
}
