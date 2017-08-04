<?php

namespace stevotvr\steamstatus\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	private $helper;

	function __construct($helper)
	{
		$this->helper = $helper;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.ucp_profile_modify_profile_info'		=> 'load_ucp_profile_language',
			'core.ucp_profile_validate_profile_info'	=> 'validate_id',
			'core.ucp_profile_info_modify_sql_ary'		=> 'modify_id',
		);
	}

	public function load_ucp_profile_language($event)
	{
		global $phpbb_container, $template, $user, $config;
		$language = $phpbb_container->get('language');
		$language->add_lang('ucp_profile', 'stevotvr/steamstatus');
		$template->assign_vars(array(
			'USER_STEAM_ID'				=> $user->data['user_steam_id'],
			'S_SHOW_STEAM_ID'			=> !empty($config['stevotvr_steamstatus_key']),
			'STEAMSTATUS_CONTROLLER'	=> $this->helper->route('stevotvr_steamstatus_route'),
			'status'					=> array(
				'NAME'		=> '',
				'GAMEID'	=> 0,
				'STATE'		=> 0,
				'STEAMID'	=> $user->data['user_steam_id'],
				'AVATAR'	=> '',
			),
		));
	}

	public function validate_id($event)
	{
		global $request, $config;

		if(empty($config['stevotvr_steamstatus_key']))
		{
			return;
		}

		$steam_id = trim($request->variable('steamstatus_steam_id', '0', false, \phpbb\request\request_interface::POST));
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
			elseif(preg_match('/(?:steamcommunity.com\/id\/)?(\w+)\/?$/', $steam_id, $matches) === 1 && $config['stevotvr_steamstatus_key'])
			{
				$query = http_build_query(array(
					'key'		=> $config['stevotvr_steamstatus_key'],
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
		global $user, $db;

		if(isset($event['data']['steamstatus_steam_id'])) {
			$sql_arr = array(
				'user_steam_id'	=> $event['data']['steamstatus_steam_id'],
			);
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $sql_arr) . '
					WHERE user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
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
