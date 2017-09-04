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

use phpbb\config\config;
use phpbb\language\language;
use phpbb\request\request_interface;
use stevotvr\steamstatus\operator\steamprofile_interface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Steam Status main controller for getting the current Steam profile status of a list of SteamIDs
 * in JSON format.
 */
class main
{
	/**
	 * @var \phpbb\config\config
	 */
	private $config;

	/**
	 * @var \phpbb\language\language
	 */
	private $language;

	/**
	 * @var \phpbb\request\request_interface
	 */
	private $request;

	/**
	 * @var \stevotvr\steamstatus\operator\steamprofile_interface
	 */
	private $steamprofile;

	/**
	 * @param \phpbb\config\config                                  $config
	 * @param \phpbb\language\language                              $language
	 * @param \phpbb\request\request_interface                      $request
	 * @param \stevotvr\steamstatus\operator\steamprofile_interface $steamprofile
	 */
	public function __construct(config $config, language $language, request_interface $request, steamprofile_interface $steamprofile)
	{
		$this->config = $config;
		$this->language = $language;
		$this->request = $request;
		$this->steamprofile = $steamprofile;

		$language->add_lang('common', 'stevotvr/steamstatus');
	}

	/**
	 * Handle the /steamstatus route.
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse The response object
	 */
	public function handle()
	{
		$api_key = $this->config['stevotvr_steamstatus_api_key'];
		if (empty($api_key))
		{
			return new JsonResponse(null, 500);
		}

		$profiles = array();
		$steamids = $this->request->variable('steamids', '', false, request_interface::GET);
		if (!empty($steamids))
		{
			$steamids = array_unique(array_map('trim', explode(',', $steamids)));
			$profiles = $this->steamprofile->get_from_api($steamids);
		}

		$output = array();
		foreach ($profiles as $profile)
		{
			$output[] = array(
				'steamid'		=> $profile->get_steamid(),
				'name'			=> $profile->get_name(),
				'profile'		=> $profile->get_profile(),
				'avatar'		=> $profile->get_avatar(),
				'state'			=> $profile->get_state(),
				'status'		=> $profile->get_localized_status(),
				'lastlogoff'	=> $profile->get_lastlogoff(),
			);
		}

		return new JsonResponse(array('status' => $output));
	}
}
