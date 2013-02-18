<?php
namespace Iresults\Core\Cache;

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