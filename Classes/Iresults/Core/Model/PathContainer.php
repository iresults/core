<?php
namespace Iresults\Core\Model;

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
