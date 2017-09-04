<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\operator;

/**
 * Steam Status interface for the steamprofile operator.
 */
interface steamprofile_interface
{
	/**
	 * @return string The name of the database table storing Steam profiles
	 */
	public function get_table_name();

	/**
	 * Get a new instance of a steamprofile entity.
	 *
	 * @return \stevotvr\steamstatus\entity\steamprofile
	 */
	public function get();

	/**
	 * Get Steam profile data from the Steam Web API.
	 *
	 * @param array $steamids The list of SteamIDs for which to get profile data
	 *
	 * @return array An array of \stevotvr\steamstatus\entity\steamprofile objects
	 *
	 * @throws \stevotvr\steamstatus\exception\out_of_bounds No API key is configured
	 */
	public function get_from_api(array $steamids);

	/**
	 * Get cached Steam profile data from the database.
	 *
	 * @param string $steamid The SteamID for which to get profile data
	 *
	 * @return \stevotvr\steamstatus\entity\steamprofile|boolean False if there is no cached data
	 */
	public function get_from_cache($steamid);

	/**
	 * Convert a SteamID from any format to the SteamID64 format.
	 *
	 * @param string $steamid The string to convert
	 * @param string &$error  A variable to hold any error string
	 *
	 * @return string The SteamID64
	 *
	 * @throws \stevotvr\steamstatus\exception\out_of_bounds No API key is configured for vanity
	 *                                                       URL lookups
	 */
	public function to_steamid64($steamid, &$error);
}
