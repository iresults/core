<?php

namespace Iresults\Core\Tests\Unit\Core;
use Iresults\Core\Tests\Fixture\ObjectTestObject;
use Iresults\Core\Tests\Fixture\ObjectTestObject2;

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
