<?php

namespace Iresults\Core\Tests\Unit\Core;

use Iresults\Core\DateTime;

/**
 * Test case for functionality of the DateTime object
 *
 * @author     Daniel Corn <cod@iresults.li>
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Iresults\Core\DateTime
     */
    protected $fixture;

    public function setUp()
    {
        error_reporting(E_ALL);
        $this->fixture = new DateTime();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function isInstanceOfDateTime()
    {
        $this->assertInstanceOf('\\DateTime', $this->fixture);
        $this->assertInstanceOf('\\Iresults\\Core\\DateTime', $this->fixture);
    }

    /**
     * @test
     */
    public function isEqualToDateTime()
    {
        $iresultsTestObject = new DateTime();
        $phpTestObject = new \DateTime();

        $this->assertEquals($phpTestObject->getTimestamp(), $iresultsTestObject->getTimestamp());
        $this->assertEquals($phpTestObject->getTimezone(), $iresultsTestObject->getTimezone());
        $this->assertEquals($phpTestObject->format('r'), $iresultsTestObject->format('r'));
    }

    /**
     * @test
     */
    public function canCreateFromDifferentFormats()
    {
        $phpTestObject = new \DateTime('Wed, 20 Nov 2013 11:21:36 +0000');

        $iresultsTestObject = new DateTime(1384946496);
        $this->assertEquals($phpTestObject->getTimestamp(), $iresultsTestObject->getTimestamp());
        $this->assertEquals($phpTestObject->format('r'), $iresultsTestObject->format('r'));

        $iresultsTestObject = new DateTime('Wed, 20 Nov 2013 11:21:36 +0000');
        $this->assertEquals($phpTestObject->getTimestamp(), $iresultsTestObject->getTimestamp());
        $this->assertEquals($phpTestObject->format('r'), $iresultsTestObject->format('r'));
    }

    /**
     * @test
     */
    public function canCreateWithTimeZone()
    {
        $testTimeZone = 'Australia/Victoria';

        $iresultsTestObject = new DateTime('Wed, 20 Nov 2013 11:21:36', new \DateTimeZone($testTimeZone));
        $this->assertEquals($testTimeZone, $iresultsTestObject->getTimezone()->getName());

        $iresultsTestObject = new DateTime('now', new \DateTimeZone($testTimeZone));
        $this->assertEquals($testTimeZone, $iresultsTestObject->getTimezone()->getName());
    }
}
