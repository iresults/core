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





use \Iresults\Core\Helpers\ObjectHelper;

/**
 * Abstract base class for iresults model classes.
 *
 * The class extends the iresults Core class and implements the KVC interface to
 * provide Key Value Coding features for all it's subclasses. Furthermore it
 * provides the methods getObjectForKeyPath() and setObjectForKeyPath() which
 * enhances the KVC features with the resolution of property key paths.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults
 */
abstract class Model extends \Iresults\Core\Core implements \Iresults\Core\KVCInterface {
	//const IRUndefinedKeyExceptionValue;

	/**
	 * The constructor
	 *
	 * @param	array   $parameters	 Optional parameters to pass to the constructor
	 * @return	Iresults_Model
	 */
	public function __construct(array $parameters = array()) {
		return $this;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* KEY VALUE CODING    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Set all properties from the array.
	 * Traverses the given source and tries to handle each key-value-pair as a new
	 * property value for that key.
	 *
	 * @param	array	$source            The input array
	 * @param	boolean	$prepareSourceKeys	 Indicates if the _prepareSourceKeys method should be invoked for the source
	 * @param	string	$prefix Optional prefix to add to the keys
	 * @return	void
	 */
	public function setPropertiesFromArray($source, $prepareSourceKeys = false, $prefix = '') {
		if ($prepareSourceKeys) {
			$source = $this->_prepareSourceKeys($source);
		}
		foreach ($source as $key => $value) {
			$key = $prefix . $key;
			$this->setObjectForKeyPath($key, $value);
		}
	}

	/**
	 * Sets an object/value for the given key.
	 *
	 * @param	string	$key   The key to set
	 * @param	mixed	$value The new value
	 * @return	void
	 */
	public function setObjectForKey($key,$value) {
		$accessorName = 'set' . ucfirst($this->_toUpperCamel($key));

		if (method_exists($this, $accessorName)) { // Check if a accessor is implemented
			call_user_func(array($this, $accessorName), $value);
			return;
		}

		$classVars = get_object_vars($this);
		if (array_key_exists($key, $classVars)) { // Check if a property exists
			$this->$key = $value;
		}
	}

	/**
	 * Returns an object/value for the given key.
	 *
	 * @param	string	$key The key/property name to fetch
	 * @return	mixed
	 */
	public function getObjectForKey($key) {
		$accessorName = 'get' . ucfirst($this->_toUpperCamel($key));

		if (method_exists($this, $accessorName)) { // Check if a accessor is implemented
			return call_user_func(array($this, $accessorName));
		}

		$classVars = get_object_vars($this);
		if (isset($classVars[$key])) { // Check if a property exists
			if (is_array($classVars[$key])) {
				$result = &$this->$key;
				return $result;
			}
			return $this->$key;
		}
		return NULL;
	}

	/**
	 * Removes the object with the given key.
	 *
	 * @param	string	$key
	 * @return	void
	 */
	public function removeObjectForKey($key) {
		$accessorName = 'set' . ucfirst($this->_toUpperCamel($key));

		if (method_exists($this, $accessorName)) { // Check if a accessor is implemented
			call_user_func(array($this, $accessorName), NULL);
			return;
		}

		$classVars = get_object_vars($this);
		if (isset($classVars[$key])) { // Check if a property exists
			unset($this->$key);
		}
	}

	/**
	 * @see getObjectForKeyPath()
	 * @deprecated
	 */
	public function getValueForKeyPath($propertyPath) {
		return $this->getObjectForKeyPath($propertyPath);
	}

	/**
	 * Returns the value of the property at the given key path.
	 *
	 * @param	string	$propertyPath The property key path to resolve in the format "object.property"
	 * @return	mixed
	 */
	public function getObjectForKeyPath($propertyPath) {
		return ObjectHelper::getObjectForKeyPathOfObject($propertyPath, $this);
	}

	/**
	 * Called if no object was found for the given property key.for the give
	 *
	 * @param	string	$key The name of the undefined property
	 * @return	mixed    Returns a substitue value
	 * @throws InvalidArgumentException on default.
	 */
	public function getObjectForUndefinedKey($key) {
		throw new InvalidArgumentException("No value found for undefined key '$key'.");
	}

	/**
	 * Returns the object for the given (undefined) key, of this instance, or
	 * the given object (if specified).
	 *
	 * The default implementation only invokes getObjectForUndefinedKey() of the
	 * this instance/the given object.
	 *
	 * @param	string	$key		The name of the undefined property
	 * @param	object	$object	The object to get the property from
	 * @return	mixed	Returns the property's value
	 */
	protected function _getObjectForUndefinedKeyOfObject($key, $object) {
		return $object->getObjectForUndefinedKey($key);
	}

	/**
	 * Sets the value for the property identified by a given key path.
	 *
	 * @param	string	$propertyPath The property key path in the form (object.property)
	 * @param	mixed	$object       The new value to assign
	 * @return	void
	 */
	public function setObjectForKeyPath($propertyPath, $object) {
		return ObjectHelper::setObjectForKeyPathOfObject($propertyPath, $object, $this);
	}

	/**
	 * Returns if the property at the given key path, can be resolved.
	 *
	 * @param	string	$propertyPath 	The property key path to resolve in the format "object.property"
	 * @return	boolean					Returns TRUE if the property key path can be resolved, otherwise FALSE
	 */
	public function hasObjectForKeyPath($propertyPath) {
		try {
			$value = ObjectHelper::getObjectForKeyPathOfObject($propertyPath, $this);
		} catch (InvalidArgumentException $e) {
			$value = NULL;
		}
		if ($value) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Invoked bevore the source array is traversed to set properties from array.
	 *
	 * @param	array/dictionary $source
	 *
	 * @return	array/dictionary
	 */
	protected function _prepareSourceKeys(&$source) {
		return $source;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* HELPER FUNCTIONS    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Converts a string to upper-camel-case.
	 *
	 * @param	string	$string
	 * @param	string	$delimiter Optional value by which the string will be split
	 * @return	string
	 */
	protected function _toUpperCamel($string, $delimiter = '_') {
		if (func_num_args() < 2) {
			return \Iresults\Core\Tools\StringTool::underscoredToLowerCamelCase($string);
		}

		$result = array();
		$parts = explode($delimiter, $string);
		foreach ($parts as $part) {
			$result[] = ucfirst($part);
		}
		return implode('', $result);
	}
}