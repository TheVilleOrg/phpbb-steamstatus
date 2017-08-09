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
 * Steam Status interface interface for the steamprofile operator.
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
	 */
	public function get_from_api(array $steamids);

	/**
	 * Get cached Steam profile data from the database.
	 *
	 * @param string $steamid The SteamID for which to get profile data
	 *
	 * @return \stevotvr\steamstatus\entity\steamprofile|false False if there is no cached data
	 */
	public function get_from_cache($steamid);
}
