<?php
namespace Iresults\Core\Mutable;

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
 * The active mutable class automatically calls the given callbacks or delegate
 * methods if a property is set the first time (it is created) or unset/removed.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class Active extends \Iresults\Core\Mutable\AutoExpanding {
	/**
	 * The callback that is invoked when a node is created.
	 *
	 * @var array<string>
	 */
	protected $createCallback;

	/**
	 * The callback that is invoked when a node is removed.
	 *
	 * @var array<string>
	 */
	protected $removeCallback;

	/**
	 * Sets a properties data.
	 *
	 * @param	string	$key
	 * @return	void
	 */
	public function __set($key, $value) {
		if (!isset($this->_data[$key])) {
			$this->createProperty($key, $value);
		}
		parent::__set($key, $value);
	}

	/**
	 * Returns a properties data.
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function __get($key) {
		if (isset($this->_data[$key])) {
			return $this->_data[$key];
		} else {
			return NULL;
		}
	}

	/**
	 * Deletes a property.
	 *
	 * Invoked when unset() is used on inaccessible properties.
	 *
	 * @param	string	$key The property to delete
	 * @return	void
	 */
	public function __unset($key) {
		if (isset($this->_data[$key])) {
			$this->removeProperty($key);
			unset($this->_data[$key]);
		}
	}

	/**
	 * Create a new property with the given key and value.
	 *
	 * @param	string	$key   The key for the property which should be created
	 * @param	mixed	$value The value for the new property
	 * @return	mixed    Returns the result from the delegated calls
	 */
	public function createProperty($key, $value) {
		if ($this->createCallback) {
			$arguments = array($key, $value, $this);
			return call_user_func_array($this->createCallback, $arguments);
		} else {
			$result = $this->_callMethodIfExists("createCallback");
			if ($result !== self::IR_METHOD_NOT_FOUND) {
				return $result;
			}
		}
		return NULL;
	}

	/**
	 * Remove a the given property.
	 *
	 * @param	string	$key   The key for the property which should be removed
	 * @return	mixed    Returns the result from the delegated calls
	 */
	public function removeProperty($key) {
		if ($this->removeCallback) {
			$oldValue = $this->getObjectForKey($key);
			$arguments = array($key, $oldValue, $this);
			return call_user_func_array($this->removeCallback, $arguments);
		} else {
			$result = $this->_callMethodIfExists("removeCallback");
			if ($result !== self::IR_METHOD_NOT_FOUND) {
				return $result;
			}
		}
		return NULL;
	}

}