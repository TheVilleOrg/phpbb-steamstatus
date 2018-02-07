<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus;

use phpbb\extension\base;

/**
 * Steam Status extension base.
 */
class ext extends base
{
	public function is_enableable()
	{
		return (bool) ini_get('allow_url_fopen');
	}
}
