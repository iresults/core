<?php

namespace Iresults\Core\Tests\Unit\Core;

use Iresults\Core\Cache\AbstractCache;
use Iresults\Core\Cache\Factory;

/**
 * Test case for functionality of the iresults Cache
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCache
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = Factory::getSharedInstance();
    }

    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function isInstanceOfCacheAbstract()
    {
        $this->assertTrue(is_a(Factory::getSharedInstance(), '\Iresults\Core\Cache\AbstractCache'));
        $this->assertTrue(is_a(Factory::makeInstance(), '\Iresults\Core\Cache\AbstractCache'));
    }

    /**
     * @test
     */
    public function setObjectForKey()
    {
        $this->fixture->setObjectForKey('age', 34);
        $age = $this->fixture->getObjectForKey('age');
        $this->assertEquals(34, $age);
    }

    /**
     * @test
     */
    public function getObjectForKeyOrPerformClosure()
    {
        $result = $this->fixture->getObjectForKeyOrPerformClosure(
            'new_key_' . time(),
            function () {
                return 'new_value';
            }
        );
        $this->assertEquals('new_value', $result);
    }

    /**
     * @test
     */
    public function getObjectForKeyOrPerformClosureAndSetValue()
    {
        $key = 'new_key_' . time();
        $this->fixture->getObjectForKeyOrPerformClosure(
            $key,
            function () {
                return 'new_value';
            },
            true
        );

        $result = $this->fixture->getObjectForKey($key);
        $this->assertEquals('new_value', $result);
    }

    /**
     * @test
     */
    public function getObjectForKeyOrPerformClosureAndDontSetValue()
    {
        $key = 'new_key_not_to_save_' . time();
        $this->fixture->getObjectForKeyOrPerformClosure(
            $key,
            function () {
                return 'new_value';
            },
            false
        );

        $result = $this->fixture->getObjectForKey($key);
        $this->assertNull($result);
    }
}

