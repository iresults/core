<?php
namespace Iresults\Core;

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
 * The iresults registry.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 */
class Registry extends \Iresults\Core\Singleton implements \Iresults\Core\KVCInterface {
	/**
	 * The storage for the registered data.
	 * @var array
	 */
	static protected $_internalStorage = array();

	/**
	 * Returns the object at the given key.
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function getObjectForKey($key) {
		if (isset(self::$_internalStorage[$key])) {
			return self::$_internalStorage[$key];
		}
		return NULL;
	}

	/**
	 * Stores the value of $object at the given key.
	 *
	 * @param	string	$key
	 * @param	mixed	$object
	 * @return	void
	 */
	public function setObjectForKey($key,$object) {
		self::$_internalStorage[$key] = $object;
	}

	/**
	 * Removes the object with the given key.
	 *
	 * @param	string	$key
	 * @return	void
	 */
	public function removeObjectForKey($key) {
		unset(self::$_internalStorage[$key]);
	}

	/**
	 * If only one parameter is passed, the registry will be searched for that
	 * key, if two arguments are given the first one will be used as key and the
	 * second one will be assigned for it.
	 *
	 * @param	string	$key    The key to fetch/set
	 * @param	mixed	$object	 The value/object to set
	 * @return	mixed    Returns the object for the given key
	 */
	static public function registry($key, $object = NULL) {
		if (func_num_args() > 1) {
			self::$_internalStorage[$key] = $object;
			return $object;
		}

		if (isset(self::$_internalStorage[$key])) {
			return self::$_internalStorage[$key];
		}
		return NULL;
	}

	/**
	 * The registry must not be serialized.
	 *
	 * @return	void
	 */
	public function __sleep() {
		throw new Exception("The registry must not be serialized.",1314028891);
	}
}
