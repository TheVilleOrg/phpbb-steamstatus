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

use phpbb\config\config;
use phpbb\request\request_interface;
use phpbb\user;

/**
 * Steam Status openid operator for interaction with Steam OpenID.
 */
class openid implements openid_interface
{
	const OPENID_NS = 'http://specs.openid.net/auth/2.0';
	const OPENID_URL = '://steamcommunity.com/openid/';

	/**
	 * @var \phpbb\request\request_interface
	 */
	protected $request;

	/**
	 * The root URL of the server.
	 *
	 * @var string
	 */
	protected $trust_root;

	/**
	 * The identity string returned by the Steam OpenID provider.
	 *
	 * @var string
	 */
	protected $identity;

	/**
	 * The URL to return to.
	 *
	 * @var string
	 */
	protected $return_url;

	/**
	 * The protocol to use for communicating with Steam.
	 *
	 * @var string
	 */
	private $protocol;

	/**
	 * @param \phpbb\config\config             $config
	 * @param \phpbb\request\request_interface $request
	 * @param \phpbb\user                      $user
	 */
	public function __construct(config $config, request_interface $request, user $user)
	{
		$this->request = $request;

		$this->trust_root = $config['server_protocol'] . $config['server_name'];
		$this->return_url = generate_board_url() . '/' .  $user->page['page'];
		$this->protocol = $config['stevotvr_steamstatus_https'] && in_array('https', stream_get_wrappers()) ? 'https' : 'http';
	}

	public function set_return_url($url)
	{
		$this->return_url = $url;
	}

	public function get_mode()
	{
		return $this->request->variable('openid_mode', '');
	}

	public function get_url()
	{
		$params = http_build_query(array(
			'openid.ns'			=> self::OPENID_NS,
			'openid.mode'		=> 'checkid_setup',
			'openid.return_to'	=> $this->return_url,
			'openid.realm'		=> $this->trust_root,
			'openid.identity'	=> self::OPENID_NS . '/identifier_select',
			'openid.claimed_id'	=> self::OPENID_NS . '/identifier_select',
		));

		return 'https' . self::OPENID_URL . 'login?' . $params;
	}

	public function validate()
	{
		$this->identity = null;

		$signed = $this->request->raw_variable('openid_signed', '');
		$params = array(
			'openid.ns'				=> self::OPENID_NS,
			'openid.mode'			=> 'check_authentication',
			'openid.assoc_handle'	=> $this->request->raw_variable('openid_assoc_handle', ''),
			'openid.signed'			=> $signed,
			'openid.sig'			=> $this->request->raw_variable('openid_sig', ''),
		);
		foreach (explode(',', $signed) as $item)
		{
			$params['openid.' . $item] = $this->request->raw_variable('openid_' . $item, '');
		}

		$ctx = stream_context_create(array(
			'http'	=> array(
				'method'		=> 'POST',
				'header'		=> 'Content-type: application/x-www-form-urlencoded',
				'content'		=> http_build_query($params),
				'ignore_errors'	=> true,
			),
		));
		$response = @file_get_contents($this->protocol . self::OPENID_URL . 'login', false, $ctx);

		$valid = preg_match('/is_valid\s*:\s*true/i', $response);

		if ($valid)
		{
			$this->identity = $this->request->variable('openid_claimed_id', '');
		}

		return $valid;
	}

	public function get_id()
	{
		if (!$this->identity)
		{
			return '';
		}

		if (preg_match('/steamcommunity.com\/openid\/id\/(\d+)\/?$/', $this->identity, $matches) === 1)
		{
			return $matches[1];
		}

		return '';
	}

	public function get_error()
	{
		return $this->request->variable('openid_error', '');
	}
}
