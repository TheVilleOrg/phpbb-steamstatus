<?php

namespace stevotvr\steamstatus\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use stevotvr\steamstatus\util\steamstatus;

class main_listener implements EventSubscriberInterface
{
	private $config;

	private $helper;

	private $template;

	private $language;

	private $request;

	private $user;

	private $db;

	private $cache;

	function __construct($config, $helper, $template, $language, $request, $user, $db, $cache)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->language = $language;
		$this->request = $request;
		$this->user = $user;
		$this->db = $db;
		$this->cache = $cache;
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
			'USER_STEAM_ID'				=> $this->user->data['user_steam_id'],
			'S_STEAMSTATUS_SHOW'		=> !empty($this->config['stevotvr_steamstatus_key']),
			'STEAMSTATUS_CONTROLLER'	=> $this->helper->route('stevotvr_steamstatus_route'),
			'STEAMSTATUS_STEAMID'		=> $this->user->data['user_steam_id'],
		));
	}

	public function validate_id($event)
	{
		if(empty($this->config['stevotvr_steamstatus_key']))
		{
			return;
		}

		$steam_id = $this->request->variable('steamstatus_steam_id', '0', false, \phpbb\request\request_interface::POST);
		if($steam_id !== '0')
		{
			$steam_id64 = null;
			$steam_error = 'ERROR_INVALID_FORMAT';
			$matches = array();
			if($steam_id === '')
			{
				$steam_id64 = '';
			}
			elseif(preg_match('/^STEAM_0:([0-1]):(\d+)$/', $steam_id, $matches) === 1)
			{
				$steam_id64 = self::add($matches[2] * 2 + $matches[1], '76561197960265728');
			}
			elseif(preg_match('/^\[?U:1:(\d+)\]?$/', $steam_id, $matches) === 1)
			{
				$steam_id64 = self::add($matches[1], '76561197960265728');
			}
			elseif(preg_match('/(?:steamcommunity.com\/profiles\/)?(\d{17})\/?$/', $steam_id, $matches) === 1)
			{
				$steam_id64 = $matches[1];
			}
			elseif(preg_match('/(?:steamcommunity.com\/id\/)?(\w+)\/?$/', $steam_id, $matches) === 1)
			{
				$query = http_build_query(array(
					'key'		=> $this->config['stevotvr_steamstatus_key'],
					'vanityurl'	=> $matches[1],
				));
				$url = 'https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?' . $query;
				$result = @file_get_contents($url);
				if($result)
				{
					$result = json_decode($result);
					if($result && $result->response && $result->response->success === 1)
					{
						$steam_id64 = $result->response->steamid;
					}
					else
					{
						$steam_error = 'ERROR_NAME_NOT_FOUND';
					}
				}
				else
				{
					$steam_error = 'ERROR_LOOKUP_FAILED';
				}
			}
			if($steam_id64 === null)
			{
				$error = $event['error'];
				$error[] = $steam_error;
				$event['error'] = $error;
			}
			else
			{
				$data = $event['data'];
				$data['steamstatus_steam_id'] = $steam_id64;
				$event['data'] = $data;
			}
		}
	}

	public function modify_id($event)
	{
		if(isset($event['data']['steamstatus_steam_id'])) {
			$sql_arr = array(
				'user_steam_id'	=> $event['data']['steamstatus_steam_id'],
			);
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $sql_arr) . '
					WHERE user_id = ' . $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
	}

	public function viewtopic_get_post_data($event)
	{
		$this->template->assign_var('STEAMSTATUS_CONTROLLER', $this->helper->route('stevotvr_steamstatus_route'));
	}

	public function viewtopic_cache_user_data($event)
	{
		$data = $event['user_cache_data'];
		$data['steamid'] = $event['row']['user_steam_id'];
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
