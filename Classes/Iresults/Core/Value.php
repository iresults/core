<?php
namespace Iresults\Core;

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
 * The iresults value class is a simple container for any kind of data.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Model
 */
class Value extends \Iresults\Core\Core {
	/**
	 * The value.
	 *
	 * @var mixed
	 */
	protected $value = NULL;

	/**
	 * The constructor
	 *
	 * @param	mixed	$value The value of the object
	 * @return	Iresults_Value
	 */
	public function __construct($value = NULL) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Returns the value of the object.
	 *
	 * @return	mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Returns the default description.
	 *
	 * @return	string    A string describing this object
	 */
	public function description() {
		return '' . Iresults::descriptionOfValue($this->value);
		return get_class($this) . Iresults::descriptionOfValue($this->value);
	}

	/**
	 * Factory method: Returns a new value object with the given value.
	 *
	 * @param	mixed	$value The value of the object
	 * @return	\Iresults\Core\Value
	 */
	static public function valueObjectWithValue($value) {
		return new static($value);
	}
}