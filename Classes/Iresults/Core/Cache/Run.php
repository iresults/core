<?php
namespace Iresults\Core\Cache;

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
 * Run adapter for the cache. Which only provides caching for runtime.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 */
class Run extends \Iresults\Core\Cache\AbstractCache {
	/**
	 * @var array The cache array.
	 */
	static protected $_cache = array();

	/**
	 * Returns the object at the given key.
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function getObjectForKey($key) {
		if (array_key_exists($key,self::$_cache)) {
			return self::$_cache[$key];
		} else {
			return NULL;
		}
	}

	/**
	 * Stores the value of $object at the given key.
	 *
	 * @param	string	$key
	 * @param	mixed	$object
	 * @return	void
	 */
	public function setObjectForKey($key,$object) {
		self::$_cache[$key] = $object;
	}

	/**
	 * Removes the object with the given key.
	 *
	 * @param	string	$key
	 * @return	void
	 */
	public function removeObjectForKey($key) {
		if (array_key_exists($key,self::$_cache)) {
			unset(self::$_cache[$key]);
		}
	}

	/**
	 * Removes the complete cache.
	 *
	 * @return	void
	 */
	public function clear() {
		//unset(self::$_cache);
		self::$_cache = array();
		$this->pd("Cache cleared.");
	}

	/**
	 * Returns the scope of the cache.
	 *
	 * The Run cache is allways language dependent.
	 *
	 * @return	integer    The current scope as one of the SCOPE constants
	 */
	public function getScope() {
		return self::SCOPE_LANGUAGE;
	}
}