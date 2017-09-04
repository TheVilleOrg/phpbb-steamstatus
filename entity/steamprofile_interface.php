<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\entity;

/**
 * Steam Status interface for the steamprofile entity.
 */
interface steamprofile_interface
{
	/* Steam profile states */
	const STATE_OFFLINE = 0;
	const STATE_ONLINE = 1;
	const STATE_INGAME = 2;

	/**
	 * Load data from the database.
	 *
	 * @param string $steamid64 The SteamID64 for the profile to load
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\out_of_bounds    No data found
	 * @throws \stevotvr\steamstatus\exception\unexpected_value Invalid SteamID64
	 */
	public function load($steamid64);

	/**
	 * Import data from an array.
	 *
	 * @param array $data The data to import
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\invalid_argument A required field is missing
	 */
	public function import(array $data);

	/**
	 * Save the profile data to the database.
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\out_of_bounds No data is loaded
	 */
	public function save();

	/**
	 * @return string The SteamID64
	 */
	public function get_steamid();

	/**
	 * @param string $steamid The SteamID64
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\unexpected_value Invalid SteamID64
	 */
	public function set_steamid($steamid);

	/**
	 * @return int The timestamp of the last time the data was updated from the API
	 */
	public function get_querytime();

	/**
	 * @return boolean The data is older than the configured cache time
	 */
	public function is_stale();

	/**
	 * @param int $querytime The timestamp of the last time the data was updated from the API
	 *
	 * @return steamprofile_interface This object for chaining
	 */
	public function set_querytime($querytime);

	/**
	 * @return string The profile name
	 */
	public function get_name();

	/**
	 * @param string $name The profile name
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\unexpected_value Value is too long
	 */
	public function set_name($name);

	/**
	 * @return string The profile URL
	 */
	public function get_profile();

	/**
	 * @param string $profile The profile URL
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\unexpected_value Value is too long
	 */
	public function set_profile($profile);

	/**
	 * @return string The avatar URL
	 */
	public function get_avatar();

	/**
	 * @param string $avatar The avatar URL
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\unexpected_value Value is too long
	 */
	public function set_avatar($avatar);

	/**
	 * @return int The current state of the profile
	 */
	public function get_state();

	/**
	 * @param int $state The current state of the profile
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\out_of_bounds Unknown state
	 */
	public function set_state($state);

	/**
	 * @return string The current status of the profile
	 */
	public function get_status();

	/**
	 * @return string The current localized status of the profile
	 */
	public function get_localized_status();

	/**
	 * @param string $status The current status of the profile
	 *
	 * @return steamprofile_interface This object for chaining
	 *
	 * @throws \stevotvr\steamstatus\exception\unexpected_value Value is too long
	 */
	public function set_status($status);

	/**
	 * @return int The timestamp of the last time the profile logged off
	 */
	public function get_lastlogoff();

	/**
	 * @param int $lastonline The timestamp of the last time the profile logged off
	 *
	 * @return steamprofile_interface This object for chaining
	 */
	public function set_lastlogoff($lastlogoff);
}
