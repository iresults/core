<?php
namespace Iresults\Core;

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
