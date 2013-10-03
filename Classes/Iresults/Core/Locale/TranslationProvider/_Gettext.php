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
 * Created 02.10.13 14:33
 */


namespace Iresults\Core\Locale\TranslationProvider;

class Gettext extends AbstractTranslationProvider {
	/**
	 * An array of loaded packages whose Gettext domain was loaded
	 *
	 * @var array<string>
	 */
	static protected $loadedGettextPackages = array();

	/**
	 * Translates the given message
	 *
	 * @param string $message         The message to translate
	 * @param string $locale          Locale to use for this translation
	 * @param string $package         Extension name
	 * @internal param array $arguments Arguments to be used in the message
	 * @return string
	 */
	public function translate($message, $locale = NULL, $package = '') {
		// TODO: Implement translate() method.
	}

	/**
	 * Initialize Gettext for the current package
	 */
	protected function initializeGettextForCurrentPackage() {
		$currentPackage = $this->getCurrentPackage();
		if (!isset(self::$loadedGettextPackages[$currentPackage])) {
			// Angeben des Pfads der Übersetzungstabellen
			bindtextdomain($currentPackage, '');
		}
	}
}