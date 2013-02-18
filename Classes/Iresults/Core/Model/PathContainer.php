<?php
namespace Iresults\Core\Model;

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
 * The iresults path container allows the storage, analysis and finding of
 * objects assigned to any kind of paths, including property key paths, tree
 * branches and similar.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Model
 */
class PathContainer extends \Iresults\Core\Model\PathAccess\AbstractContainer implements \Iresults\Core\Model\PathContainerInterface {
	/**
	 * Initializes a path container instance with the data from the given array.
	 *
	 * @param	array	$array The associative array|dictionary to read the data from
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 *
	 * @throws InvalidArgumentException if the given value is not an object.
	 */
	public function initWithArray($array) {
		foreach ($array as $key => $value) {
			$key = ''.$key;
			if (!is_object($value)) {
				throw new InvalidArgumentException("The given value for key '$key' is not an object.", 1321542946);
			}
			$this->pathToObjectMap[$key] = $value;
			$this->hashToPathMap[spl_object_hash($value)] = $key;
		}
		return $this;
	}

	/**
	 * Initializes a path container instance with the data from the given
	 * mutable XML object.
	 *
	 * The XML file must have the following format:
	 *
	 *  <paths>
	 * 		<entry>
	 * 			<path>[1|2]??.??.??</path>
	 * 			<value>Wertzeichen</value>
	 * 		</entry>
	 * 		<entry>
	 * 			...
	 * 		</entry>
	 * 	</paths>
	 *
	 * @param	\Iresults\Core\Mutable\Xml	$mutable The mutable object from which to read the data
	 * @return	\Iresults\Core\Model\PathContainer
	 */
	public function initWithMutableFromXml(\Iresults\Core\Mutable\Xml $mutable) {
		$array = $mutable->getObjectForKey('entry');
		foreach ($array as $entry) {
			$key = $entry->getObjectForKey('path');
			$value = $entry->getObjectForKey('value');

			if (!is_object($value)) {
				$value = new \Iresults\Core\Value($value);
			// If the value doesn't have a path property save the path there.
			} else if ($value instanceof \Iresults\Core\KVCInterface && !$value->getObjectForKey('path')) {
				$value->setObjectForKey('path', $key);
			}
			$this->pathToObjectMap[$key] = $value;
			$this->hashToPathMap[spl_object_hash($value)] = $key;
		}
		return $this;
	}

	/**
	 * Factory method: Returns an empty path container instance.
	 *
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 */
	static public function container() {
		$container = NULL;
		if (IR_MODERN_PHP) {
			$container = new static();
		} else {
			$container = new self();
		}
		return $container;
	}

	/**
	 * Factory method: Returns a path container instance with the data from the
	 * given mutable.
	 *
	 * @param	\Iresults\Core\Mutable	$object The mutable object from which the data will be read
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 */
	static public function containerWithMutable($mutable) {
		if (is_a($mutable,'\Iresults\Core\Mutable\Xml')) {
			$container = self::container();
			$container->initWithMutableFromXml($mutable);
			return $container;
		}
		return self::containerWithArray($mutable);
	}

	/**
	 * Factory method: Returns a path container instance with the data from the
	 * given array.
	 *
	 * @param	array	$array The associative array|dictionary to read the data from
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 */
	static public function containerWithArray($array) {
		$container = self::container();
		$container->initWithArray($array);
		return $container;
	}
}