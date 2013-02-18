<?php
namespace Iresults\Core\Exception;

/*
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *             Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
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