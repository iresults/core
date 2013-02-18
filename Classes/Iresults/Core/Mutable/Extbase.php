<?php
namespace Iresults\Core\Mutable;

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
 * The concrete implementation class for mutable objects that implement Tx_Extbase_DomainObject_DomainObjectInterface.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class Extbase extends \Iresults\Core\Mutable\Simple {
	/**
	 * Getter for uid.
	 * @return	int the uid or NULL if none set yet
	 */
	public function getUid() {
		return $this->getObjectForKey('uid');
	}

	/**
	 * Setter for the pid.
	 * @return	void
	 */
	public function setPid($pid) {
		$this->setObjectForKey('pid',$pid);
	}

	/**
	 * Getter for the pid.
	 * @return	int The pid or NULL if none set yet
	 */
	public function getPid() {
		return $this->getObjectForKey('pid');
	}

	/**
	 * Returns TRUE if the object is new (the uid was not set, yet). Only for internal use
	 * @return	boolean
	 */
	public function _isNew() {
		if ($this->getObjectForKey('uid')) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Reconstitutes a property. Only for internal use.
	 *
	 * @param	string	$propertyName
	 * @param	string	$value
	 * @return	void
	 */
	public function _setProperty($propertyName, $value) {
		//throw new Exception("DODO");
		$this->setObjectForKey($propertyName,$value);
	}

	/**
	 * Returns the property value of the given property name. Only for internal use.
	 * @return	mixed The propertyValue
	 */
	public function _getProperty($propertyName) {
		return $this->getObjectForKey($propertyName);
	}

	/**
	 * Returns a hash map of property names and property values
	 * @return	array The properties
	 */
	public function _getProperties() {
		return $this->getData();
	}
}