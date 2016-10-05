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
 * @package    Iresults
 * @subpackage Core
 * @version    1.2.2
 */
class DateTime extends \DateTime
{
    /**
     * Defines if an exception is thrown if the input couldn't be parsed.
     */
    const THROW_EXCEPTION_ON_BAD_INPUT = 0;

    /**
     * If the input year is not clearly defined, this threshold is used to choose
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
    protected $_preparedInput = null;

    /**
     * @var \Iresults\Core\Helpers\Locale\Date
     */
    static protected $dateHelper = null;

    /**
     * Constructor for a new DateTime object
     *
     * @param string|\DateTime $time
     * @param \DateTimeZone    $timezone
     * @throws \Exception if the input could not be transformed
     */
    public function __construct($time = 'now', \DateTimeZone $timezone = null)
    {
        if ($timezone === null) {
            $timezone = new \DateTimeZone(date_default_timezone_get());
        }
        if ($time === null) {
            $time = 'now';
        }

        // If a argument is given try to parse it as a DateTime object
        if ($time !== 'now') {
            /*
             * If it is an object of type \Iresults\Core\DateTime get the raw property
             * from the passed object and release the given object.
             */
            if (is_object($time) && is_a($time, '\DateTime')) {
                parent::__construct($time->format('r'), $timezone);
                $time = null;
            } else /*
				 * Else try to create a new DateTime object from the argument.
				 */ {
                if (is_int($time) || (is_numeric($time) && intval($time) == $time * 1)) {
                    $time = '@' . $time;
                    parent::__construct($time, $timezone);
                } else /*
					 * Else try to create a new DateTime object from the argument.
					 */ {
                    if (strtotime($time) !== false || strtotime($this->_prepareInput($time)) !== false) {
                        $time = $this->_prepareInput($time);
                        try {
                            parent::__construct($time, $timezone);
                        } catch (\Exception $exception) {
                            if (self::THROW_EXCEPTION_ON_BAD_INPUT) {
                                throw $exception;
                            }
                        }
                    } else /*
			 * Else bad input
			 */ {
                        return null;
                    }
                }
            }
        } else {
            parent::__construct($time, $timezone);
        }

        return $this;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESSING DATA                        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns a new DateTime relative to the current date and time
     *
     * @param  mixed $dateInterval
     * @return  DateTime
     */
    public function dateByAddingTimeInterval($dateInterval)
    {
        $newDate = clone $this;
        if (is_string($dateInterval)) {
            $dateInterval = new \DateInterval($dateInterval);
        } elseif (is_integer($dateInterval)) {
            $dateInterval = new \DateInterval('PT' . $dateInterval . 'S');
        }
        $newDate->add($dateInterval);

        return $newDate;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* PREPARATION OF STRING INPUTS          MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Prepares the date input string
     *
     * @param    string $dateTime
     * @return string
     */
    protected function _prepareInput($dateTime)
    {
        if ($this->_preparedInput) {
            return $this->_preparedInput;
        }

        if (preg_match('!\d\. +\d!', $dateTime)) {
            $dateTime = str_replace(' ', '', $dateTime);
        }

        /*
         * Make sure the input has 13.11.1986 and not only 13.11.86
         */
        if (strpos($dateTime, '.') !== false && count(explode('.', $dateTime)) >= 3) {
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
        if (strpos($dateTime, '/') !== false && count(explode('/', $dateTime)) >= 3) {
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
            && (Iresults::getLocale() == 'en_GB' || Iresults::getLocale() == 'en_UK')
        ) {
            $dateParts = explode('/', $dateTime);
            if (count($dateParts) !== 3) {
                trigger_error(
                    'Wanted to swap date parts of ' . $dateTime . ' in an UK environment. But the number of date parts (separated by "/"") is not 3.',
                    E_WARNING
                );
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
     * @return    \Iresults\Core\Helpers\Locale\Date
     */
    static public function getDateHelper()
    {
        if (!self::$dateHelper && class_exists('\Iresults\Core\Helpers\Locale\Date', false)) {
            self::$dateHelper = new \Iresults\Core\Helpers\Locale\Date();
        }

        return self::$dateHelper;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return    string    A string describing this object
     */
    public function description()
    {
        static $useDateHelper = -1;

        // Check if the date helper should be used
        if ($useDateHelper === -1) {
            if (self::getDateHelper()) {
                // Test if the localization is installed correctly
                try {
                    $description = self::getDateHelper()->format(
                        $this,
                        \Iresults\Core\Helpers\Locale\Date::FORMAT_DATE_LONG
                    );
                    $useDateHelper = true;

                    return $description;
                } catch (\Exception $e) {
                    $useDateHelper = false;
                }
            } else {
                $useDateHelper = false;
            }
        }
        if ($useDateHelper) {
            return self::getDateHelper()->format($this, \Iresults\Core\Helpers\Locale\Date::FORMAT_DATE_LONG);
        }

        return '' . $this->format('Y-m-d');
    }

    public function __toString()
    {
        return $this->description();
    }
}
