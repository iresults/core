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
 * The iresults sort helper provides different methods to sort arrays of
 * objects. It also enables you to detect and retrieve subgroups of object.
 * Subgroups are arrays of objects that share the same property value for a
 * given property key.
 *
 * @package	Iresults
 * @subpackage Helpers
 * @version 1.5
 */
class SortHelper extends SortHelperAbstract {
	/**
	 * Invokes the getter method on the given object. If no getter method is specified
	 * or the given value is NULL _getProperty() will be called.
	 *
	 * @param	object	$object       The object to get the value from
	 * @param	string	$propertyKey  The name of the property
	 * @param	string	$getterMethod	 The name of the method to be used
	 * @return	mixed
	 */
	protected function _invokeGetterMethodOnObject($object, $propertyKey, $getterMethod = NULL) {
		if ($getterMethod === NULL) {
			return ObjectHelper::getObjectForKeyPathOfObject($propertyKey, $object);
		}

		//\Iresults\Core\Iresults::pd($propertyKey,$getterMethod,$object,call_user_func_array(array($object,$getterMethod), array($propertyKey)));

		if (method_exists($object, $getterMethod)) {
			return call_user_func_array(array($object, $getterMethod), array($propertyKey));
		} else {
			return NULL;
		}
	}
}