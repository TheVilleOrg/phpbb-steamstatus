<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\operator;

/**
 * Steam Status HTTP helper operator interface.
 */
interface http_helper_interface
{
	/**
	 * Make an HTTPS GET request to a remote URL.
	 *
	 * @param string $url The URL for the request
	 *
	 * @return string|boolean The response body, or FALSE on failure
	 */
	public function get($url);

	/**
	 * Make an HTTPS POST request to a remote URL.
	 *
	 * @param string $url  The URL for the request
	 * @param string $body The body of the request
	 *
	 * @return string|boolean The response body, or FALSE on failure
	 */
	public function post($url, $body);

	/**
	 * Get the response code from the last request.
	 *
	 * @return int The response code
	 */
	public function last_response_code();
}
