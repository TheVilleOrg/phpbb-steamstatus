<?php

namespace stevotvr\steamstatus\controller;

use \Symfony\Component\HttpFoundation\JsonResponse;

class main
{
	private $config;

	private $request;

	private $cache;

	private $language;

	static private $status_text = array(
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
		$this->language = $language;

		$language->add_lang('common', 'stevotvr/steamstatus');
	}

	public function handle()
	{
		$key = $this->config['stevotvr_steamstatus_key'];
		if(empty($key))
		{
			return new JsonResponse(null, 500);
		}

		$output = array();
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
						$output[] = $cached;
					}
					else
					{
						$stale[] = $id;
					}
				}
				self::get_from_api($key, $stale, $output);
			}
		}

		foreach($output as &$user)
		{
			$user = $user['data'];
			if($user['state'] < 2)
			{
				$user['status'] = $this->language->lang($user['status']);
			}
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
					$now = time();
					foreach($response->response->players as $player)
					{
						$user = array(
							'time'	=> $now,
							'data'	=> array(
								'steamid'		=> $player->steamid,
								'name'			=> $player->personaname,
								'profile'		=> $player->profileurl,
								'avatar'		=> $player->avatar,
								'state'			=> self::get_profile_state($player),
								'status'		=> self::get_profile_status($player),
								'lastlogoff'	=> $player->lastlogoff,
							),
						);
						$this->cache->put('stevotvr_steamstatus_id' . $player->steamid, $user, 5);
						$results[] = $user;
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

	static private function get_profile_status($user)
	{
		if(!empty($user->gameextrainfo))
		{
			return $user->gameextrainfo;
		}
		return self::$status_text[$user->personastate];
	}
}
