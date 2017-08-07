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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use stevotvr\steamstatus\util\steamstatus;

class memberlist_listener implements EventSubscriberInterface
{
	/* @var \phpbb\cache\service */
	private $cache;

	/* @var \phpbb\config\config */
	private $config;

	/* @var \phpbb\controller\helper */
	private $helper;

	/* @var \phpbb\language\language */
	private $language;

	/* @var \phpbb\template\template */
	private $template;

	/**
	 * @param \phpbb\cache\service		$cache
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\controller\helper	$helper
	 * @param \phpbb\language\language	$language
	 * @param \phpbb\template\template	$template
	 */
	function __construct(\phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\template\template $template)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->helper = $helper;
		$this->language = $language;
		$this->template = $template;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.memberlist_view_profile'	=> 'memberlist_view_profile',
		);
	}

	/**
	 * Loads the Steam Status template variables for the user profile.
	 *
	 * @param \phpbb\event\data	$event
	 */
	public function memberlist_view_profile(\phpbb\event\data $event)
	{
		if (!$this->config['stevotvr_steamstatus_show_on_profile']) {
			return;
		}

		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->template->assign_var('U_STEAMSTATUS_CONTROLLER', $this->helper->route('stevotvr_steamstatus_route'));

		$steamid = $event['member']['user_steamid'];
		if (!empty($steamid))
		{
			list($profile_time, $profile) = steamstatus::get_from_cache($steamid, $this->cache);
			if ($profile)
			{
				$profile = steamstatus::get_localized_data($profile, $this->language);
				$this->template->assign_vars(array(
					'STEAMSTATUS_STEAMID'	=> $steamid,
					'STEAMSTATUS_NAME'		=> $profile['name'],
					'STEAMSTATUS_PROFILE'	=> $profile['profile'],
					'STEAMSTATUS_AVATAR'	=> $profile['avatar'],
					'STEAMSTATUS_STATE'		=> $profile['state'],
					'STEAMSTATUS_STATUS'	=> $profile['status'],
					'S_STEAMSTATUS_SHOW'	=> true,
					'S_STEAMSTATUS_LOADED'	=> true,
				));
			}
			else
			{
				$this->template->assign_vars(array(
					'STEAMSTATUS_STEAMID'	=> $steamid,
					'STEAMSTATUS_PROFILE'	=> 'http://steamcommunity.com/profiles/' . $steamid,
					'S_STEAMSTATUS_SHOW'	=> true,
				));
			}
		}
	}
}
