<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\event;

use phpbb\config\config;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;
use stevotvr\steamstatus\operator\steamprofile_interface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Steam Status user event listener.
 */
class user_listener implements EventSubscriberInterface
{
	/**
	 * @var \phpbb\config\config
	 */
	private $config;

	/**
	 * @var \phpbb\language\language
	 */
	private $language;

	/**
	 * @var \phpbb\request\request_interface
	 */
	private $request;

	/**
	 * @var \stevotvr\steamstatus\operator\steamprofile_interface
	 */
	private $steamprofile;

	/**
	 * @var \phpbb\template\template
	 */
	private $template;

	/**
	 * @var \phpbb\user
	 */
	private $user;

	/**
	 * @param \phpbb\config\config                                  $config
	 * @param \phpbb\language\language                              $language
	 * @param \phpbb\request\request_interface                      $request
	 * @param \stevotvr\steamstatus\operator\steamprofile_interface $steamprofile
	 * @param \phpbb\template\template                              $template
	 * @param \phpbb\user                                           $user
	 */
	function __construct(config $config, language $language, request_interface $request, steamprofile_interface $steamprofile, template $template, user $user)
	{
		$this->config = $config;
		$this->language = $language;
		$this->request = $request;
		$this->steamprofile = $steamprofile;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_users_modify_profile'			=> 'acp_users_modify_profile',
			'core.acp_users_profile_modify_sql_ary'	=> 'profile_modify_sql_ary',
			'core.acp_users_profile_validate'		=> 'validate_profile_info',

			'core.ucp_profile_info_modify_sql_ary'		=> 'profile_modify_sql_ary',
			'core.ucp_profile_modify_profile_info'		=> 'ucp_profile_modify_profile_info',
			'core.ucp_profile_validate_profile_info'	=> 'validate_profile_info',

			'core.ucp_register_data_after'		=> 'ucp_register_data_after',
			'core.ucp_register_data_before'		=> 'ucp_register_data_before',
			'core.ucp_register_user_row_after'	=> 'ucp_register_user_row_after',
		);
	}

	/**
	 * Loads the language files and sets the template variables for the Profile page of the APC.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function acp_users_modify_profile(data $event)
	{
		if (empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'S_STEAMSTATUS_SHOW'	=> true,

			'STEAMSTATUS_STEAMID'	=> $event['user_row']['user_steamid'],
		));
	}

	/**
	 * Saves the SteamID when the form is submitted.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function profile_modify_sql_ary(data $event)
	{
		if (empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		if (isset($event['data']['steamstatus_steamid']))
		{
			$sql_ary = $event['sql_ary'];
			$sql_ary['user_steamid'] = $event['data']['steamstatus_steamid'];
			$event['sql_ary'] = $sql_ary;

			if (!empty($event['data']['steamstatus_steamid']))
			{
				$this->steamprofile->get_from_api(array($event['data']['steamstatus_steamid']));
			}
		}
	}

	/**
	 * Loads the language files and sets the template variables for the Profile page of the UPC.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function ucp_profile_modify_profile_info(data $event)
	{
		if (empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'S_STEAMSTATUS_SHOW'	=> true,

			'STEAMSTATUS_STEAMID'	=> $this->user->data['user_steamid'],
		));
	}

	/**
	 * Validates and converts the SteamID when the registration form is submitted, and adds it to
	 * the custom profile fields array. Also populates the template variable.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function ucp_register_data_after(data $event)
	{
		if (!$this->config['stevotvr_steamstatus_reg_field'] || empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->validate_profile_info($event);

		if (isset($event['data']['steamstatus_steamid']))
		{
			$cp_data = $event['cp_data'];
			$cp_data['user_steamid'] = $event['data']['steamstatus_steamid'];
			$event['cp_data'] = $cp_data;
		}

		$this->template->assign_var('STEAMSTATUS_STEAMID', $event['data']['steamstatus_steamid']);
	}

	/**
	 * Loads the language files and sets the template variables for the user registration page.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function ucp_register_data_before(data $event)
	{
		if (!$this->config['stevotvr_steamstatus_reg_field'] || empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_var('S_STEAMSTATUS_SHOW', true);
	}

	/**
	 * Moves the SteamID from the custom profile fields array to the user row array, if present.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function ucp_register_user_row_after(data $event)
	{
		if (isset($event['cp_data']['user_steamid']))
		{
			$cp_data = $event['cp_data'];

			$user_row = $event['user_row'];
			$user_row['user_steamid'] = $cp_data['user_steamid'];
			$event['user_row'] = $user_row;

			if (!empty($cp_data['user_steamid']))
			{
				$this->steamprofile->get_from_api(array($cp_data['user_steamid']));
			}

			unset($cp_data['user_steamid']);
			$event['cp_data'] = $cp_data;
		}
	}

	/**
	 * Reads the SteamID when the form is submitted and attempts to convert it to the SteamID64
	 * format. Produces an error if the conversion fails.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function validate_profile_info(data $event)
	{
		if (empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$steamid = $this->request->variable('steamstatus_steamid', '0', false, request_interface::POST);
		if ($steamid !== '0')
		{
			$steam_error = null;
			$steamid64 = $this->steamprofile->to_steamid64($steamid, $steam_error);

			if (!isset($steamid64))
			{
				$error = $event['error'];
				$error[] = $this->language->lang($steam_error);
				$event['error'] = $error;
			}

			$data = $event['data'];
			$data['steamstatus_steamid'] = $steamid64 ? $steamid64 : $steamid;
			$event['data'] = $data;
		}
	}
}
