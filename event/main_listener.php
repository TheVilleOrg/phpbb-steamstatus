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
use phpbb\controller\helper;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\template\template;
use stevotvr\steamstatus\operator\steamprofile_interface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Steam Status main event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
	 * @var \phpbb\config\config
	 */
	private $config;

	/**
	 * @var \phpbb\controller\helper
	 */
	private $helper;

	/**
	 * @var \phpbb\language\language
	 */
	private $language;

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
	 * @param \phpbb\controller\helper                              $helper
	 * @param \phpbb\language\language                              $language
	 * @param \stevotvr\steamstatus\operator\steamprofile_interface $steamprofile
	 * @param \phpbb\template\template                              $template
	 */
	function __construct(config $config, helper $helper, language $language, steamprofile_interface $steamprofile, template $template)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->language = $language;
		$this->steamprofile = $steamprofile;
		$this->template = $template;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.memberlist_view_profile'	=> 'memberlist_view_profile',

			'core.viewtopic_cache_user_data'	=> 'viewtopic_cache_user_data',
			'core.viewtopic_get_post_data'		=> 'viewtopic_get_post_data',
			'core.viewtopic_modify_post_row'	=> 'viewtopic_modify_post_row',
		);
	}

	/**
	 * Loads the Steam Status template variables for the user profile.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function memberlist_view_profile(data $event)
	{
		if (!$this->config['stevotvr_steamstatus_show_on_profile'] || empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'S_STEAMSTATUS'	=> true,

			'STEAMSTATUS_REFRESH'	=> $this->config['stevotvr_steamstatus_refresh_time'] * 60000,

			'U_STEAMSTATUS_CONTROLLER'	=> $this->helper->route('stevotvr_steamstatus_route'),
		));

		$steamid = $event['member']['user_steamid'];
		if (!empty($steamid))
		{
			$cached = $this->steamprofile->get_from_cache($steamid);
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

				'STEAMSTATUS_STEAMID'	=> $steamid,

				'U_STEAMSTATUS_PROFILE'	=> 'http://steamcommunity.com/profiles/' . $steamid,
			));
		}
	}

	/**
	 * Adds the SteamID to the user data.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function viewtopic_cache_user_data(data $event)
	{
		if (!$this->config['stevotvr_steamstatus_show_on_viewtopic'] || empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$data = $event['user_cache_data'];
		$data['steamid'] = $event['row']['user_steamid'];
		foreach ($event['row'] as $key => $value)
		{
			if (strpos($key, 'steam_') === 0)
			{
				$data[$key] = $value;
			}
		}
		$event['user_cache_data'] = $data;
	}

	/**
	 * Loads the language files and sets the template variables for the View Topic page.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function viewtopic_get_post_data(data $event)
	{
		if (!$this->config['stevotvr_steamstatus_show_on_viewtopic'] || empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'S_STEAMSTATUS'	=> true,

			'STEAMSTATUS_REFRESH'	=> $this->config['stevotvr_steamstatus_refresh_time'] * 60000,

			'U_STEAMSTATUS_CONTROLLER'	=> $this->helper->route('stevotvr_steamstatus_route'),
		));

		$sql_ary = $event['sql_ary'];
		$sql_ary['SELECT'] .= ', steam.*';
		$sql_ary['LEFT_JOIN'][] = array(
			'FROM'	=> array($this->steamprofile->get_table_name() => 'steam'),
			'ON'	=> 'u.user_steamid = steam.steam_steamid',
		);
		$event['sql_ary'] = $sql_ary;
	}

	/**
	 * Loads the Steam Status template variables for each post.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function viewtopic_modify_post_row(data $event)
	{
		if (!$this->config['stevotvr_steamstatus_show_on_viewtopic'] || empty($this->config['stevotvr_steamstatus_api_key']))
		{
			return;
		}

		$steamid = $event['user_poster_data']['steamid'];
		if (!empty($steamid))
		{
			if (!empty($event['user_poster_data']['steam_steamid']))
			{
				$cached = $this->steamprofile->get()->import($event['user_poster_data']);
				$event['post_row'] = array_merge($event['post_row'], array(
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
					$event['post_row'] = array_merge($event['post_row'], array(
						'S_STEAMSTATUS_LOADED'	=> true,

						'STEAMSTATUS_STATE'		=> $cached->get_state(),
						'STEAMSTATUS_STATUS'	=> $cached->get_localized_status(),
					));
				}

				return;
			}

			$event['post_row'] = array_merge($event['post_row'], array(
				'S_STEAMSTATUS_SHOW'	=> true,

				'STEAMSTATUS_STEAMID'	=> $steamid,

				'U_STEAMSTATUS_PROFILE'	=> 'http://steamcommunity.com/profiles/' . $steamid,
			));
		}
	}
}
