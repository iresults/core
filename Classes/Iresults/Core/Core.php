<?php
namespace Iresults\Core;

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
 * Base class for iresults classes.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 1.2.0
 */
abstract class Core implements \Iresults\Core\ObjectInterface {
	/**
	 * The delegate of this renderer.
	 * @var object
	 */
	protected $_delegate = NULL;

	const IR_METHOD_NOT_FOUND = '__ir_method_not_found_';

	const DEBUG_LEVEL_INFO = -1;
	const DEBUG_LEVEL_WARNING = 1;
	const DEBUG_LEVEL_CRITICAL = 2;

	const DEBUG_PRINT_LEVEL_OFF = 0;
	const DEBUG_PRINT_LEVEL_ALL = 1;
	const DEBUG_PRINT_LEVEL_CRITICAL = 2;

	static private $_debugLevel = 2;

	/**
	 * Tries to invoke a dynamically added method.
	 *
	 * @param	string	$name		The originally called method
	 * @param	array	$arguments	The arguments passed to the original method
	 * @return	mixed
	 * @throws	BadMethodCallException	If no dynamic method was found
	 */
	public function __call($name, array $arguments) {
		if (isset($this->$name) && is_callable($this->$name)) {
			$dynamicMethod = $this->$name;
			return call_user_func_array($dynamicMethod, $arguments);
		}
		throw \Iresults\Core\Exception\UndefinedMethod::exceptionWithMethod($name);
	}

	/**
	 * Triggered when invoking inaccessible methods in a static context.
	 *
	 * @param	string	$name      The name of the called function
	 * @param	array   $arguments An enumerated array containing the parameters passed to the inaccessible method
	 * @return	mixed    The return value
	 *
	 * @throws BadMethodCallException
	 */
	static public function __callStatic($name , array $arguments) {
		throw \Iresults\Core\Exception\UndefinedMethod::exceptionWithMethod($name);
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns a new instance of the class.
	 *
	 * @return	\Iresults\Core\Core
	 */
	static public function alloc() {
		return new static();
	}
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* DEBUGGING         MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
	 * @var mixed $var1
	 */
	public function pd($var1 = '__iresults_pd_noValue') {
		$args = func_get_args();
		return call_user_func_array(array('\Iresults\Core\Iresults','pd'),$args);
	}

	/**
	 * Prints a given message if the debug level is set to DEBUG_PRINT_LEVEL_ALL.
	 *
	 * @param	string|mixed $msg
	 */
	public function debug($msg,$level = -1) {
		if ($level == -1) $level = self::DEBUG_LEVEL_INFO;

		if (self::$_debugLevel == self::DEBUG_PRINT_LEVEL_ALL OR
		(self::$_debugLevel == self::DEBUG_PRINT_LEVEL_CRITICAL AND $level == self::DEBUG_LEVEL_CRITICAL)
		) {
			echo "<div class='iresults_debug'>$msg</div>";
		}
	}

	/**
	 * The magic __toString function
	 *
	 * @return	string
	 */
	public function __toString() {
		return $this->description();
	}

	/**
	 * Returns the default description.
	 *
	 * @return	string    A string describing this object
	 */
	public function description() {
		return Iresults::descriptionOfValue($this);
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* DELEGATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Tries to call a method on the delegate or, if the delegate doesn't respond,
	 * the method will be tried on $this.
	 *
	 * @param	string	$method The name of the method to invoke
	 * @param	array	$arguments	 Optional arguments to pass to the object
	 * @param	object	$object	 Optional object to be checked first
	 * @return	mixed|METHOD_NOT_FOUND
	 */
	protected function _callMethodIfExists($method, $arguments = array(), $object = NULL) {
		if ($object !== NULL && is_object($object)) {
			if (method_exists($object, $method)) {
				$arguments[] = $this;
				return call_user_func_array(array($object, $method), $arguments);
			}
		}
		if ($this->_delegate && is_object($this->_delegate) && method_exists($this->_delegate, $method)) {
			if (!is_array($arguments)) {
				throw new Exception('Passed delegation argument is not an array. It is of type ' . gettype($arguments));
			}
			$arguments[] = $this;
			return call_user_func_array(array($this->_delegate, $method), $arguments);
		}
		if (method_exists($this, $method)) {
			$arguments[] = $this;
			return call_user_func_array(array($this, $method), $arguments);
		}
		return self::IR_METHOD_NOT_FOUND;
	}
}