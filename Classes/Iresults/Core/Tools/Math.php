<?php
/*
 *  Copyright notice
 *
 *  (c) 2014 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author COD
 * Created 11.07.14 09:38
 */


namespace Iresults\Core\Tools;

use Iresults\Core\Tools\Exception\MathException;

/**
 * Utility class for mathematical operations
 *
 * @package Iresults\Core
 */
class Math {
	/**
	 * Defines the precision of calculations and comparison
	 *
	 * @var integer
	 */
	static protected $_precision = 9;

	/**
	 * Sets the precision of calculations and comparison
	 *
	 * @param int|\Iresults\Core\Tools\Math $precision
	 */
	static public function setPrecision($precision) {
		self::$_precision = $precision;
	}

	/**
	 * Returns the precision of calculations and comparison
	 *
	 * @return int|\Iresults\Core\Tools\Math
	 */
	static public function getPrecision() {
		return self::$_precision;
	}


	/**
	 * Add two arbitrary precision numbers
	 *
	 * @param int|float|string|bool $augend
	 * @param int|float|string|bool $addend
	 * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
	 * @return float|string
	 */
	static public function add($augend, $addend, $returnString = FALSE) {
		if (static::_useBCMath()) {
			$augend = static::_prepareValueForBCMath($augend);
			$addend = static::_prepareValueForBCMath($addend);
			$result = bcadd($augend, $addend, self::$_precision);
			if (!$returnString) {
				return floatval($result);
			}
			return $result;
		}
		return -1;
	}

	/**
	 * Subtract one arbitrary precision number from another
	 *
	 * @param int|float|string|bool $minuend
	 * @param int|float|string|bool $subtrahend
	 * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
	 * @return float|string
	 */
	static public function subtract($minuend, $subtrahend, $returnString = FALSE) {
		if (static::_useBCMath()) {
			$minuend = static::_prepareValueForBCMath($minuend);
			$subtrahend = static::_prepareValueForBCMath($subtrahend);
			$result = bcsub($minuend, $subtrahend, self::$_precision);
			if (!$returnString) {
				return floatval($result);
			}
			return $result;
		}
		return -1;
	}

	/**
	 * Multiply two arbitrary precision numbers
	 *
	 * @param int|float|string|bool $multiplicand
	 * @param int|float|string|bool $multiplier
	 * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
	 * @return float|string
	 */
	static public function multiply($multiplicand, $multiplier, $returnString = FALSE) {
		if (static::_useBCMath()) {
			$multiplicand = static::_prepareValueForBCMath($multiplicand);
			$multiplier = static::_prepareValueForBCMath($multiplier);
			$result = bcmul($multiplicand, $multiplier, self::$_precision);
			if (!$returnString) {
				return floatval($result);
			}
			return $result;
		}
		return -1;
	}

	/**
	 * Divide two arbitrary precision numbers
	 *
	 * @param int|float|string|bool $dividend
	 * @param int|float|string|bool $divisor
	 * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
	 * @return float|string
	 */
	static public function divide($dividend, $divisor, $returnString = FALSE) {
		if (static::_useBCMath()) {
			$dividend = static::_prepareValueForBCMath($dividend);
			$divisor = static::_prepareValueForBCMath($divisor);
			$result = bcdiv($dividend, $divisor, self::$_precision);
			if (!$returnString && $result !== NULL) {
				return floatval($result);
			}
			return $result;
		}
		return -1;
	}

	/**
	 * Compare two arbitrary precision numbers
	 *
	 * @param int|float|string|bool $a
	 * @param int|float|string|bool $b
	 *
	 * @return bool
	 */
	static public function almostEquals($a, $b) {
		if (static::_useBCMath()) {
			$a = static::_prepareValueForBCMath($a);
			$b = static::_prepareValueForBCMath($b);
			return bccomp($a, $b, self::$_precision) === 0;
		}
		return -1;
	}

	/**
	 * Prepares the given value for BC Math functions
	 *
	 * @param mixed $value
	 * @throws Exception\MathException if the value could not be prepared
	 * @return string
	 */
	static protected function _prepareValueForBCMath($value) {
		switch (TRUE) {
			case is_float($value):
				return number_format($value, self::$_precision, '.', '');

			case is_integer($value):
				return $value . '.00';

			case is_string($value) && is_numeric($value):
				return $value;

			case is_bool($value):
				return $value ? '1.00' : '0.00';

			case is_resource($value):
			case is_array($value):
			case is_object($value):
			default:
				throw new MathException('Could not prepare value of type ' . gettype($value) . ' for BC Math', 1405066983);
		}
	}

	/**
	 * Returns if BC Math should be used
	 *
	 * @throws \LogicException currently only BC Math is supported
	 * @return boolean
	 */
	static protected function _useBCMath() {
		if (!function_exists('bccomp')) throw new \LogicException('BC Math extension currently is required', 1405066207);
		return TRUE;
	}
}