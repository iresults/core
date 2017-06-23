<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 15:20
 */


namespace Iresults\Core\Locale;

use Iresults\Core\Locale\TranslationProvider\InvalidTranslationFileException;

/**
 * Translator class which will query the configured Translation Provider for
 * translated messages
 *
 * @package Iresults\Core\Locale
 */
class Translator implements TranslatorInterface
{
    /**
     * Collection of Translation Providers
     *
     * @var array<TranslationProviderInterface>
     */
    protected $translationProviderCollection;

    /**
     * Sets the locale this Translator is bound to
     *
     * @var string
     */
    protected $boundLocale = '';

    /**
     * Initialize a new Translator
     *
     * @param TranslationProviderInterface $translationProvider
     */
    function __construct($translationProvider = null)
    {
        $this->translationProviderCollection[] = $translationProvider;
    }

    /**
     * Translates the given message
     *
     * @param string $message   The message to translate
     * @param array  $arguments Arguments to be used in the message
     * @param string $locale    Locale to use for this translation
     * @throws \Exception|TranslationProvider\InvalidTranslationFileException
     * @return string
     */
    public function translate($message, $arguments = null, $locale = null)
    {
        $translatedMessage = false;
        foreach ($this->getTranslationProviderCollection() as $translationProvider) {
            /** @var TranslationProviderInterface $translationProvider */
            try {
                $translatedMessage = $translationProvider->translate($message, $locale);
            } catch (InvalidTranslationFileException $exception) {
            }
        }
        if (!$translatedMessage) {
            $translatedMessage = $message;
        }
        if ($arguments) {
            $translatedMessage = vsprintf($translatedMessage, $arguments);
        }

        return $translatedMessage;
    }

    /**
     * Sets the collection of Translation Providers
     *
     * @param array <TranslationProviderInterface> $translationProviderCollection
     */
    public function setTranslationProviderCollection($translationProviderCollection)
    {
        $this->translationProviderCollection = $translationProviderCollection;
    }

    /**
     * Returns the collection of Translation Providers
     *
     * @return array<TranslationProviderInterface>
     */
    public function getTranslationProviderCollection()
    {
        return $this->translationProviderCollection;
    }

    /**
     * Adds the Translation Provider to the collection
     *
     * @param \Iresults\Core\Locale\TranslationProviderInterface $translationProvider
     */
    public function addTranslationProvider($translationProvider)
    {
        if ($this->getBoundLocale()) {
            $translationProvider->bindToLocale($this->getBoundLocale());
        }
        $this->translationProviderCollection[] = $translationProvider;
    }

    /**
     * Sets the locale this Translator is bound to
     *
     * This method is used to "lock" the Translator (and it's Translation
     * Providers) to the given locale. If it isn't set, the objects will read
     * the locale from \Locale\Environment
     *
     * @param string $locale
     * @return $this
     */
    public function bindToLocale($locale)
    {
        $this->boundLocale = $locale;

        /** @var TranslationProviderInterface $translationProvider */
        foreach ($this->getTranslationProviderCollection() as $translationProvider) {
            $translationProvider->bindToLocale($locale);
        }

        return $this;
    }

    /**
     * Returns the locale this Translator is bound to
     *
     * @return string
     */
    public function getBoundLocale()
    {
        return $this->boundLocale;
    }


}