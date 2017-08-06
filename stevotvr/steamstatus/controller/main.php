<?php

namespace stevotvr\steamstatus\controller;

use \Symfony\Component\HttpFoundation\JsonResponse;
use \stevotvr\steamstatus\util\steamstatus;

class main
{
	private $cache;

	private $config;

	private $language;

	private $request;

	public function __construct($cache, $config, $language, $request)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->language = $language;
		$this->request = $request;

		$language->add_lang('common', 'stevotvr/steamstatus');
	}

	public function handle()
	{
		$api_key = $this->config['stevotvr_steamstatus_api_key'];
		if (empty($api_key))
		{
			return new JsonResponse(null, 500);
		}

		$output = array();
		$steamids = $this->request->variable('steamids', '', false, \phpbb\request\request_interface::GET);
		if (!empty($steamids))
		{
			$steamids = array_unique(array_map('trim', explode(',', $steamids)));
			$steamids = self::get_valid_ids($steamids);
			$stale = array();
			foreach ($steamids as $steamid)
			{
				$cached = steamstatus::get_from_cache($steamid, $this->cache);
				if ($cached)
				{
					$output[] = $cached['data'];
				}
				else
				{
					$stale[] = $steamid;
				}
			}
			steamstatus::get_from_api($api_key, $stale, $output, $this->cache);
		}

		foreach ($output as &$profile)
		{
			$profile = steamstatus::get_localized_data($profile, $this->language);
		}

		return new JsonResponse(array('status' => $output));
	}

	static private function get_valid_ids($unsafe)
	{
		$safe = array();
		foreach ($unsafe as $steamid)
		{
			if (preg_match('/^\d{17}$/', $steamid))
			{
				$safe[] = $steamid;
			}
		}
		return $safe;
	}
}
