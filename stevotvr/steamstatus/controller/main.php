<?php

namespace stevotvr\steamstatus\controller;

use \Symfony\Component\HttpFoundation\JsonResponse;
use \stevotvr\steamstatus\util\steamstatus;

class main
{
	private $config;

	private $request;

	private $cache;

	private $language;

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
					$cached = steamstatus::get_from_cache($id, $this->cache);
					if($cached)
					{
						$output[] = $cached;
					}
					else
					{
						$stale[] = $id;
					}
				}
				steamstatus::get_from_api($key, $stale, $output, $this->cache);
			}
		}

		foreach($output as &$user)
		{
			$user = steamstatus::get_localized_data($user, $this->language);
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
}
