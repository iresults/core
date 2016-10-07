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

use Iresults\Core\Tests\Fixture\ObjectTestObject;
use Iresults\Core\Tests\Fixture\ObjectTestObject2;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for functionality of the Core object
 *
 * @author     Daniel Corn <cod@iresults.li>
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectTestObject
     */
    protected $fixture;

    public function setUp()
    {
        error_reporting(E_ALL);
        $this->fixture = new ObjectTestObject();
    }

    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function isInstanceOfCoreObject()
    {
        $this->assertTrue(is_a($this->fixture, '\Iresults\Core\Core'));
    }

    /**
     * @test
     */
    public function canAddDynamicMethod()
    {
        $this->fixture->newMethod = function () {
            return 'hello';
        };
        $result = $this->fixture->newMethod();
        $this->assertEquals('hello', $result);
    }

    /**
     * @test
     */
    public function canAddDynamicMethodWithArguments()
    {
        $this->fixture->newMethod = function ($name) {
            return 'hello ' . $name;
        };
        $result = $this->fixture->newMethod('world');
        $this->assertEquals('hello world', $result);
    }

    /**
     * @test
     */
    public function canAddInstanceMethodForSelector()
    {
        ObjectTestObject::_instanceMethodForSelector(
            'canAddInstanceMethodForSelector',
            function () {
                return 'hello world';
            }
        );
        $result = $this->fixture->canAddInstanceMethodForSelector();
        $this->assertEquals('hello world', $result);
    }

    /**
     * @test
     */
    public function canAddInstanceMethodForSelectorWithoutOverriding()
    {
        ObjectTestObject::_instanceMethodForSelector(
            'canAddInstanceMethodForSelectorWithoutOverriding',
            function () {
                return 'object 1';
            }
        );
        ObjectTestObject2::_instanceMethodForSelector(
            'canAddInstanceMethodForSelectorWithoutOverriding',
            function () {
                return 'object 2';
            }
        );
        $result = $this->fixture->canAddInstanceMethodForSelectorWithoutOverriding();
        $this->assertEquals('object 1', $result);
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Exception\UndefinedMethod
     */
    public function canAddInstanceMethodForSelectorWithoutInheriting()
    {
        \Iresults\Core\Core::_instanceMethodForSelector(
            'canAddInstanceMethodForSelectorWithoutInheriting',
            function () {
                return 'hello world';
            }
        );
        $this->fixture->canAddInstanceMethodForSelectorWithoutInheriting();
    }
}
