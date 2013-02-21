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
 * A wrapper for the DateTime object.
 *
 * @package	Iresults
 * @subpackage Core
 * @version 1.2.2
 */
class DateTime extends \DateTime {
	/**
	 * Defines if an exception is thrown if the input couldn't be parsed.
	 */
	const THROW_EXCEPTION_ON_BAD_INPUT = 0;

	/**
	 * If the input year is not clearly defined, this treshold is used to choose
	 * if a year belongs to the 19th or 20th century.
	 * Example:
	 * $input = 14
	 * $input < THOUSEND_THRESHOLD => year = 1914
	 *
	 * $input = 39
	 * $input > THOUSEND_THRESHOLD => year = 2039
	 */
	const THOUSEND_THRESHOLD = 22;

	/**
	 * The cache for the prepared input.
	 *
	 * @var string
	 */
	protected $_preparedInput = NULL;

	/**
	 * @var \Iresults\Core\Helpers\Locale\Date
	 */
	static protected $dateHelper = NULL;

	/**
	 * Constructor for a new DateTime object
	 *
	 * @param	string|DateTime $dateTime
	 * @return	\Iresults\Core\DateTime
	 */
	public function __construct($dateTime = NULL) {
		// If a argument is given try to parse it as a DateTime object
		if ($dateTime !== NULL) {
			/*
			 * If it is an object of type \Iresults\Core\DateTime get the raw property
			 * from the passed object and release the given object.
			 */
			if (is_object($dateTime) && is_a($dateTime, '\Iresults\Core\DateTime')) {
				parent::__construct($dateTime);
				$dateTime = NULL;
			} else
			/*
			 * If it is an object of type DateTime set the raw property directly.
			 */
			if (is_object($dateTime) && is_a($dateTime, '\DateTime')) {
				parent::__construct($dateTime);
			} else
			/*
			 * Else try to create a new DateTime object from the argument.
			 */
			if (is_int($dateTime) || (is_numeric($dateTime) && intval($dateTime) == $dateTime * 1 )) {
				$dateTime = '@' . $dateTime;
				parent::__construct($dateTime);
			} else
			/*
			 * Else try to create a new DateTime object from the argument.
			 */
			if (strtotime($dateTime) !== FALSE || strtotime($this->_prepareInput($dateTime)) !== FALSE) {
				$dateTime = $this->_prepareInput($dateTime);
				try {
					parent::__construct($dateTime);
				} catch (Exception $exception) {
					if (self::THROW_EXCEPTION_ON_BAD_INPUT) {
						throw $exception;
					}
				}
			} else
			/*
			 * Else bad input
			 */
			{
				return NULL;
			}
		} else {
			parent::__construct();
		}
		return $this;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ACCESSING DATA                        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the raw DateTime object or NULL if parsing the input didn't
	 * succeed.
	 * @return	DateTime|NULL Returns a DateTime object on success, otherwise NULL
	 * @deprecated
	 */
	public function getRaw() {
		return $this;
	}

	/**
	 * Returns a new DateTime relative to the current date and time
	 *
	 * @param  mixed $dateInterval
	 * @return  DateTime
	 */
	public function dateByAddingTimeInterval($dateInterval) {
		$newDate = clone $this;
		if (is_string($dateInterval)) {
			$dateInterval = new \DateInterval($dateInterval);
		} else if (is_integer($dateInterval)) {
			$dateInterval = new \DateInterval('PT' . $dateInterval . 'S');
		}
		$newDate->add($dateInterval);
		return $newDate;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* PREPARATION OF STRING INPUTS          MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * @param	string	$dateTime
	 */
	protected function _prepareInput($dateTime) {
		if ($this->_preparedInput) {
			return $this->_preparedInput;
		}

		if (preg_match('!\d\. +\d!', $dateTime)) {
			$dateTime = str_replace(' ', '', $dateTime);
		}

		/*
		 * Make sure the input has 13.11.1986 and not only 13.11.86
		 */
		if (strpos($dateTime, '.') !== FALSE && count(explode('.', $dateTime)) >= 3) {
			$dateParts = explode('.', $dateTime);
			$last = end($dateParts);
			if (is_numeric($last) && $last < 1001) {
				if ($last > self::THOUSEND_THRESHOLD) {
					$last = $last + 1900;
				} else {
					$last = $last + 2000;
				}
				array_pop($dateParts);
				$dateParts[] = $last;
			}
			$dateTime = implode('.', $dateParts);
		}

		/*
		 * Make sure the input has 09/11/1986 and not only 09/11/86
		 */
		if (strpos($dateTime, '/') !== FALSE && count(explode('/', $dateTime)) >= 3) {
			$dateParts = explode('/', $dateTime);
			$last = end($dateParts);
			if (is_numeric($last) && $last < 1001) {
				if ($last > self::THOUSEND_THRESHOLD) {
					$last = $last + 1900;
				} else {
					$last = $last + 2000;
				}
				array_pop($dateParts);
				$dateParts[] = $last;
			}
			$dateTime = implode('/', $dateParts);
		}


		/*
		 * Fix a bug where 11/04/2011 is even in the UK interpreted as Nov. 4th.
		 */
		if (count(explode('/', $dateTime)) >= 3
			&& (self::getDateHelper()->getCountryCode() == 'en_GB' || self::getDateHelper()->getCountryCode() == 'en_UK')
		  ) {
			$dateParts = explode('/', $dateTime);
			if (count($dateParts) !== 3) {
				trigger_error('Wanted to swap date parts of ' . $dateTime . ' in an UK environment. But the number of date parts (separated by "/"") is not 3.', E_WARNING);
			} else {
				$dateParts = array_reverse($dateParts);
				$dateTime = implode('/', $dateParts);
			}

		}
		$this->_preparedInput = $dateTime;
		return $dateTime;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* DATE HELPER AND CALL FORWARDING       MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the date helper instance.
	 *
	 * @return	\Iresults\Core\Helpers\Locale\Date
	 */
	static public function getDateHelper() {
		if (!self::$dateHelper && class_exists('\Iresults\Core\Helpers\Locale\Date', FALSE)) {
			self::$dateHelper = new \Iresults\Core\Helpers\Locale\Date();
		}
		return self::$dateHelper;
	}

	/**
	 * Returns a string representation of the object.
	 *
	 * @return	string    A string describing this object
	 */
	public function description() {
		static $useDateHelper = -1;

		// Check if the date helper should be used
		if ($useDateHelper === -1) {
			if (self::getDateHelper()) {
				// Test if the localization is installed correctly
				try{
					$description = self::getDateHelper()->format($this, \Iresults\Core\Helpers\Locale\Date::FORMAT_DATE_LONG);
					$useDateHelper = TRUE;
					return $description;
				} catch(Exception $e) {
					$useDateHelper = FALSE;
				}
			} else {
				$useDateHelper = FALSE;
			}
		}
		if ($useDateHelper) {
			return self::getDateHelper()->format($this, \Iresults\Core\Helpers\Locale\Date::FORMAT_DATE_LONG);
		}
		return '' . $this->format('Y-m-d');
	}
	public function __toString() {
		return $this->description();
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS                       MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
}
