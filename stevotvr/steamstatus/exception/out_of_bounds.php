<?php
/**
 *
 * Steam Status. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\steamstatus\exception;

/**
* Exception thrown upon an out-of-bounds error.
*/
class out_of_bounds extends \Exception
{
	/**
	 * @param string     $argument_name The name of the argument
	 * @param mixed      $value         The value of the argument
	 * @param int        $code          The Exception code
	 * @param \Throwable $previous      The previous exception used for the exception chaining
	 */
	function __construct($argument_name, $value = '[no_value]', $code = 0, \Throwable $previous = null)
	{
		$message = 'Out-of-bounds value for "' . (string) $argument_name . '": "' . (string) $value . '"';
		parent::__construct($message, $code, $previous);
	}
}
