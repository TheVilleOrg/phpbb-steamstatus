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

use \phpbb\config\config;
use \phpbb\event\data;
use \phpbb\language\language;
use \phpbb\request\request;
use \phpbb\request\request_interface;
use \phpbb\template\template;
use \phpbb\user;
use \stevotvr\steamstatus\operator\steamprofile_interface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Steam Status listener for ucp events.
 */
class ucp_listener implements EventSubscriberInterface
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
	 * @var \phpbb\request\request
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
	 * @param \phpbb\request\request                                $request
	 * @param \stevotvr\steamstatus\operator\steamprofile_interface $steamprofile
	 * @param \phpbb\template\template                              $template
	 * @param \phpbb\user                                           $user
	 */
	function __construct(config $config, language $language, request $request, steamprofile_interface $steamprofile, template $template, user $user)
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
			'core.ucp_profile_modify_profile_info'		=> 'ucp_profile_modify_profile_info',
			'core.ucp_profile_validate_profile_info'	=> 'ucp_profile_validate_profile_info',
			'core.ucp_profile_info_modify_sql_ary'		=> 'ucp_profile_info_modify_sql_ary',
		);
	}

	/**
	 * Loads the language files and sets the template variables for the Profile page of the UPC.
	 *
	 * @param \phpbb\event\data $event
	 */
	public function ucp_profile_modify_profile_info(data $event)
	{
		if (empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'STEAMSTATUS_STEAMID'	=> $this->user->data['user_steamid'],
			'S_STEAMSTATUS_SHOW'	=> true,
		));
	}

	/**
	 * Reads the SteamID when the user updates their profile and attempts to convert it to the
	 * SteamID64 format. Produces an error if the conversion fails.
	 *
	 * @param \phpbb\event\data $event
	 */
	public function ucp_profile_validate_profile_info(data $event)
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
				$error[] = $steam_error;
				$event['error'] = $error;
			}
			else
			{
				$data = $event['data'];
				$data['steamstatus_steamid'] = $steamid64;
				$event['data'] = $data;
			}
		}
	}

	/**
	 * Saves the SteamID when the user updates their profile.
	 *
	 * @param \phpbb\event\data $event
	 */
	public function ucp_profile_info_modify_sql_ary(data $event)
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
}
