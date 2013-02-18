<?php
namespace Iresults\Core\Helpers;

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
 * The iresults object helper provides some functions to inspect, reflect,
 * traverse and transform objects of different classes.
 *
 * @package Iresults
 * @subpackage Iresults_Helpers
 * @version 1.5
 */
class ObjectHelper {
	/**
	 * Indicates if the stdClass should be treated as mutable.
	 *
	 * @var boolean
	 */
	static protected $treatStdClassAsMutable = FALSE;


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* GETTING PROPERTIES    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the given object's value for the property key path.
	 *
	 * @param	string 	$propertyPath 	The property key path to resolve in the format 'object.property'
	 * @param	object	$object			The object to get the value of
	 * @return		mixed 					The value of the property
	 */
	static public function getObjectForKeyPathOfObject($propertyPath, $object) {
		// @Info: FLOW3 $configuration = \TYPO3\FLOW3\Utility\Arrays::getValueByPath($array, $path);
		/*
		 * Check if the given property path is not 'this', an empty string (or
		 * similar), but allow the string '0'.
		 */
		$propertyPath .= '';
		if ($propertyPath === '' || $propertyPath == 'this') {
			return NULL;
		}
		$pathParts = explode('.', $propertyPath);

		/** @var mixed $currentObject */
		$currentObject = NULL;
		/** @var mixed $parentObject */
		$parentObject = NULL;

		$currentObject = $object;
		$parentObject = $object;

		foreach ($pathParts as $path) {
			$path = trim($path);
			if ($path === 'this') {
				if (count($pathParts) !== 1) {
					continue;
				} else {
					break;
				}
			} else if ($path === '') {
				break;
			}

			if (is_object($currentObject)) {
				$parentObject = $currentObject;
			} else {
				$parentObject = &$currentObject;
			}


			/*
			 * Check the implementations
			 */
			if (is_array($parentObject)) {
				if (isset($parentObject[$path])) {
					$currentObject = &$parentObject[$path];
				} else {
					$currentObject = NULL;
				}
			} else if ($parentObject instanceof \Iresults\Core\KVCInterface) { // \Iresults\Core\KVCInterface
				$currentObject = $parentObject->getObjectForKey($path);
			} else if (is_a($parentObject, 'Tx_Extbase_DomainObject_DomainObjectInterface')) { // Tx_Extbase_DomainObject_DomainObjectInterface
				$currentObject = $parentObject->_getProperty($path);
			} else if (is_a($parentObject, '\Iresults\Core\Value')) { // \Iresults\Core\Value
				if ($path === 'value') {
					$currentObject = $parentObject->getValue();
				} else {
					$currentObject = NULL;
				}
			} else if (method_exists($parentObject, 'getData')) { // getData()
				$currentObject = $parentObject->getData($path);
			} else if (property_exists($parentObject, $path)) { // Direct access
				$currentObject = $parentObject->$path;
			} else if (self::$treatStdClassAsMutable && get_class($parentObject) === 'stdClass') { // Mutable stdClass
				@$currentObject = $parentObject->$path;
			} else if (method_exists($parentObject, 'execute')) { // execute()
				$currentObject = $parentObject->execute($path);
				if (IR_MODERN_PHP) {
					trigger_error('The accessor method execute is deprecated and will not be used in ObjectHelper in future versions.', E_USER_DEPRECATED);
				} else {
					trigger_error('The accessor method execute is deprecated and will not be used in ObjectHelper in future versions.', E_USER_WARNING);
				}
			} else {
				$currentObject = NULL;

				$type = '';
				if (is_object($parentObject)) {
					$type = get_class($parentObject);
				} else {
					$type = gettype($parentObject);
				}
				throw new InvalidArgumentException('Cannot get property \'' . $path . '\' of object of type ' . $type . '.', 1320769266);
			}

		}
		return $currentObject;
	}

	/**
	 * Returns the value of the given object at the given key.
	 *
	 * @param	string	$key		The property key to get
	 * @param	object	$object	The object to get the property from
	 * @return		mixed 			Returns the property's value
	 */
	static public function getObjectForKeyOfObject($key, $object) {
		return self::getObjectForKeyPathOfObject($key, $object);
	}

