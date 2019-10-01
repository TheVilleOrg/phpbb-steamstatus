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
 * Steam Status HTTP helper operator.
 */
class http_helper implements http_helper_interface
{
	/**
	 * The response code from the last request.
	 *
	 * @var int
	 */
	protected $response_code;

	/**
	 * @inheritDoc
	 */
	public function get($url)
	{
		return $this->request(false, $url, null);
	}

	/**
	 * @inheritDoc
	 */
	public function post($url, $body)
	{
		return $this->request(true, $url, $body);
	}

	/**
	 * @inheritDoc
	 */
	public function last_response_code()
	{
		return $this->response_code;
	}

	/**
	 * Make an HTTPS request to a remote URL.
	 *
	 * @param boolean $post True if this is a POST request
	 * @param string  $url  The URL for the request
	 * @param string  $body The body of the request
	 *
	 * @return string|boolean The response body, or FALSE on failure
	 */
	protected function request($post, $url, $body)
	{
		return function_exists('curl_init') ? $this->request_curl($post, $url, $body) : $this->request_fopen($post, $url, $body);
	}

	/**
	 * Make an HTTPS request to a remote URL using fopen.
	 *
	 * @param boolean $post True if this is a POST request
	 * @param string  $url  The URL for the request
	 * @param string  $body The body of the request
	 *
	 * @return string|boolean The response body, or FALSE on failure
	 */
	protected function request_fopen($post, $url, $body)
	{
		$ctx = array(
			'http' => array(
				'header'            => 'Connection: Close',
				'protocol_version'  => 1.1,
				'timeout'           => 30.0,
				'ignore_errors'     => true,
			),
		);

		if ($post)
		{
			$ctx['http']['method'] = 'POST';
			$ctx['http']['content'] = $body;
		}

		$fp = fopen($url, 'r', false, stream_context_create($ctx));

		if ($fp === false)
		{
			return false;
		}

		$meta = stream_get_meta_data($fp);
		$http_response = $meta['wrapper_data'][0];
		$this->response_code = (int) substr($http_response, strpos($http_response, ' ') + 1, 3);
		$response = stream_get_contents($fp);

		fclose($fp);

		return $response;
	}

	/**
	 * Make an HTTPS request to a remote URL using cURL.
	 *
	 * @param boolean $post True if this is a POST request
	 * @param string  $url  The URL for the request
	 * @param string  $body The body of the request
	 *
	 * @return string|boolean The response body, or FALSE on failure
	 */
	protected function request_curl($post, $url, $body)
	{
		$ch = curl_init($url);

		if ($ch === false)
		{
			return false;
		}

		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

		if ($post)
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}

		$response = curl_exec($ch);
		$this->response_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		return $response;
	}
}
