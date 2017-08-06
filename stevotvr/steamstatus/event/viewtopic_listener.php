<?php

namespace stevotvr\steamstatus\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use stevotvr\steamstatus\util\steamstatus;

class viewtopic_listener implements EventSubscriberInterface
{
	private $cache;

	private $helper;

	private $language;

	private $template;

	function __construct($cache, $helper, $language, $template)
	{
		$this->cache = $cache;
		$this->helper = $helper;
		$this->language = $language;
		$this->template = $template;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_get_post_data'				=> 'viewtopic_get_post_data',
			'core.viewtopic_cache_user_data'			=> 'viewtopic_cache_user_data',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_modify_post_row',
		);
	}

	public function viewtopic_get_post_data($event)
	{
		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->template->assign_var('U_STEAMSTATUS_CONTROLLER', $this->helper->route('stevotvr_steamstatus_route'));
	}

	public function viewtopic_cache_user_data($event)
	{
		$data = $event['user_cache_data'];
		$data['steamid'] = $event['row']['user_steamid'];
		$event['user_cache_data'] = $data;
	}

	public function viewtopic_modify_post_row($event)
	{
		$steamid = $event['user_poster_data']['steamid'];
		if (!empty($steamid))
		{
			list($profile_time, $profile) = steamstatus::get_from_cache($steamid, $this->cache);
			if ($profile)
			{
				$profile = steamstatus::get_localized_data($profile, $this->language);
				$event['post_row'] = array_merge($event['post_row'], array(
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
				$event['post_row'] = array_merge($event['post_row'], array(
					'STEAMSTATUS_STEAMID'	=> $steamid,
					'STEAMSTATUS_PROFILE'	=> 'http://steamcommunity.com/profiles/' . $steamid,
					'S_STEAMSTATUS_SHOW'	=> true,
				));
			}
		}
	}

	static private function add($left, $right)
	{
	    $left = str_pad($left, strlen($right), '0', STR_PAD_LEFT);
	    $right = str_pad($right, strlen($left), '0', STR_PAD_LEFT);

	    $carry = 0;
	    $result = '';
	    for ($i = strlen($left) - 1; $i >= 0; --$i)
	    {
	        $sum = $left[$i] + $right[$i] + $carry;
	        $carry = (int)($sum / 10);
	        $result .= $sum % 10;
	    }
	    if ($carry)
	    {
	        $result .= '1';
	    }

	    return strrev($result);
	}
}
