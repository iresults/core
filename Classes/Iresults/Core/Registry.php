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