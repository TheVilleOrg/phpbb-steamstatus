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
use stevotvr\steamstatus\lib\LightOpenID;

/**
 * Steam Status openid operator for interaction with Steam OpenID.
 */
class openid implements openid_interface
{
	/**
	 * @var \phpbb\request\request_interface
	 */
	private $request;

	/**
	 * @var \stevotvr\steamstatus\lib\LightOpenID
	 */
	private $openid;

	/**
	 * @param \phpbb\request\request_interface $request
	 * @param \phpbb\user                      $user
	 */
	public function __construct(request_interface $request, user $user)
	{
		$this->request = $request;

		$this->init(generate_board_url() . '/' .  $user->page['page']);
	}

	public function init($url)
	{
		return $this->wrap('_init', $url);
	}

	public function get_mode()
	{
		return $this->openid->mode;
	}

	public function get_url()
	{
		return $this->wrap('_get_url');
	}

	public function validate()
	{
		return $this->wrap('_validate');
	}

	public function get_id()
	{
		$id = $this->openid->identity;
		if (preg_match('/steamcommunity.com\/openid\/id\/(\d+)\/?$/', $id, $matches) === 1)
		{
			return $matches[1];
		}

		return '';
	}

	/**
	 * Internal init.
	 *
	 * @param string $url The return URL
	 */
	private function _init($url)
	{
		$this->openid = new LightOpenID($url);
		$this->openid->identity = 'https://steamcommunity.com/openid/?l=english';
	}

	/**
	 * Internal get_url.
	 *
	 * @return string The authentication URL
	 */
	private function _get_url()
	{
		return $this->openid->authUrl();
	}

	/**
	 * Internal validate.
	 *
	 * @return boolean The account is valid
	 */
	private function _validate()
	{
		return $this->openid->validate();
	}

	/**
	 * Wrapper for methods to be run with super globals enabled.
	 *
	 * @param string $fn      The name of the method to run
	 * @param mixed  $arg,... Arguments to pass
	 *
	 * @return mixed Return value if any
	 */
	private function wrap($fn)
	{
		$superglobals_disabled = $this->request->super_globals_disabled();
		$this->request->enable_super_globals();

		$params = func_get_args();
		array_shift($params);
		$ret = call_user_func_array(array($this, $fn), $params);

		if ($superglobals_disabled)
		{
			$this->request->disable_super_globals();
		}

		return $ret;
	}
}