	/**
	 * Returns the properties from the given object as a dictionary.
	 *
	 * @param	object	$object The object to analyse
	 * @return		void
	 */
	static public function getPropertiesOfObject($object) {
		return Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($object);
	}

	/**
	 * Returns the value of the given object at the given key, even if it is not
	 * accessible.
	 *
	 * Spying is evil, you maybe shouldn't use this function.
	 *
	 * @param	string	$key	The property key to get
	 * @param	object	$object	The object to get the property from
	 * @return	mixed			Returns the property's value
	 */
	static public function spyPropertyOfObject($key, $object) {
		$refObject   = new ReflectionObject($object);
		$refProperty = $refObject->getProperty($key);
		$refProperty->setAccessible(TRUE);
		return $refProperty->getValue();
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* SETTING PROPERTIES    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Sets the target object's value for the property identified by the given key path.
	 *
	 * @param	string 	$propertyPath 	The property key path in the form (object.property)
	 * @param	mixed 	$object 		The new value to assign
	 * @param	object 	$targetObject 	The object from which the property path will be resolved
	 * @return		mixed					Returns the object that has been modified
	 */
	static public function setObjectForKeyPathOfObject($propertyPath, $object, $targetObject) {
		/*
		 * Check if the given property path is not 'this', an empty string (or
		 * similar), but allow the string '0'.
		 */
		$propertyPath .= '';
		if ($propertyPath === '' || $propertyPath == 'this') {
			return NULL;
		}

		$setObject = NULL;
		$setKey = substr(strrchr($propertyPath, '.'), 1);
		/*
		 * If $setKey is not set the '.' was not found and no sub-object has to
		 * be fetched.
		 */
		if (!$setKey) {
			$setKey = $propertyPath;
			$setObject = $targetObject;
		} else {
			$propertyPathTillSetObject = substr($propertyPath, 0, -(strlen($setKey)));
			$setObject = self::getObjectForKeyPathOfObject($propertyPathTillSetObject, $targetObject);
		}

		/*
		 * Check the implementations
		 */
		if (is_array($setObject)) { // An array
			$setObject[$setKey] = $object;
			self::setObjectForKeyPathOfObject(substr($propertyPathTillSetObject, 0, -1), $setObject, $targetObject);
		} else if ($setObject instanceof \Iresults\Core\KVCInterface) { // \Iresults\Core\KVCInterface
			$setObject->setObjectForKey($setKey, $object);
		} else if (is_a($setObject, 'Tx_Extbase_DomainObject_DomainObjectInterface')) { // Tx_Extbase_DomainObject_DomainObjectInterface
			$setObject->_setProperty($setKey, $object);
		} else if (method_exists($setObject, 'setData')) { // setData()
			$setObject->setData($setKey, $object);
		} else if (is_object($setObject) && class_exists($setObject) && property_exists($setObject, $setKey)) { // Direct access
			$setObject->$setKey = $object;
		} else if (self::$treatStdClassAsMutable && get_class($setObject) === 'stdClass') { // Mutable stdClass
			@$setObject->$setKey = $object;
		} else {
			$type = '';
			if (is_object($setObject)) {
				$type = get_class($setObject);
			} else {
				$type = gettype($setObject);
			}
			throw new \InvalidArgumentException('Cannot set property \'' . $setKey . '\' of object of type ' . $type . '.', 1320769037);
		}
		return $setObject;
	}

	/**
	 * Sets the value of the given parent object at the given key.
	 *
	 * @param	string	$key		The property key to set
	 * @param	mixed	$object		The new value to set
	 * @param	object	$parentObject	The parent object to change
	 * @return	boolean	Returns TRUE on success, otherwise FALSE
	 */
	static public function setObjectForKeyOfParentObject($key, $object, $parentObject) {
		self::setObjectForKeyPathOfObject($key, $object, $parentObject);
		return TRUE;
	}

	/**
	 * Sets the values from the given array|dictionary as the object's
	 * properties.
	 *
	 * @param	array<mixed> $array  The source array|dictionary
	 * @param	object	$object The object to change
	 * @param	boolean	$convertKeys	 Set if array keys should be converted from lowerlevel-underscored to camelCase
	 * @return	void
	 */
	static public function setPropertiesOfObjectFromArray($array, $object, $convertKeys = FALSE) {
		if (!is_array($array) && !$array instanceof Traversable) {
			$array = self::getPropertiesOfObject($array);
		}
		foreach ($array as $key => $value) {
			if ($convertKeys) {
				$key = \Iresults\Core\Tools\StringTool::underscoredToLowerCamelCase($key);
			}
			self::setObjectForKeyPathOfObject($key, $value, $object);
		}
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* CREATING OBJECTS      MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Creates an object for the given value.
	 *
	 * @param	mixed	$value The value to transform into an object
	 * @return	object    The object representation of the given value or NULL on error
	 */
	static public function createObjectWithValue($value) {
		$newValue = NULL;
		if (is_array($value)) {
			$newValue = \Iresults\Core\Mutable::mutableWithArray($value);
		} else if (is_string($value) && file_exists($value)) {
			$newValue = \Iresults\Core\System\FileManager::getResourceAtUrl($value);
		} else if (is_resource($value)) {
		} else if (is_object($value) && self::$treatStdClassAsMutable && get_class($value) === 'stdClass') {
			$newValue = \Iresults\Core\Mutable::mutableWithStdClass($value);
		} else {
			$newValue = new \Iresults\Core\Value($value);
		}
		return $newValue;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* COPYING OBJECTS       MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Creates a copy of the given object and all it's child objects.
	 *
	 * @link http://www.waynehaffenden.com/Blog/PHP-Deep-Object-Cloning
	 *
	 * @param	object	$object The object to create a copy of
	 * @param	boolean	$useSleep	 If set to TRUE the __sleep() and __wakeup() methods will be called
	 * @return	object    Returns the object's clone
	 */
	static public function createCopyOfObject($object, $useSleep = FALSE) {
		$clone = NULL;

		// If the object is scalar or a resource return it.
		if (is_scalar($object) || is_resource($object)) {
			return $object;
		}

		// If __sleep should be used, JSON is unavailable or failed, use serialize()
		if ($clone === NULL) {
			$clone = unserialize(serialize($object));
		}
		return $clone;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MANAGING OBJECTS      MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Creates an unique identifier for the given object.
	 *
	 * @param	object	$object The object to create the hash of
	 * @return	string	The unique identifier
	 */
	static public function createIdentfierForObject($object) {
		$hash = '';
		if (is_resource($object)) {
			$hash = get_resource_type($object) . $object;
		} else if (!is_object($object)) {
			if (is_array($object)) {
				sort($object);
			}
			$hash = md5(serialize($object));
	 	} else {
			$uid = '';
			/*
			 * Get the unique property value of the object.
			 */
			if (is_a($object, 'Tx_Extbase_DomainObject_DomainObjectInterface')) { // Tx_Extbase_DomainObject_DomainObjectInterface
				$uid = $object->getUid();
			} else if (is_a($object, '\Iresults\Core\Value')) { // \Iresults\Core\Value
				$uid = $object->getValue();
			} else if (is_a($object, '\Iresults\Core\Mutable')) { // \Iresults\Core\Mutable
				$uid = md5( serialize( array_keys($object->getData()) ) );
			} else {
				$properties = get_object_vars($object); // object
				$uid = md5( serialize( array_keys($properties) ) );
			}
			$hash = spl_object_hash($object) . '_' . $uid;
		}
		return $hash;
	}

	/**
	 * @see createIdentfierForObject()
	 */
	static public function createHashForObject($object) {
		return self::createIdentfierForObject($object);
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* CONFIGURATION         MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns if the stdClass should be treated as mutable.
	 * @return	boolean
	 */
	static public function getTreatStdClassAsMutable() {
		return self::$treatStdClassAsMutable;
	}

	/**
	 * Set if the stdClass should be treated as mutable.
	 *
	 * @param	boolean	$flag The flag
	 * @return	void
	 */
	static public function setTreatStdClassAsMutable($flag) {
		self::$treatStdClassAsMutable = $flag;
	}



}