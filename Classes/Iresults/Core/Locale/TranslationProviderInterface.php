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
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 11:41
 */


namespace Iresults\Core\Locale;

/**
 * Interface for Translation Providers which search for translations in the
 * sources they can handle (i.e. CSV files are handled by the CsvTranslationProvider)
 *
 * Think of the Translation Providers as a backend for the Translator
 *
 * @package Iresults\Core\Locale
 */
interface TranslationProviderInterface extends BindingInterface
{
    /**
     * Language default key
     */
    const PACKAGE_DEFAULT = 'default';

    /**
     * Translates the given message
     *
     * @param string $message The message to translate
     * @param string $locale  Locale to use for this translation
     * @return string
     */
    public function translate($message, $locale = null);

    /**
     * Sets the source to read the translations from
     *
     * @param mixed $source
     * @return void
     */
    public function setSource($source);
}