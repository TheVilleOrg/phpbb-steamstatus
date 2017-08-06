<?php

namespace stevotvr\steamstatus\event;

use stevotvr\steamstatus\util\steamstatus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ucp_listener implements EventSubscriberInterface
{
	private $config;

	private $db;

	private $language;

	private $request;

	private $template;

	private $user;

	function __construct(\phpbb\config\config $config, \phpbb\db\driver\factory $db, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
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

	public function ucp_profile_modify_profile_info(\phpbb\event\data $event)
	{
		$this->language->add_lang('common', 'stevotvr/steamstatus');
		$this->language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$this->template->assign_vars(array(
			'STEAMSTATUS_STEAMID'	=> $this->user->data['user_steamid'],
			'S_STEAMSTATUS_SHOW'	=> !empty($this->config['stevotvr_steamstatus_api_key']),
		));
	}

	public function ucp_profile_validate_profile_info(\phpbb\event\data $event)
	{
		$api_key = $this->config['stevotvr_steamstatus_api_key'];
		if (empty($api_key))
		{
			return;
		}

		$steamid = $this->request->variable('steamstatus_steamid', '0', false, \phpbb\request\request_interface::POST);
		if ($steamid !== '0')
		{
			$steamid64 = null;
			$steam_error = 'STEAMSTATUS_ERROR_INVALID_FORMAT';
			$matches = array();
			if ($steamid === '')
			{
				$steamid64 = '';
			}
			else if (preg_match('/^STEAM_0:([0-1]):(\d+)$/', $steamid, $matches) === 1)
			{
				$steamid64 = self::add($matches[2] * 2 + $matches[1], '76561197960265728');
			}
			else if (preg_match('/^\[?U:1:(\d+)\]?$/', $steamid, $matches) === 1)
			{
				$steamid64 = self::add($matches[1], '76561197960265728');
			}
			else if (preg_match('/(?:steamcommunity.com\/profiles\/)?(\d{17})\/?$/', $steamid, $matches) === 1)
			{
				$steamid64 = $matches[1];
			}
			else if (preg_match('/(?:steamcommunity.com\/id\/)?(\w+)\/?$/', $steamid, $matches) === 1)
			{
				$query = http_build_query(array(
					'key'		=> $api_key,
					'vanityurl'	=> $matches[1],
				));
				$url = 'https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?' . $query;
				$result = @file_get_contents($url);
				if ($result)
				{
					$result = json_decode($result);
					if ($result && $result->response && $result->response->success === 1)
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

	public function ucp_profile_info_modify_sql_ary(\phpbb\event\data $event)
	{
		if (isset($event['data']['steamstatus_steamid'])) {
			$sql_arr = array(
				'user_steamid'	=> $event['data']['steamstatus_steamid'],
			);
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $sql_arr) . '
					WHERE user_id = ' . $this->user->data['user_id'];
			$this->db->sql_query($sql);
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
