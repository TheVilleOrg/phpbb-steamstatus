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

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var request_interface
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * List of error messages.
	 *
	 * @var array
	 */
	private $error = array();

	public function main($id, $mode)
	{
		global $phpbb_container;
		$this->db = $phpbb_container->get('dbal.conn');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');

		$this->tpl_name = 'ucp_steamstatus_body';
		$this->page_title = 'UCP_STEAMSTATUS_TITLE';

		if ($this->request->variable('action', '') === 'disconnect')
		{
			$this->disconnect();
		}

		$openid = $phpbb_container->get('stevotvr.steamstatus.operator.openid');
		$openid_mode = $openid->get_mode();
		if ($openid_mode === 'error')
		{
			$this->error[] = $this->language->lang('UCP_STEAMSTATUS_OPENID_ERROR', $openid->get_error());
		}
		else if ($openid_mode && $openid_mode !== 'cancel')
		{
			$openid->set_return_url(generate_board_url() . '/' .  $this->u_action);
			if ($openid->validate())
			{
				$steamid64 = $openid->get_id();
				if (!empty($steamid64))
				{
					$user_id = $this->user->data['user_id'];
					$sql = 'UPDATE ' . USERS_TABLE . "
							SET user_steamid = '" . $this->db->sql_escape($steamid64) . "'
							WHERE user_id = " . (int) $user_id;
					$this->db->sql_query($sql);

					$this->trigger_update_event($user_id, $steamid64);
				}
			}

			redirect($this->u_action);
			return;
		}

		$helper = $phpbb_container->get('controller.helper');
		$config = $phpbb_container->get('config');
		$root_path = $phpbb_container->getParameter('core.root_path');

		$steamid = $this->user->data['user_steamid'];
		$image_lang = $this->language->lang('UCP_STEAMSTATUS_OPENID_IMG_LANG');
		$image_path = $root_path . 'ext/stevotvr/steamstatus/styles/all/theme/images/' . $image_lang . '/sits.png';
		$this->template->assign_vars(array(
			'ERROR'						=> implode('<br>', $this->error),
			'STEAMSTATUS_STEAMID'		=> $steamid,
			'STEAMSTATUS_IMAGE_PATH'	=> $image_path,

			'U_ACTION'					=> $this->u_action,
			'U_STEAMSTATUS_OPENID'		=> $openid->get_url(),
		));

		if (empty($config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		if (!empty($steamid))
		{
			$steamprofile = $phpbb_container->get('stevotvr.steamstatus.operator');

			$this->language->add_lang('common', 'stevotvr/steamstatus');
			$this->template->assign_vars(array(
				'S_STEAMSTATUS'	=> true,

				'STEAMSTATUS_REFRESH'	=> $config['stevotvr_steamstatus_refresh_time'] * 60000,

				'U_STEAMSTATUS_CONTROLLER'	=> $helper->route('stevotvr_steamstatus_route'),
			));

			$cached = $steamprofile->get_from_cache($steamid);
			if ($cached)
			{
				$this->template->assign_vars(array(
					'S_STEAMSTATUS_SHOW'	=> true,

					'STEAMSTATUS_STEAMID'		=> $steamid,
					'STEAMSTATUS_NAME'			=> $cached->get_name(),
					'STEAMSTATUS_AVATAR_ALT'	=> $this->language->lang('STEAMSTATUS_AVATAR_ALT', $cached->get_name()),
					'STEAMSTATUS_PROFILE_LINK'	=> $this->language->lang('STEAMSTATUS_PROFILE_LINK', $cached->get_name()),
					'STEAMSTATUS_ADD_LINK'		=> $this->language->lang('STEAMSTATUS_ADD_LINK', $cached->get_name()),

					'U_STEAMSTATUS_PROFILE'	=> $cached->get_profile(),
					'U_STEAMSTATUS_AVATAR'	=> $cached->get_avatar(),
				));

				if (!$cached->is_stale())
				{
					$this->template->assign_vars(array(
						'S_STEAMSTATUS_LOADED'	=> true,

						'STEAMSTATUS_STATE'		=> $cached->get_state(),
						'STEAMSTATUS_STATUS'	=> $cached->get_localized_status(),
					));
				}

				return;
			}

			$this->template->assign_vars(array(
				'S_STEAMSTATUS_SHOW'	=> true,

				'U_STEAMSTATUS_PROFILE'	=> 'https://steamcommunity.com/profiles/' . $steamid,
			));
		}
	}

	/**
	 * Handle the disconnect action.
	 */
	private function disconnect()
	{
		if (!confirm_box(true))
		{
			$hidden_fields = build_hidden_fields(array(
				'action'	=> 'disconnect',
			));
			confirm_box(false, $this->language->lang('UCP_STEAMSTATUS_DISCONNECT_CONFIRM'), $hidden_fields);
			return;
		}

		$user_id = $this->user->data['user_id'];
		$sql = 'UPDATE ' . USERS_TABLE . "
				SET user_steamid = ''
				WHERE user_id = " . (int) $user_id;
		$this->db->sql_query($sql);

		$this->trigger_update_event($user_id);

		if ($this->request->is_ajax())
		{
			$json_response = new json_response();
			$json_response->send(array(
				'REFRESH_DATA'	=> array(
					'url'	=> html_entity_decode($this->u_action),
				),
			));
		}

		redirect($this->u_action);
	}

	/**
	 * Trigger the event for a user changing their SteamID.
	 *
	 * @param int    $user_id  The user ID
	 * @param string $steam_id The new SteamID
	 */
	private function trigger_update_event($user_id, $steam_id = '')
	{
		global $phpbb_container;
		$phpbb_dispatcher = $phpbb_container->get('dispatcher');

		/**
		 * Event triggered when a user updates their SteamID.
		 *
		 * @event stevotvr.steamstatus.update_steam_id
		 * @var int    user_id  The user ID
		 * @var string steam_id The new SteamID
		 * @since 0.2.1
		 */
		$vars = array('user_id', 'steam_id');
		extract($phpbb_dispatcher->trigger_event('stevotvr.steamstatus.update_steam_id', compact($vars)));
	}
}
