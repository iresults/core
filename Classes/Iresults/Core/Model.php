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
