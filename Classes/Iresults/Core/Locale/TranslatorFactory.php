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
 *         Created 03.10.13 14:28
 */


namespace Iresults\Core\Locale;


use Iresults\Core\Iresults;

/**
 * Factory class for Translators
 *
 * @package Iresults\Core\Locale
 */
abstract class TranslatorFactory {
	/**
	 * Returns a translator with the given source path
	 *
	 * @param  string $sourcePath
	 * @param  string $package
	 * @throws \UnexpectedValueException if an unsupported file URL is given
	 * @return    \Iresults\Core\Locale\TranslatorInterface
	 */
	static public function translatorWithSourcePath($sourcePath, $package = TranslationProviderInterface::PACKAGE_DEFAULT) {
		$translatorSuffix = static::searchSourcePathForTranslationFiles($sourcePath);
		$translatorClassName = '\\Iresults\\Core\\Locale\\TranslationProvider\\' . ucfirst($translatorSuffix) . 'TranslationProvider';

		if (!class_exists($translatorClassName)) {
			throw new \UnexpectedValueException('No Translation Provider found for source "' . $sourcePath . '"', 1380804058);
		}
		/** @var TranslationProviderInterface $translatorInstance */
		$translatorInstance = new $translatorClassName($package);
		$translatorInstance->setSource($sourcePath);
		return new Translator($translatorInstance);
	}

	/**
	 * Returns a translator with the given source
	 *
	 * @param mixed $source
	 * @return TranslatorInterface
	 * @throws \UnexpectedValueException if no Translator was found for the given source
	 */
	static public function translatorWithSource($source) {
		if (is_string($source)) {
			return static::translatorWithSourcePath($source);
		} else if (is_array($source)) {
		}
		throw new \UnexpectedValueException('No Translator found for source', 1380804057);
	}

	/**
	 * Returns a translator for the current or given package
	 *
	 * @param string $package
	 * @return TranslatorInterface
	 * @throws \UnexpectedValueException if no Translator was found for the given source
	 */
	static public function translatorWithPackage($package = '') {
		if (!$package) {
			$package = Iresults::getNameOfCallingPackage(TRUE);
		}
		$packagePath = Iresults::getPackagePath($package) . 'Resources/Private/Language/';
		return static::translatorWithSourcePath($packagePath, $package);
	}

	/**
	 * Checks the source path for translation files
	 *
	 * @param $sourcePath
	 * @return string|bool Returns the translator suffix to use, or FALSE if none was found
	 */
	static protected function searchSourcePathForTranslationFiles($sourcePath) {
		$translatorSuffix = array('csv', 'not-found');
		$currentTranslatorSuffix = reset($translatorSuffix);
		while ($currentTranslatorSuffix && !glob($sourcePath . '*.locallang.' . $currentTranslatorSuffix, GLOB_NOSORT)) {
			$currentTranslatorSuffix = next($translatorSuffix);
		}
		if ($currentTranslatorSuffix === 'not-found') {
			return FALSE;
		}
		return $currentTranslatorSuffix;
	}
}