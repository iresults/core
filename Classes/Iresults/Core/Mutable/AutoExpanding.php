<?php
namespace Iresults\Core\Mutable;

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
 * The auto expanding class extends the mutability of mutable objects. In
 * addition to the possibility to set any property of the instance, the auto
 * expanding class creates a new instance for each undefined property key, or
 * property key path. This allows you to set the property "subobject.aProperty"
 * even if subobject didn't exist in advance.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class AutoExpanding extends \Iresults\Core\Mutable {
	/**
	 * Returns the object for the given (undefined) key, of this instance, or
	 * the given object (if specified).
	 *
	 * @param	string	$key		The name of the undefined property
	 * @param	object	$object 	The object to get the property from
	 * @return	mixed	Returns the property's value
	 */
	protected function _getObjectForUndefinedKeyOfObject($key, $object) {
		$propertyValue = NULL;
		/*
		 * Allow subclasses to overwrite getObjectForUndefinedKey()
		 */
		if (get_class($this) !== __CLASS__) {
			try{
				$propertyValue = $this->getObjectForUndefinedKey($key);
			} catch(InvalidArgumentException $e) {
				if ($e->getCode() !== 1320741816) {
					throw InvalidArgumentException($e->getMessage(),$e->getCode());
				}
			}
		}

		/*
		 * If still no property value is defined, create the new sub object.
		 */
		if (!$propertyValue) {
			$propertyValue = $this->_createSubObjectForKeyOfObject($key, $object);
		}
		return $propertyValue;
	}

	/**
	 * Creates a new object that will be set as the property of the parent object.
	 *
	 * @param	string	$key		The property key to set
	 * @param	object	$object	The object to set the property of
	 * @return	\Iresults\Core\Mutable Returns the new created object
	 */
	protected function _createSubObjectForKeyOfObject($key, $object) {
		static $subObjectClassNameForSubClass = NULL;
		if (!$subObjectClassNameForSubClass) {
			$subObjectClassNameForSubClass = $this->_getSubObjectClassName();
		}
		$newObject = new $subObjectClassNameForSubClass();

		$res = \Iresults\Core\Helpers\ObjectHelper::setObjectForKeyOfParentObject($key, $newObject, $object);
		if ($res === FALSE) {
			unset($newObject);
			return NULL;
		}
		return $newObject;
	}

	/**
	 * Returns the class name for sub-/child-objects.
	 *
	 * On default the current instance's class name will be returned.
	 *
	 * @return	string    The class name
	 */
	protected function _getSubObjectClassName() {
		return get_class($this);
	}

	///**
	// * Returns a properties data.
	// *
	// * @param	string	$key
	// *
	// * @return	mixed
	// */
	//public function __get($key) {
	//	if (isset($this->_data[$key])) {
	//		return $this->_data[$key];
	//	} else {
	//		return NULL;
	//	}
	//}

	/**
	 * Tests if a property exists.
	 *
	 * Will always return TRUE for auto expanding objects.
	 *
	 * @param	string	$key The property name
	 * @return	boolean    Always return TRUE for auto expanding objects
	 */
	public function __isset($key) {
		return TRUE;
	}

	/**
	 * Called if no object was found for the given property key.for the give
	 *
	 * @param	string	$key The name of the undefined property
	 * @return	mixed    Returns a substitue value
	 * @throws InvalidArgumentException on default.
	 */
	public function getObjectForUndefinedKey($key) {
		return $this->_createSubObjectForKeyOfObject($key, $this);
	}

	/**
	 * Returns if the object already has an object for the given key.
	 *
	 * The method will ignore the auto-expanding functionality and will return
	 * FALSE if a key isn't set, without creating it.
	 *
	 * @param	string	$key The property key to test
	 * @return	boolean    TRUE if the property is set, otherwise FALSE
	 */
	public function hasRealObjectForKey($key) {
		if (isset($this->_data[$key])) {
			return TRUE;
		}
		return FALSE;
	}

	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ARRAY ACCESS   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	public function offsetExists($offset) {
		return TRUE;
	}
	public function offsetGet($offset) {
		return $this->getObjectForKey($offset);
	}
	public function offsetSet($offset,$value) {
		$this->setObjectForKey($offset,$value);
	}
	public function offsetUnset($offset) {
		$this->removeObjectForKey($offset);
	}
}