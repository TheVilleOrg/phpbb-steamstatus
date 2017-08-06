<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\controller;

use \stevotvr\steamstatus\util\steamstatus;
use \Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Steam Status main controller for getting the current Steam profile status of a list of SteamIDs
 * in JSON format.
 */
class main
{
	/* @var \phpbb\cache\service */
	private $cache;

	/* @var \phpbb\config\config */
	private $config;

	/* @var \phpbb\language\language */
	private $language;

	/* @var \phpbb\request\request */
	private $request;

	/**
	 * @param \phpbb\cache\service		$cache
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\language\language	$language
	 * @param \phpbb\request\request	$request
	 */
	public function __construct(\phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\language\language $language, \phpbb\request\request $request)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->language = $language;
		$this->request = $request;

		$language->add_lang('common', 'stevotvr/steamstatus');
	}

	/**
	 * Handle the /steamstatus route.
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse	The response object
	 */
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
			$output = steamstatus::get_from_api($api_key, $steamids, $this->cache);
		}

		foreach ($output as &$profile)
		{
			$profile = steamstatus::get_localized_data($profile, $this->language);
		}

		return new JsonResponse(array('status' => $output));
	}

	/**
	 * Get a list of valid SteamID64s from a list of strings.
	 *
	 * @param array	$unsafe	An array of strings
	 *
	 * @return array		An array of valid SteamID64 strings
	 */
	static private function get_valid_ids(array $unsafe)
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
