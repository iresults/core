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

if (@class_exists('Mage_Core_Helper_Abstract')) {
	class SortHelperAbstractBase extends Mage_Core_Helper_Abstract {}
} else {
	class SortHelperAbstractBase {}
}

/**
 * The iresults sort helper provides different methods to sort arrays of
 * objects. It also enables you to detect and retrieve subgroups of object.
 * Subgroups are arrays of objects that share the same property value for a
 * given property key.
 *
 * @package Iresults
 * @subpackage Helpers
 * @version 1.5
 */
abstract class SortHelperAbstract extends SortHelperAbstractBase {
	protected $_alternativeCompare = array();
	protected $_alternativeCompareWorkingCopy = array();
	protected $_alternativeCompareConfig = array();

	protected $_subgroups = array();

	protected $_subgroupLevel = 0;

	/**
	 * The cache for subgroup methods.
	 */
	/**
	 * @var array The last input array to create subgroups of.
	 */
	protected $_lastArray = NULL;

	/**
	 * @var string The last property key to create subgroups with.
	 */
	protected $_lastGroupPropertyKey = '';

	/**
	 * @var array The last result of the subgroup creation.
	 */
	protected $_lastSubgroupsArray = array();

	/**
	 * @var string The last used getter method.
	 */
	protected $_lastGetterMethod = '';

	/**
	 * @var boolean Indicates if string comparison should be case sensitive.
	 */
	static protected $_caseSensitive = FALSE;

	/**
	 * @var boolean	NULL data values will be replaced with the value of
	 * self::$_nullPlaceholder. If you don't want to use this functionality
	 * set this value to NULL.
	 */
	static protected $_nullPlaceholder = 0;

	/**
	 * @var boolean Indicates if debug calls should echo the message.
	 */
	static protected $_debug = 0;
	const DEBUG_NONE = 0;
	const DEBUG_ECHO = 1;
	const DEBUG_TRIGGER = 2;

	/**
	 * @var SortHelperAbstract A singleton instance for
	 * static method calls.
	 */
	static protected $_instance = NULL;


	/**
	 * The sorting function sorts the Varien objects by the given key.
	 *
	 * @param	string	$propertyKey		The property key to sort by
	 * @param	object	$objectA			The first object to compare
	 * @param	object	$objectB			The second object to compare
	 * @param	boolean	$desc				Sort desc
	 * @param	boolean	$useAttributeText	If set to true getAttributeText() will be invoked on the objects instead of getData()
	 * @return	integer
	 */
	public function sortByProperty($propertyKey, $objectA, $objectB, $desc = false, $useAttributeText = FALSE) {
		$dataA = NULL;
		$dataB = NULL;
		$result = 0;

		// Check the objects
		if (!is_object($objectA)) {
			self::debug('Argument 1 is not an object. It is of type ' . gettype($objectA));
			$result = 1;
		}
		if (!is_object($objectB)) {
			self::debug('Argument 2 is not an object. It is of type ' . gettype($objectB));
			$result = -1;
		}
		if ($result !== 0) {
			if ($desc) $result *= -1;
			return $result;
		}

		$getterMethod = NULL;
		if ($useAttributeText) {
			$getterMethod = 'getAttributeText';
		}

		$dataA = $this->_invokeGetterMethodOnObject($objectA, $propertyKey, $getterMethod);
		$dataB = $this->_invokeGetterMethodOnObject($objectB, $propertyKey, $getterMethod);

		// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
		// Check if data 1 is null
		if ($dataA === null) {
			self::debug('Data 1 is null for property ' . $propertyKey);
			$result = -1;
		} else
		// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
		// Check if data 2 is null
		if ($dataB === null) {
			self::debug('Data 2 is null for property ' . $propertyKey);
			$result = 1;
		} else
		// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
		// Check if the data is equal
		if ($dataA == $dataB) {
			self::debug('Handle equal for property ' . $propertyKey);
			//$result = $this->_handleEqual($objectA, $objectB);
			$result = 0;
		} else
		// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
		// Check if both are numeric
		if (is_numeric($dataA) && is_numeric($dataB)) { // If numeric compare values
			self::debug('Handle numeric for property ' . $propertyKey . '(' . $dataA . ' vs. ' . $dataB . ').');
			$dataA = (float)$dataA;
			$dataB = (float)$dataB;
			$result = ($dataA < $dataB) ? -1 : 1;

		// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
		// Check if both are Varien objects
		} else if (is_object($dataA) && is_object($dataB) && method_exists($dataA, 'getUid') && method_exists($dataB, 'getUid')) { // If object compare the UIDs
			$result = ($dataA->getUid() < $dataB->getUid()) ? -1 : 1;

		// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
		// Check if one data is a string
		} else if (is_string($dataA) || is_string($dataB)) { // If one data object is a string use strcmp or strcasecmp
			self::debug('Handle string for property' . $propertyKey . '(' . $dataA . ' vs. ' . $dataB . ').');
			if (self::$_caseSensitive) {
				$result = strcmp('' . $dataA, '' . $dataB);
			} else {
				$result = strcasecmp('' . $dataA, '' . $dataB);
			}
		} else {
			self::debug('Couldn\'t handle data for property ' . $propertyKey . '. Data A was of type ' . gettype($dataA) . ' and data B of type ' . gettype($dataA) . '.');
		}

		if ($desc) $result *= -1;
		return $result;
	}

