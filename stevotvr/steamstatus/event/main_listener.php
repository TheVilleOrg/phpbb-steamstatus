<?php

namespace stevotvr\steamstatus\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use stevotvr\steamstatus\util\steamstatus;

class main_listener implements EventSubscriberInterface
{
	private $cache;

	private $config;

	private $db;

	private $helper;

	private $language;

	private $request;

	private $template;

	private $user;

	function __construct($cache, $config, $db, $helper, $language, $request, $template, $user)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.ucp_profile_modify_profile_info'		=> 'load_ucp_profile_language',
			'core.ucp_profile_validate_profile_info'	=> 'validate_id',
			'core.ucp_profile_info_modify_sql_ary'		=> 'modify_id',
			'core.viewtopic_get_post_data'				=> 'viewtopic_get_post_data',
			'core.viewtopic_cache_user_data'			=> 'viewtopic_cache_user_data',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_modify_post_row',
		);
	}

	public function load_ucp_profile_language($event)
	{
		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'STEAMSTATUS_STEAMID'	=> $this->user->data['user_steamid'],
			'S_STEAMSTATUS_SHOW'	=> !empty($this->config['stevotvr_steamstatus_api_key']),
		));
	}

	public function validate_id($event)
	{
		$api_key = $this->config['stevotvr_steamstatus_api_key'];
		if(empty($api_key))
		{
			return;
		}

		$steamid = $this->request->variable('steamstatus_steamid', '0', false, \phpbb\request\request_interface::POST);
		if($steamid !== '0')
		{
			$steamid64 = null;
			$steam_error = 'STEAMSTATUS_ERROR_INVALID_FORMAT';
			$matches = array();
			if($steamid === '')
			{
				$steamid64 = '';
			}
			elseif(preg_match('/^STEAM_0:([0-1]):(\d+)$/', $steamid, $matches) === 1)
			{
				$steamid64 = self::add($matches[2] * 2 + $matches[1], '76561197960265728');
			}
			elseif(preg_match('/^\[?U:1:(\d+)\]?$/', $steamid, $matches) === 1)
			{
				$steamid64 = self::add($matches[1], '76561197960265728');
			}
			elseif(preg_match('/(?:steamcommunity.com\/profiles\/)?(\d{17})\/?$/', $steamid, $matches) === 1)
			{
				$steamid64 = $matches[1];
			}
			elseif(preg_match('/(?:steamcommunity.com\/id\/)?(\w+)\/?$/', $steamid, $matches) === 1)
			{
				$query = http_build_query(array(
					'key'		=> $api_key,
					'vanityurl'	=> $matches[1],
				));
				$url = 'https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?' . $query;
				$result = @file_get_contents($url);
				if($result)
				{
					$result = json_decode($result);
					if($result && $result->response && $result->response->success === 1)
					{
						$steamid64 = $result->response->steamid;
					}
					else
					{
						$steam_error = 'STEAMSTATUS_ERROR_NAME_NOT_FOUND';
					}
				}
				else
				{
					$steam_error = 'STEAMSTATUS_ERROR_LOOKUP_FAILED';
				}
			}
			if(!isset($steamid64))
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

	public function modify_id($event)
	{
		if(isset($event['data']['steamstatus_steamid'])) {
			$sql_arr = array(
				'user_steamid'	=> $event['data']['steamstatus_steamid'],
			);
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $sql_arr) . '
					WHERE user_id = ' . $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
	}

	public function viewtopic_get_post_data($event)
	{
		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->template->assign_var('STEAMSTATUS_CONTROLLER', $this->helper->route('stevotvr_steamstatus_route'));
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
		if(!empty($steamid))
		{
			$status = steamstatus::get_from_cache($steamid, $this->cache);
			if($status)
			{
				$status = steamstatus::get_localized_data($status, $this->language);
				$event['post_row'] = array_merge($event['post_row'], array(
					'STEAMSTATUS_STEAMID'	=> $steamid,
					'STEAMSTATUS_NAME'		=> $status['name'],
					'STEAMSTATUS_PROFILE'	=> $status['profile'],
					'STEAMSTATUS_AVATAR'	=> $status['avatar'],
					'STEAMSTATUS_STATE'		=> $status['state'],
					'STEAMSTATUS_STATUS'	=> $status['status'],
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
	    for($i = strlen($left) - 1; $i >= 0; --$i)
	    {
	        $sum = $left[$i] + $right[$i] + $carry;
	        $carry = (int)($sum / 10);
	        $result .= $sum % 10;
	    }
	    if($carry)
	    {
	        $result .= '1';
	    }

	    return strrev($result);
	}
}
