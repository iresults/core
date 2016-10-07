<?php
namespace Iresults\Core\Tests\Unit\Core;

/*
 * The MIT License (MIT)
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use Iresults\Core\Cache\AbstractCache;
use Iresults\Core\Cache\Factory;

require_once __DIR__ . '/../Autoloader.php';

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

