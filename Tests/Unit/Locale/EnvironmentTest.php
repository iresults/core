<?php

namespace Iresults\Core\Tests\Unit\Locale;
use Iresults\Core\Iresults;
use Iresults\Core\Locale\Environment;

/**
 * Test case for the locale translator
 *
 * @author     Daniel Corn <cod@iresults.li>
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
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
    public function getInitialLocaleTest()
    {
        $locale = setlocale(LC_CTYPE, '0');
        if ($locale === 'C') {
            $locale = Iresults::getLocale();
        }
        if ($locale === 'UTF-8') {
            $locale = Iresults::getLocale() . '.UTF-8';
        }

        $this->assertEquals($locale, Environment::getSharedInstance()->getLocale());
    }

    /**
     * @test
     */
    public function setLocaleTest()
    {
//		$newLocale = 'ne_NP.UTF-8';
        $newLocale = 'de_DE.UTF-8';

        Environment::getSharedInstance()->setLocale($newLocale);
        $this->assertEquals($newLocale, Environment::getSharedInstance()->getLocale());
        $this->assertEquals($newLocale, setlocale(LC_CTYPE, '0'));
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Locale\Exception\LocaleException
     */
    public function executeWithInvalidLocaleTest()
    {
        $newLocale = 'ir_ES.UTF-8';
        Environment::getSharedInstance()->executeWithLocale($newLocale, 'time');
    }

    /**
     * @test
     */
    public function executeWithLocaleAndCallableTest()
    {
        $newLocale = 'de_DE.UTF-8';

        $result = Environment::getSharedInstance()->executeWithLocale(
            $newLocale,
            function () {
                return setlocale(LC_CTYPE, '0');
            }
        );
        $this->assertEquals($newLocale, $result);
        $this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
        $this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
    }

    /**
     * @test
     */
    public function executeWithLocaleAndCallableTimeFormatTest()
    {
        $newLocale = 'nl_NL.UTF-8';

        $result = Environment::getSharedInstance()->executeWithLocale(
            $newLocale,
            function () {
                return strftime("%A %e %B %Y", mktime(0, 0, 0, 12, 22, 1978));
            }
        );
        $this->assertEquals('vrijdag 22 december 1978', $result);
        $this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
        $this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
    }

    /**
     * @test
     */
    public function executeWithLocaleAndArrayWithoutArgumentsTest()
    {
        $newLocale = 'de_DE.UTF-8';

        $result = Environment::getSharedInstance()->executeWithLocale(
            $newLocale,
            [
                [$this, 'functionWithoutArguments'],
            ]
        );
        $this->assertEquals($newLocale, $result);
        $this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
        $this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
    }

    /**
     * @test
     */
    public function executeWithLocaleAndArrayWithArgumentsTest()
    {
        $newLocale = 'de_DE.UTF-8';

        $result = Environment::getSharedInstance()->executeWithLocale(
            $newLocale,
            [
                [$this, 'functionWithArguments'],
                ['My current locale: '],
            ]
        );
        $this->assertEquals('My current locale: ' . $newLocale, $result);
        $this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
        $this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
    }

    public function functionWithoutArguments()
    {
        return setlocale(LC_CTYPE, '0');
    }

    public function functionWithArguments($arg0)
    {
        return $arg0 . setlocale(LC_CTYPE, '0');

    }
}
