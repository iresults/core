<?php
/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
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
 * Created 03.10.13 17:51
 */


namespace Iresults\Core\Locale;


use Iresults\Core\Iresults;

/**
 * Manager for the locale environment
 *
 * @package Iresults\Core\Locale
 */
class Environment {
	/**
	 * The current locale
	 * @var string
	 */
	protected $locale;

	/**
	 * Shared instance
	 *
	 * @var Environment
	 */
	static protected $sharedInstance;

	/**
	 *
	 */
	function __construct() {
		$locale = setlocale(LC_CTYPE, '0');
		if ($locale === 'C') {
			$locale = Iresults::getLocale();
		}
		$this->setLocale($locale);
	}


	/**
	 * Sets the current locale
	 *
	 * @param string $locale
	 */
	public function setLocale($locale) {
		$this->locale = $locale;
		putenv('LC_ALL=' . $locale);
		setlocale(LC_ALL, $locale);
	}

	/**
	 * Returns the current locale
	 *
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * Returns the shared instance
	 *
	 * @return Environment
	 */
	static public function getSharedInstance() {
		if (!static::$sharedInstance) {
			static::$sharedInstance = new static();
		}
		return static::$sharedInstance;
	}
}