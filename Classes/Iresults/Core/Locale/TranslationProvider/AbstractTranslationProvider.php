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
 * Created 03.10.13 11:48
 */


namespace Iresults\Core\Locale\TranslationProvider;


use Iresults\Core\Iresults;
use Iresults\Core\Locale\Environment;
use Iresults\Core\Locale\TranslationProviderInterface;

/**
 * An abstract Translation Provider
 *
 * @package Iresults\Core\Locale\TranslationProvider
 */
abstract class AbstractTranslationProvider implements TranslationProviderInterface {
	/**
	 * The locale to use for translation
	 *
	 * @var string
	 */
	protected $locale = '';

	/**
	 * The current package name
	 *
	 * @var string
	 */
	protected $package = self::PACKAGE_DEFAULT;

	/**
	 * Translates the given message
	 *
	 * @param string $message The message to translate
	 * @return string
	 */
	abstract protected function findTranslation($message);

	/**
	 * Constructor
	 */
	function __construct($package = self::PACKAGE_DEFAULT) {
		$this->package = $package;
		$this->locale = Environment::getSharedInstance()->getLocale();
	}

	/**
	 * Returns the locale to use for translation
	 *
	 * @return string
	 */
	public function getLocale() {
		if (!$this->locale) {
			$this->locale = Environment::getSharedInstance()->getLocale();
		}
		return $this->locale;
	}

	/**
	 * Defines the locale to use for translation
	 *
	 * @param string $locale
	 * @return $this
	 */
	public function setLocale($locale) {
		$this->locale = $locale;
		return $this;
	}

	/**
	 * Translates the given message
	 *
	 * @param string $message         The message to translate
	 * @param string $locale          Locale to use for this translation
	 * @return string
	 */
	public function translate($message, $locale = NULL) {
		$previousLocale = FALSE;

		// Set the environment to the temporary locale
		if ($locale !== NULL) {
			$previousLocale = $this->getLocale();
			$this->setLocale($locale);
			$this->setEnvironment($locale);
		}

		// Get the translated message
		$translatedMessage = $this->findTranslation($message);

		// Reset the environment to the previous locale
		if ($previousLocale) {
			$this->setEnvironment($previousLocale);
		}
		return $translatedMessage;
	}

	/**
	 * Sets the locale environment
	 *
	 * @param string $locale
	 */
	protected function setEnvironment($locale) {
		Environment::getSharedInstance()->setLocale($locale);
	}

	/**
	 * Returns the current package name or 'default' if it isn't set
	 *
	 * @return string
	 */
	protected function getPackage() {
		return $this->package;
	}
}