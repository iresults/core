<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 13:56
 */


namespace Iresults\Core\Locale\TranslationProvider;


abstract class AbstractFileBasedTranslationProvider extends AbstractTranslationProvider
{
    /**
     * A dictionary of all loaded translations
     *
     * The array is built in the following format:
     *
     *  array(
     *    'translationFileSuffix' => array(
     *        'package' => array(
     *                'locale' => array(
     *                    'message 1' => 'translation 1'
     *                    'message 2' => 'translation 2'
     *                )
     *            )
     *    )
     *  )
     *
     *
     * @var array
     */
    static protected $translations = [];

    /**
     * Path to the translation source files
     *
     * @var string
     */
    protected $sourcePath = '';

    /**
     * Suffix of the managed translation files
     *
     * @var string
     */
    protected $translationFileSuffix = 'csv';

    /**
     * Translates the given message
     *
     * @param string $message The message to translate
     * @internal param string $package Package name
     * @return string
     */
    protected function findTranslation($message)
    {
        $translationsArray = $this->getTranslations();

        return $translationsArray[$message];
    }

    /**
     * Sets the source to read the translations from
     *
     * @param mixed $source
     * @return void
     */
    public function setSource($source)
    {
        $this->sourcePath = $source;
    }

    /**
     * Returns the array of translations for the given package and current locale
     *
     * @internal param string $package
     * @return array
     */
    public function getTranslations()
    {
        $locale = $this->getBoundLocale();
        $package = $this->getPackage();
        $suffix = $this->translationFileSuffix;
        if (!isset(static::$translations[$suffix])
            || !isset(static::$translations[$suffix][$package])
            || !isset(static::$translations[$suffix][$package][$locale])
        ) {
            $this->loadTranslations();
        }

        return static::$translations[$suffix][$package][$locale];
    }

    /**
     * Loads the translations for the given package and current locale
     *
     * @internal param string $package
     */
    protected function loadTranslations()
    {
        $package = $this->getPackage();
        $locale = $this->getBoundLocale();
        $suffix = $this->translationFileSuffix;
        $packageTranslations = $this->parseTranslationFile($this->getTranslationFilePath());

        if (!isset(static::$translations[$suffix])) {
            static::$translations[$suffix] = [];
        }
        if (!isset(static::$translations[$suffix][$package])) {
            static::$translations[$suffix][$package] = [];
        }
        static::$translations[$suffix][$package][$locale] = $packageTranslations;
    }

    /**
     * Returns the path to the translation file for the given package and current
     * locale
     *
     * @internal param string $package
     * @return string
     */
    protected function getTranslationFilePath()
    {
        $currentLocale = $this->getBoundLocale();

        // The current locale may be in the format "de_DE.UTF-8"
        $filePrefix = explode('.', $currentLocale);
        $filePrefix = $filePrefix[0];

        return $this->sourcePath . $filePrefix . '.locallang.' . $this->translationFileSuffix;
    }

    /**
     * Parses the translation file at the given path
     *
     * @param string $translationFilePath
     * @return array
     */
    abstract protected function parseTranslationFile($translationFilePath);
}