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

use phpbb\request\request_interface;
use phpbb\user;
use stevotvr\steamstatus\operator\http_helper_interface;

/**
 * Steam Status openid operator for interaction with Steam OpenID.
 */
class openid implements openid_interface
{
	const OPENID_NS = 'http://specs.openid.net/auth/2.0';
	const OPENID_URL = 'https://steamcommunity.com/openid/';

	/**
	 * @var http_helper_interface
	 */
	protected $http_helper;

	/**
	 * @var request_interface
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
	 * @param http_helper_interface $http_helper
	 * @param request_interface     $request
	 * @param user                  $user
	 */
	public function __construct(http_helper_interface $http_helper, request_interface $request, user $user)
	{
		$this->http_helper = $http_helper;
		$this->request = $request;

		$this->trust_root = generate_board_url(true);
		$this->return_url = generate_board_url() . '/' .  $user->page['page'];
	}

	/**
	 * @inheritDoc
	 */
	public function set_return_url($url)
	{
		$this->return_url = $url;
	}

	/**
	 * @inheritDoc
	 */
	public function get_mode()
	{
		return $this->request->variable('openid_mode', '');
	}

	/**
	 * @inheritDoc
	 */
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

		return self::OPENID_URL . 'login?' . $params;
	}

	/**
	 * @inheritDoc
	 */
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

		$response = $this->http_helper->post(self::OPENID_URL . 'login', http_build_query($params));
		$valid = preg_match('/is_valid\s*:\s*true/i', $response);

		if ($valid)
		{
			$this->identity = $this->request->variable('openid_claimed_id', '');
		}

		return $valid;
	}

	/**
	 * @inheritDoc
	 */
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

	/**
	 * @inheritDoc
	 */
	public function get_error()
	{
		return $this->request->variable('openid_error', '');
	}
}
