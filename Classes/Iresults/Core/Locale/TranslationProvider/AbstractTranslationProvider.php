<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 11:48
 */


namespace Iresults\Core\Locale\TranslationProvider;

use Iresults\Core\Locale\Environment;
use Iresults\Core\Locale\TranslationProviderInterface;

/**
 * An abstract Translation Provider
 *
 * @package Iresults\Core\Locale\TranslationProvider
 */
abstract class AbstractTranslationProvider implements TranslationProviderInterface
{
    /**
     * The locale to use for translation
     *
     * @var string
     */
    protected $boundLocale = '';

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
    function __construct($package = self::PACKAGE_DEFAULT)
    {
        $this->package = $package;
    }

    /**
     * Returns the locale to use for translation
     *
     * @return string
     */
    public function getBoundLocale()
    {
        if (!$this->boundLocale) {
            return Environment::getSharedInstance()->getLocale();
        }

        return $this->boundLocale;
    }

    /**
     * Defines the locale to use for translation
     *
     * @param string $locale
     * @return $this
     */
    public function bindToLocale($locale)
    {
        $this->boundLocale = $locale;

        return $this;
    }

    /**
     * Translates the given message
     *
     * @param string $message The message to translate
     * @param string $locale  Locale to use for this translation
     * @return string
     */
    public function translate($message, $locale = null)
    {
        $previousLocale = false;

        // Set the environment to the temporary locale
        if ($locale !== null) {
            $previousLocale = $this->getBoundLocale();
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
    protected function setEnvironment($locale)
    {
        Environment::getSharedInstance()->setLocale($locale);
    }

    /**
     * Returns the current package name or 'default' if it isn't set
     *
     * @return string
     */
    protected function getPackage()
    {
        return $this->package;
    }
}