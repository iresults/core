<?php

namespace Iresults\Core\Tests\Unit\Locale;

use Iresults\Core\Locale\Environment;
use Iresults\Core\Locale\TranslatorFactory;

/**
 * Test case for the locale translator
 *
 * @author     Daniel Corn <cod@iresults.li>
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    static protected $systemLocale;

    public function setUp()
    {
        if (!self::$systemLocale) {
            self::$systemLocale = Environment::getSharedInstance()->getLocale();
        }
    }

    public function tearDown()
    {
        Environment::getSharedInstance()->setLocale(self::$systemLocale);
    }

    /**
     * @test
     */
    public function getOriginalMessageTest()
    {
        $message = 'A simple translated string';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals($message, $translator->translate($message));
    }

    /**
     * @test
     */
    public function getTranslatedMessageTest()
    {
        $originalMessage = 'A simple translated string';
        $translatedMessage = 'Ein einfacher übersetzter Text';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals($translatedMessage, $translator->translate($originalMessage, null, 'de_DE'));

        $this->assertEquals($originalMessage, $translator->translate($originalMessage));
    }

    /**
     * @test
     */
    public function getOriginalMessageWithArgumentTest()
    {
        $message = 'My name is %s';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals('My name is Daniel', $translator->translate($message, ['Daniel']));
    }

    /**
     * @test
     */
    public function getTranslatedMessageWithArgumentTest()
    {
        $originalMessage = 'My name is %s';
        $translatedMessage = 'Mein Name ist Daniel';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals($translatedMessage, $translator->translate($originalMessage, ['Daniel'], 'de_DE'));
    }

    /**
     * @test
     */
    public function getTranslatedMessageWithUtf8LocaleTest()
    {
        $originalMessage = 'My name is %s';
        $translatedMessage = 'Mein Name ist Daniel';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals(
            $translatedMessage,
            $translator->translate($originalMessage, ['Daniel'], 'de_DE.UTF-8')
        );
    }

    /**
     * @test
     */
    public function getTranslatedMessageWithEnvironmentLocaleTest()
    {
        $originalMessage = 'A simple translated string';
        $translatedMessage = 'Ein einfacher übersetzter Text';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals($originalMessage, $translator->translate($originalMessage));

        Environment::getSharedInstance()->setLocale('de_DE.UTF-8');
        $this->assertEquals($translatedMessage, $translator->translate($originalMessage));
    }

    /**
     * @test
     */
    public function localeBindingTest()
    {
        $originalMessage = 'A simple translated string';
        $translatedMessage = 'Ein einfacher übersetzter Text';
        $translator = TranslatorFactory::translatorWithSource(__DIR__ . '/../SampleData/');
        $this->assertEquals($originalMessage, $translator->translate($originalMessage));

        // Set de_DE
        $translator->bindToLocale('de_DE.UTF-8');
        $this->assertEquals($translatedMessage, $translator->translate($originalMessage));

        // Still de_DE
        Environment::getSharedInstance()->setLocale('en_US');
        $this->assertEquals($translatedMessage, $translator->translate($originalMessage));

        // Set to en_US
        $translator->bindToLocale('en_US');
        $this->assertEquals($originalMessage, $translator->translate($originalMessage));

    }
}
