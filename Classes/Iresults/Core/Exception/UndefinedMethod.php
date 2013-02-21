<?php
namespace Iresults\Core\Exception;

/*
 * The MIT License (MIT)
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * The iresults Undefined Method exception.
 *
 * = Examples =
 * <code>
 * throw \Iresults\Core\Exception\UndefinedMethod::exceptionWithMethod('theUndefinedMethod', array('user information'));
 * </code>
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults
 */
class UndefinedMethod extends \Iresults\Core\Error {
	/**
	 * Exception message
	 */
	const MESSAGE = 'Call to undefined method %s';

	/**
	 * Constructs the Exception.
	 *
	 * @param string  	$message  The Exception message to throw
	 * @param integer 	$code     The Exception code
	 * @param Exception $previous The previous exception used for the exception chaining
	 */
	public function __construct($message = '', $code = 0, $previous = NULL) {
		if ($code === 0) {
			$code = 1346336887;
		}
		parent::__construct($message, $code, $previous);
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns a new UndefinedMethod exception with the given
	 * method name in the message.
	 *
	 * @param  string $method   The name of the undefined method
	 * @param  array  $userInfo An optional user info array
	 * @return \Iresults\Core\Exception\UndefinedMethod
	 */
	static public function exceptionWithMethod($method, $userInfo = array()) {
		$message = sprintf(self::MESSAGE, $method);
		$error = new static($message);
		$error->_setUserInfo($userInfo);
		return $error;
	}
}

?>
