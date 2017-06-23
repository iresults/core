<?php
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