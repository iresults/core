<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 02.10.13 14:01
 */


namespace Iresults\Core\Locale;

/**
 * Interface for a Translator utilizing a Translation Provider to retrieve
 * translated messages
 *
 * @package Iresults\Core\Locale
 */
interface TranslatorInterface extends BindingInterface
{
    /**
     * Translates the given message
     *
     * @param string $message   The message to translate
     * @param array  $arguments Arguments to be used in the message
     * @param string $locale    Locale to use for this translation
     * @return string
     */
    public function translate($message, $arguments = null, $locale = null);
}