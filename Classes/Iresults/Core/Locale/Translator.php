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