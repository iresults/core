<?php
namespace Iresults\Core\Helpers\Object;

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
 * The iresults object Mutable Helper provides functions to transform mutable
 * objects.
 *
 * @package Iresults
 * @subpackage Iresults_Helpers_Object
 * @version 1.5
 */
class MutableHelper extends \Iresults\Core\Core {
	/**
	 * Transforms each property of the given mutable into an object.
	 *
	 * @param	\Iresults\Core\Mutable	$mutable The mutable whose properties to transform
	 * @param	boolean	$recursive	 Set to TRUE if you want the transformation recursively
	 * @return	void
	 */
	static public function transformPropertiesOfMutableToObjects($mutable, $recursive = FALSE) {
		$mutableData = NULL;
		if (is_array($mutable)) {
			$mutableData = $mutable;
		} else {
			$mutableData = $mutable->getData();
		}

		foreach ($mutableData as $key => $value) {
			$newValue = NULL;

			/*
			 * If value isn't an object, try to create a new object of the best
			 * kind.
			 */
			if (!is_object($value)) {
				if (is_resource($value)) {
					throw new InvalidArgumentException("Cannot transform a resource into an object.", 1321634463);
				}
				$newValue = ObjectHelper::createObjectWithValue($value);
				if ($recursive && $newValue instanceof \Iresults\Core\Mutable) {
					self::transformPropertiesOfMutableToObjects($newValue, TRUE);
				}
				$mutable->setObjectForKey($key, $newValue);
			}
		}
	}

	/**
	 * Transforms each property of the given mutable into an object recursively.
	 *
	 * @param	\Iresults\Core\Mutable	$mutable The mutable whose properties to transform
	 * @return	void
	 */
	static public function transformPropertiesOfMutableToObjectsRecursive($mutable) {
		return self::transformPropertiesOfMutableToObjects($mutable, TRUE);
	}
}