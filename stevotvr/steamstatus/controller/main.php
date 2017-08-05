<?php

namespace stevotvr\steamstatus\controller;

use \Symfony\Component\HttpFoundation\JsonResponse;

class main
{
	private $config;

	private $request;

	private $cache;

	private $status_text = array(
		'STATUS_OFFLINE',
		'STATUS_ONLINE',
		'STATUS_BUSY',
		'STATUS_AWAY',
		'STATUS_SNOOZE',
		'STATUS_LTT',
		'STATUS_LTP',
	);

	public function __construct($config, $request, $cache, $language)
	{
		$this->config = $config;
		$this->request = $request;
		$this->cache = $cache;

		$language->add_lang('common', 'stevotvr/steamstatus');
		foreach($this->status_text as &$status)
		{
			$status = $language->lang($status);
		}
	}

	public function handle()
	{
		$key = $this->config['stevotvr_steamstatus_key'];
		if(empty($key))
		{
			return new JsonResponse(null, 500);
		}

		$status = array();
		$input = $this->request->raw_variable('list', '', \phpbb\request\request_interface::GET);
		if($input)
		{
			$input = json_decode($input);
			if($input && is_array($input->ids))
			{
				$ids = self::get_valid_ids($input->ids);
				$stale = array();
				foreach($ids as $id)
				{
					$cached = self::get_from_cache($id);
					if($cached)
					{
						$status[] = $cached;
					}
					else
					{
						$stale[] = $id;
					}
				}
				self::get_from_api($key, $stale, $status);
			}
		}

		$output = array();
		foreach($status as $user)
		{
			$output[] = array(
				'steamid'		=> $user->steamid,
				'name'			=> $user->personaname,
				'profile'		=> $user->profileurl,
				'avatar'		=> $user->avatar,
				'state'			=> self::get_profile_state($user),
				'status'		=> $this->get_profile_status($user),
				'lastlogoff'	=> $user->lastlogoff,
			);
		}

		return new JsonResponse(array('status' => $output));
	}

	static private function get_valid_ids($unsafe)
	{
		$safe = array();
		foreach($unsafe as $id)
		{
			$id = trim($id);
			if(preg_match('/^\d{17}$/', $id))
			{
				$safe[] = $id;
			}
		}
		return $safe;
	}

	private function get_from_cache($id)
	{
		return $this->cache->get('stevotvr_steamstatus_id' . $id);
	}

	private function get_from_api($key, $ids, &$results)
	{
		if(empty($ids))
		{
			return;
		}

		$ids = array_chunk($ids, 100);
		foreach($ids as $chunk)
		{
			$query = http_build_query(array(
				'key'		=> $key,
				'steamids'	=> implode(',', $chunk),
			));
			$url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?' . $query;
			$response = @file_get_contents($url);
			if($response)
			{
				$response = json_decode($response);
				if($response && $response->response && is_array($response->response->players))
				{
					foreach($response->response->players as $player)
					{
						$results[] = $player;
						$this->cache->put('stevotvr_steamstatus_id' . $player->steamid, $player, 5);
					}
				}
			}
		}
	}

	static private function get_profile_state($user)
	{
		if(!empty($user->gameextrainfo))
		{
			return 2;
		}
		if($user->personastate > 0)
		{
			return 1;
		}
		return 0;
	}

	private function get_profile_status($user)
	{
		if(!empty($user->gameextrainfo))
		{
			return $user->gameextrainfo;
		}
		return $this->status_text[$user->personastate];
	}
}