	/**
	 * Sorts an array by the given property, or properties if more then one are
	 * passed.
	 *
	 * @param	array	$array							The array to sort
	 * @param	string|array<string> $propertyKey		The key of the primary property
	 * @param	string	$propertyKeyAlt1				Additional property keys to sort
	 * @param	string	$propertyKeyAlt2				Additional property keys to sort
	 * @return	array    Returns the sorted array
	 */
	public function sortArrayAscByProperty(&$array, $propertyKey, $propertyKeyAlt1 = NULL) {
		$this->_subgroupLevel = 0;
		/**
		 * Get the alternative compare keys.
		 * If $propertyKeyAlt1 is given it is assumed, that multiple arguments
		 * have been passed.
		 */
		if ($propertyKeyAlt1) {
			$arguments = func_get_args();
			array_shift($arguments);
			$this->_alternativeCompare = $arguments;
		} else
		/**
		 * If property key is an array, its values are used as alternative
		 * compare keys.
		 */
		if (is_array($propertyKey)) {
			$this->_alternativeCompare = $propertyKey;
			reset($propertyKey);
			$propertyKey = current($propertyKey);
		} else
		/**
		 * Use only the given property key.
		 */
		{
			$this->_alternativeCompare = array('asc' => $propertyKey);
		}

		$this->_alternativeCompareWorkingCopy = array_values($this->_alternativeCompare);
		$this->_alternativeCompareConfig = array_keys($this->_alternativeCompare);
		#set_time_limit(0);

		/**
		 * Sort by the first property.
		 */
		$callback = $this->_getSortCallbackForPropertyKeyAndConfig($propertyKey, '' . $this->_alternativeCompareConfig[0]);
		@usort($array, array($this, $callback));

		if (count($this->_alternativeCompare) > 1) {
			$this->sortSubgroups($array);
		}
		return $array;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* SUBGROUP METHODS       WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/**
	 * Groups the input array by it's subgroups, according to the given group
	 * property key.
	 *
	 * The method will group the elements of the input array that share the same
	 * value for the given group-property key. If a sort property key is passed
	 * the subgroups will be sorted. Furthermore you can set recursive to TRUE so
	 * the sortSubgroups() method will be called recursively.
	 *
	 * @param	array	$inputArray 			Reference to the input array
	 * @param	string	$groupPropertyKey	 The property key by which to group
	 * @param	string	$sortPropertyKey		The key by which to sort
	 * @param	string	$config				A string containing additional
	 * information about how to sort using the sort property key.
	 *
	 * @param	boolean	$recursively			Indicates if this method should be
	 * called recursively.
	 *
	 * @return	array
	 */
	public function sortSubgroups(&$inputArray, $groupPropertyKey = NULL, $sortPropertyKey = NULL, $config = '', $recursively = TRUE) {
		if (!$groupPropertyKey) {
			if (!array_key_exists($this->_subgroupLevel, $this->_alternativeCompareWorkingCopy)) {
				//Iresults_Tools::pd('fff', $this->_subgroupLevel, $this->_alternativeCompareWorkingCopy);
				return $inputArray;
			}
			$groupPropertyKey = $this->_alternativeCompareWorkingCopy[$this->_subgroupLevel];
		}
		if (!$sortPropertyKey) {
			$this->_subgroupLevel++;
			if (!array_key_exists($this->_subgroupLevel, $this->_alternativeCompareWorkingCopy)) {
				#$this->_subgroupLevel--;
				#Iresults_Tools::pd('ddd', $this->_alternativeCompareWorkingCopy);
				#return $inputArray;
				$sortPropertyKey = $this->_alternativeCompareWorkingCopy[$this->_subgroupLevel - 1];
				if (!$sortPropertyKey) {
					return $inputArray;
				}
			} else {
				$sortPropertyKey = $this->_alternativeCompareWorkingCopy[$this->_subgroupLevel];
			}
		}
		if (!$config) {
			if (array_key_exists($this->_subgroupLevel, $this->_alternativeCompareConfig)) {
				$config = $this->_alternativeCompareConfig[$this->_subgroupLevel];
			}
		}
		//Iresults_Tools::pd('eee', $this->_subgroupLevel);
		//$this->_subgroupLevel++;

		/**
		 * Sort the subgroups
		 */
		$returnArray = array();
		$subgroupIndex = '';

		$callback = $this->_getSortCallbackForPropertyKeyAndConfig($sortPropertyKey, $config);

		$subgroups = $this->getSubgroups($inputArray, $groupPropertyKey);
		foreach ($subgroups as $subgroupIndex => $subgroup) {
			/**
			 * Sort if a sort property key is given and the subgroup has more
			 * than 1 entries.
			 */
			if ($sortPropertyKey && count($subgroup) > 1) {
				@usort($subgroup,array($this, $callback));
			}



			if ($recursively) $this->sortSubgroups($subgroup);

			if (count($subgroup) > 1) {
				$this->debugSubgroup("The '$groupPropertyKey (".$this->_invokeGetterMethodOnObject($subgroup[0], $groupPropertyKey).")'-subgroup with ".count($subgroup)." objects are sorted by $sortPropertyKey.");
			} else {
				$this->debugSubgroup("The '$groupPropertyKey (".$this->_invokeGetterMethodOnObject($subgroup[0], $groupPropertyKey).")'-subgroup has only one entry.");
			}

			$this->debugSubgroup($subgroup);
			//foreach ($subgroup as $irKey => $irVal) {
			//	$this->debugSubgroup("$irKey: {$irVal->getSku()} #{$irVal->getId()}");
			//}

			$returnArray = array_merge($returnArray, $subgroup);
		}


		$this->_subgroupLevel--;

		//echo '<h1>';
		//
		////Iresults_Tools::pd($returnArray);
		//$this->debugSubgroup($returnArray);
		//echo 'B</h1>';

		$inputArray = $returnArray;
		return $returnArray;
	}

	/**
	 * Returns the subgroups of the input array as a dictionary. A subgroup is
	 * an array of objects that share the same property value for the property
	 * key given in $groupPropertyKey.
	 *
	 * @param	array	$inputArray			The array to group
	 * @param	string	$groupPropertyKey	The property to group by
	 * @param	string	$getterMethod			If a getter method is specified, this
	 * method will be used to get the group properties instead of using getData().
	 *
	 * @return	array
	 */
	public function getSubgroups($inputArray, $groupPropertyKey, $getterMethod = NULL) {
		// Check the cache for a former result
		if ($this->_lastArray == $inputArray && $this->_lastGroupPropertyKey == $groupPropertyKey && $this->_lastGetterMethod == $getterMethod) {
			if ($this->_lastSubgroupsArray && !empty($this->_lastSubgroupsArray)) {
				$this->debug(sprintf('Get subgroups cache. Current memory usage is %0.4fMB<br />',(memory_get_usage() / 1024 / 1024)));
				return $this->_lastSubgroupsArray;
			}
		}

		// Sort the input if $isSorted is FALSE
		//if (!$isSorted) {
		//	$callback = $this->_getSortCallbackForPropertyKeyAndConfig($groupPropertyKey);
		//	//@usort($inputArray,array($this, $callback));
		//}


		// Create the groups
		$subgroups = array();
		$value = NULL;
		foreach ($inputArray as $key => $element) {
			$value = $this->_invokeGetterMethodOnObject($element, $groupPropertyKey, $getterMethod);
			//if (is_null($value)) continue;
			if (!isset($subgroups['' . $value])) {
				$subgroups['' . $value] = array();
				//$this->debugSubgroup("new value=$value");
			}
			$subgroups['' . $value][] = $element;
		}


		// Set the cache
		if ($subgroups && !empty($subgroups)) {
			$this->_lastArray = $inputArray;
			$this->_lastGroupPropertyKey = $groupPropertyKey;
			$this->_lastSubgroupsArray = $subgroups;
			$this->_lastGetterMethod = $getterMethod;
		}


		return $subgroups;
	}

	/**
	 * Returns a single subgroup identified by its name.
	 *
	 * @param	string	$subgroupName		The name of the subgroup to return
	 * @param	array	$inputArray			The array to group
	 * @param	string	$groupPropertyKey	The property to group by
	 * @param	string	$getterMethod			If a getter method is specified, this
	 * method will be used to get the group properties instead of using getData().
	 *
	 * @return	array
	 */
	public function getSubgroup($subgroupName, $inputArray, $groupPropertyKey, $getterMethod = NULL) {
		$subgroups = $this->getSubgroups($inputArray, $groupPropertyKey, $getterMethod);

		//Iresults_Tools::pd($subgroups);

		if (isset($subgroups[$subgroupName])) {
			return $subgroups[$subgroupName];
		}
		return array();
	}

	/**
	 * Sorts the subgroups by the names given in the order array.
	 *
	 * In the first step the input array will be sorted and grouped. After that
	 * the given order array, which contains a list of group names, will be
	 * traversed. For each element of the order array a subgroup with that name
	 * will be searched in the input array.
	 *
	 * Example: An order array with the elements "green", "blue" will return an
	 * array with all elements of the subgroups with either the name "green" or
	 * "blue". If no "green" subgroup is found only the elements of the "blue"
	 * subgroup will be returned.
	 *
	 * Warning: Subgroups that are not required in the order array will not be
	 * part of the return value. If there exists a subgroup "red" in the above
	 * example it would not be returned, because it is not required. If you want
	 * the not mentioned elements to be applied at the end of the return array
	 * pass an asterisk as the last element of the order array.
	 *
	 * For more information about the sorting method see getSubgroups().
	 *
	 * @param	array	$inputArray The input array
	 * @param	array	$order		The array of subgroup names to return
	 * @param	string	$groupPropertyKey	The property to group by
	 * @param	string	$getterMethod			If a getter method is specified, this
	 * method will be used to get the group properties instead of using getData().
	 *
	 * @return	array
	 */
	public function getSubgroupsByCustomOrder($inputArray, $order, $groupPropertyKey, $getterMethod = NULL) {
		$result = array();
		$subgroups = $this->getSubgroups($inputArray, $groupPropertyKey, $getterMethod);

		/**
		 * Check if valid subgroups were found.
		 */
		if (!$subgroups || empty($subgroups) || (count($subgroups) < 2 && key($subgroups) == '')) {
			return $result;
		}

		/**
		 * Go through each order names
		 */
		foreach ($order as $subgroupName) {
			if ($subgroupName == '*') {
				$result = array_merge($result, $subgroups);
			} else if (isset($subgroups[$subgroupName])) {
				$result[$subgroupName] = $subgroups[$subgroupName];
				unset($subgroups[$subgroupName]);
			}
		}

		//Iresults_Tools::pd(array_keys($result),count($subgroups), $subgroups);

		return $result;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* HELPER METHODS         WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/**
	 * Handles the comparison if the values are equal (for the primary property key).
	 *
	 * @param	object	$objectA
	 * @param	object	$objectB
	 * @return	integer
	 */
	protected function _handleEqual($objectA, $objectB) {
		if ($this->_alternativeCompare && !empty($this->_alternativeCompare) && !empty($this->_alternativeCompareWorkingCopy)) {
			$alternativePropertyKey = array_shift($this->_alternativeCompareWorkingCopy);
			$result = $this->sortByProperty($alternativePropertyKey, $objectA, $objectB);
			return $result;
		} else if ($this->_alternativeCompare && !empty($this->_alternativeCompare) && empty($this->_alternativeCompareWorkingCopy)) {
			unset($this->_alternativeCompareWorkingCopy);
			$this->_alternativeCompareWorkingCopy = $this->_alternativeCompare;
			$alternativePropertyKey = array_shift($this->_alternativeCompareWorkingCopy);
			$result = $this->sortByProperty($alternativePropertyKey, $objectA, $objectB);
			return $result;
		} else {
			return 0;
		}
	}

	/**
	 * Creates the sort callback for the given property key and configuration
	 * string.
	 *
	 * @param	string	$propertyKey 	The key of the property to sort by
	 * @param	string	$config			The callback configuration as string
	 * @return	string    The name of the callback function
	 */
	protected function _getSortCallbackForPropertyKeyAndConfig($propertyKey, $config = '') {
		return $this->_getSortCallbackForConfig($config) . $propertyKey;
	}

	/**
	 * Constructs the sort callback for the given configuration string.
	 *
	 * @param	string	$config
	 * @return	string
	 */
	protected function _getSortCallbackForConfig($config) {
		$callback = '';
		if (strpos($config, 'desc') !== FALSE) {
			$callback = 'sortDescBy';
		} else {
			$callback = 'sortAscBy';
		}
		if (strpos($config, 'attr') !== FALSE) {
			$callback = 'attr_' . $callback;
		}
		return $callback;
	}

	/**
	 * Calls sortAscByProperty
	 *
	 * @param	string	$name      Function name
	 * @param	array	$arguments Function arguments
	 * @return	integer
	 */
	public function __call($name, $arguments) {
		$sortDesc = FALSE;
		$useAttributeText = FALSE;
		if (strpos($name, 'attr_') === 0) {
			$useAttributeText = TRUE;
			$name = str_replace('attr_', '', $name);
		}

		if (strpos($name,'sortAscBy') !== FALSE) {
			$propertyKey = self::lcfirst(str_replace('sortAscBy', '', $name));
		} else if (strpos($name,'sortDescBy') !== false || strpos($name, 'sortDscBy') !== FALSE) {
			$propertyKey = self::lcfirst(str_replace(array('sortDscBy', 'sortDescBy'), '', $name));
			$sortDesc = TRUE;
		} else if (strpos($name,'sortBy') !== FALSE) {
			$propertyKey = self::lcfirst(str_replace('sortBy', '', $name));
		} else {
			echo 'No method found with name $name in class ' . get_class($this) . '<br />';
			return FALSE;
		}

		return $this->sortByProperty($propertyKey, $arguments[0], $arguments[1], $sortDesc, $useAttributeText);
	}

	/**
	 * Calls sortAscByProperty
	 *
	 * @param	string	$name      Function name
	 * @param	array	$arguments Function arguments
	 * @return	integer
	 */
	static public function __callStatic($name, $arguments) {
		if (!self::$_instance) {
			if (is_callable('get_called_class')) {
				$class = get_called_class();
			}
			self::$_instance = new $class();
		}

		return self::$_instance->__call($name, $arguments);
	}

	/**
	 * Makes the first character of a string lower case.
	 *
	 * @param	string	$string Input string
	 * @return	string
	 */
	static public function lcfirst($string) {
		$firstChar = strtolower(substr($string,0,1));
		$nextChars = substr($string,1,strlen($string)-1);
		return $firstChar.$nextChars;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* DEBUGGING & PROFILING  WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/**
	 * Debug a message.
	 *
	 * @param	string	$message
	 * @return	void
	 */
	static public function debug($message) {
		$forceDebug = FALSE;
		if ( (array_key_exists('REMOTE_ADDR', $_SERVER) && $_SERVER['REMOTE_ADDR'] == '188.82.98.197' ) || (array_key_exists('irsortdebug', $_GET) && $_GET['irsortdebug']) ) {
			if (array_key_exists('irsortdebug', $_GET) && $_GET['irsortdebug']) {
				$forceDebug = TRUE;
			}
		}

		if (self::$_debug == self::DEBUG_ECHO || $forceDebug) {
			echo "$message<br />";
		} else if (self::$_debug == self::DEBUG_TRIGGER) {
			trigger_error($message,E_USER_WARNING);
		}
	}

	/**
	 * Profile a message.
	 *
	 * @param	string	$message
	 * @return	void
	 */
	static public function profile($message) {
		$ipOk = FALSE;
		if (array_key_exists('REMOTE_ADDR', $_SERVER) && $_SERVER['REMOTE_ADDR'] == '188.82.98.197') {
			$ipOk = TRUE;
		}

		if (self::$_debug == self::DEBUG_ECHO || $ipOk) {
			echo "$message<br />";
		}
	}

	/**
	 * Prints a message with an intend that visualizes the current subgroup level.
	 *
	 * @param	string|object $message
	 *
	 * @return	void
	 */
	public function debugSubgroup($message) {
		$ipOk = FALSE;
		if (array_key_exists('REMOTE_ADDR', $_SERVER) && $_SERVER['REMOTE_ADDR'] == '188.82.98.197' || (array_key_exists('irsortdebug', $_GET) && $_GET['irsortdebug']) ) {
			$ipOk = TRUE;
		}


		if (self::$_debug == self::DEBUG_NONE && !$ipOk) return;
		if (is_array($message)) {
			foreach ($message as $irpd) {
				for($i = 0;$i < $this->_subgroupLevel;$i++) {
					echo '-----&nbsp&nbsp;';
				}
				if (is_a($irpd,'Varien_Object')) {
					echo "'{$irpd->getSku()}' \t#".$irpd->getId()." \tsurf.:".$irpd->getAttributeText('oberflaeche')." \tØ:{$irpd->getData('durchmesser_ca_mm')} \tpck:(".$irpd->getPackung().")";
				}
				echo '<br />';
			}
		} else {
			for($i = 0;$i < $this->_subgroupLevel;$i++) {
				echo '-----&nbsp&nbsp;';
			}
			echo "$message<br />";
		}
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* IMPLEMENTATION DEPENDENT ABSTRACT METHODS MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW */
	/**
	 * Invokes the getter method on the given object. If no getter method is specified
	 * or the given value is NULL _getProperty() will be called.
	 *
	 * @param	object	$object       The object to get the value from
	 * @param	string	$propertyKey  The name of the property
	 * @param	string	$getterMethod	 The name of the method to be used
	 * @return	mixed
	 */
	abstract protected function _invokeGetterMethodOnObject($object, $propertyKey, $getterMethod = NULL);
}