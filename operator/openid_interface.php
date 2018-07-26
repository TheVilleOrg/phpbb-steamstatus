<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\operator;

/**
 * Steam Status interface for the openid operator.
 */
interface openid_interface
{
	/**
	 * @param string $url The URL to return to from the Steam site
	 */
	public function set_return_url($url);

	/**
	 * @return string The current mode of the client
	 */
	public function get_mode();

	/**
	 * @return string The authentication URL
	 */
	public function get_url();

	/**
	 * Validate the account authentication attempt.
	 *
	 * @return boolean The account is valid
	 */
	public function validate();

	/**
	 * @return string The 64 bit SteamID
	 */
	public function get_id();

	/**
	 * @return string The authentication error
	 */
	public function get_error();
}
