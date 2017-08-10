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
use \stevotvr\steamstatus\operator\steamprofile_interface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Steam Status listener for acp events.
 */
class acp_listener implements EventSubscriberInterface
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
	 * @param \phpbb\config\config                                  $config
	 * @param \phpbb\language\language                              $language
	 * @param \phpbb\request\request                                $request
	 * @param \stevotvr\steamstatus\operator\steamprofile_interface $steamprofile
	 * @param \phpbb\template\template                              $template
	 */
	function __construct(config $config, language $language, request $request, steamprofile_interface $steamprofile, template $template)
	{
		$this->config = $config;
		$this->language = $language;
		$this->request = $request;
		$this->steamprofile = $steamprofile;
		$this->template = $template;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_users_modify_profile'			=> 'acp_users_modify_profile',
			'core.acp_users_profile_validate'		=> 'acp_users_profile_validate',
			'core.acp_users_profile_modify_sql_ary'	=> 'acp_users_profile_modify_sql_ary',
		);
	}

	/**
	 * Loads the language files and sets the template variables for the Profile page of the APC.
	 *
	 * @param \phpbb\event\data $event
	 */
	public function acp_users_modify_profile(data $event)
	{
		if (empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'STEAMSTATUS_STEAMID'	=> $event['user_row']['user_steamid'],
			'S_STEAMSTATUS_SHOW'	=> true,
		));
	}

	/**
	 * Reads the SteamID when the form is submitted and attempts to convert it to the SteamID64
	 * format. Produces an error if the conversion fails.
	 *
	 * @param \phpbb\event\data $event
	 */
	public function acp_users_profile_validate(data $event)
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
	 * Saves the SteamID when the form is submitted.
	 *
	 * @param \phpbb\event\data $event
	 */
	public function acp_users_profile_modify_sql_ary(data $event)
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
