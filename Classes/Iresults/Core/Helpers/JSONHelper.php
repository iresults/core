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

use TYPO3\Flow\Annotations as Flow;

/**
 * The iresults JSON helper provides some functions to encode objects as JSON
 * strings.
 *
 * = Examples =
 *
 * <code>
 * JSONHelper::createJSONStringFromObject($anObject);
 * </code>
 *
 * @package Iresults
 * @subpackage Iresults_Helpers
 * @version 1.5
 * @Flow\Scope("singleton")
 */
class JSONHelper {
	/**
	 * This character at the beginning of a string marks the string as a special
	 * string.
	 */
	const SPECIAL_CHAR = ':';

	/**
	 * Returns the given value in a JSON representation.
	 *
	 * @param	mixed 	$object  			The object to translate
	 * @param	integer 	$options 			Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE
	 * @param	string 		$objectWrap	A string to wrap the JavaScript objects into
	 * @return	string    	Returns the given value in a JSON representation.
	 */
	static public function createJSONStringFromObject($object, $options = 0, $objectWrap = '|') {
		$JSONString = self::_createJSONStringFromObject($object, $options, $objectWrap);
		return str_replace(array("\r", "\n"), '', $JSONString);
	}

	/**
	 * Returns the given value in a JSON representation.
	 *
	 * This method makes use of the SPECIAL_CHAR constant. If a key or value are
	 * of type string and begin with the SPECIAL_CHAR, the will be checked for
	 * special information. I.e.:
	 * 	:f:hello
	 * returns
	 * 	hello
	 *
	 * 	aSimpleString
	 * returns
	 * 	"aSimpleString"
	 *
	 * 	:true	or 	:True 	or :TRUE
	 * return
	 * 	true
	 *
	 * 	:false	or 	:False	or :FALSE
	 * return
	 * 	false
	 *
	 * This behaviour allows you to specify JavaScript function calls in PHP
	 * objects that will be parsed as JSON objects.
	 *
	 * @param	mixed 	$object  			The object to translate
	 * @param	integer 	$options 			Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE
	 * @param	string 		$objectWrap	A string to wrap the JavaScript objects into
	 * @return	string    	Returns the given value in a JSON representation.
	 */
	static protected function _createJSONStringFromObject($object, $options = 0, $objectWrap = '|') {
		if (is_a($object, 'JsonSerializable')) { // If the object implements JsonSerializable
			$object = $object->jsonSerialize();
			$options = $options | JSON_FORCE_OBJECT;
		} else if (is_a($object, '\Iresults\Core\Mutable')) { // If the object is a Mutable create an array-version of it
			$object = SerializationHelper::objectToArray($object);
			$options = $options | JSON_FORCE_OBJECT;
		} else if (is_a($object, 'Tx_Extbase_Persistence_QueryResultInterface') ||
				  is_array($object)) {
			return self::arrayOrDictionaryToJSON($object, $options, $objectWrap);
		} else if ($object instanceof Traversable) {
			$object = iterator_to_array($object);
			$options = $options | JSON_FORCE_OBJECT;
		} else if (is_object($object)) {
			if (is_a($object, 'Tx_Extbase_DomainObject_DomainObjectInterface')) {
				$object = $object->_getProperties();
			} else {
				$object = get_object_vars($object);
			}
			$options = $options | JSON_FORCE_OBJECT;
		} else if (is_scalar($object)) {
			return self::stringFromValue($object);
		}
		$JSONString = json_encode($object, $options);
		return str_replace('|', $JSONString, $objectWrap);
	}

	/**
	 * Returns the given array or object in a JSON representation.
	 *
	 * @param	array|object		$value				The array or object to transform
	 * @param	integer				$options			Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE
	 * @param	string 				$objectWrap	A string to wrap the JavaScript objects into
	 * @return	string 		Returns the given value in a JSON representation
	 */
	static protected function arrayOrDictionaryToJSON($value, $options = 0, $objectWrap = '|') {
		$isDictionary = FALSE;
		$allKeys = NULL;
		$JSONStrings = array();

		/*
		 * Treat instances of Tx_Extbase_Persistence_QueryResultInterface as
		 * indexed arrays.
		 */
		if (is_object($value) && (JSON_FORCE_OBJECT & $options) === 0
			&& is_a($value, 'Tx_Extbase_Persistence_QueryResultInterface')) {
			$value = iterator_to_array($value);
			$isDictionary = FALSE;
		}
		/*
		 * If the JSON_FORCE_OBJECT option is set, or the value is an object
		 * don't check if the given value is a dictionary.
		 */
		else if (is_object($value) || (JSON_FORCE_OBJECT & $options)) {
			$isDictionary = TRUE;
		} else {
			$allKeys = array_keys($value);
			while ($currentKey = current($allKeys)) {
				if (!is_int($currentKey)) {
					$isDictionary = TRUE;
					break;
				}
				next($allKeys);
			}
		}

		// Traverse the value
		while (($currentValue = current($value)) || key($value)) {
			$currentJSONString = '';

			if ($isDictionary) {
				$currentKey = key($value);
				$currentJSONString .= '"' . $currentKey .'": ';
			}
			$currentJSONString .= self::_createJSONStringFromObject($currentValue, $options, $objectWrap);
			$JSONStrings[] = $currentJSONString;

			next($value);
		}

		if ($isDictionary) {
			return '{' . implode(',', $JSONStrings) . '}';
		}
		return '[' . implode(',', $JSONStrings) . ']';
	}

	/**
	 * Returns the JSON representation of the given value.
	 *
	 * @param	mixed	$value The value to parse as a JSON value
	 * @return	string    Returns the JSON representation of the given value
	 */
	static public function stringFromValue($value) {
		if (is_bool($value)) { // Boolean
			#$value = $value ? 'true' : 'false';
			$value = $value ? 1 : 0;
			#$value = json_encode($value);
		} else
		if (is_numeric($value)) { // Number
			if (is_int($value)) {
				$value = intval($value);
			} else {
				$value = $value * 1.0;
			}
		}

		// Handle special values
		if (is_string($value)) {
			if (substr($value, 0, 1) == self::SPECIAL_CHAR) {
				if (substr($value, 1, 2) == 'f:') { // Function
					$value = substr($value, 3);
				} else if (strtolower(substr($value, 1)) == 'true') {
					$value = 'true';
				} else if (strtolower(substr($value, 1)) == 'false') {
					$value = 'false';
				}
			}
			$value = json_encode($value);
		}
		return $value;
	}
}