<?php

namespace Iresults\Core\Tests\Unit\Core;
use Iresults\Core\Tests\Fixture\Person;

/**
 * Test case for Iresults_Model
 *
 * @author     Daniel Corn <cod@iresults.li>
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Person
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Person();
    }

    public function tearDown()
    {
    }


    /**
     * @test
     */
    public function getSimpleObjectForKeyWithoutAccessorMethod()
    {
        $age = $this->fixture->getObjectForKey('age');
        $this->assertEquals(26, $age);
    }

    /**
     * @test
     */
    public function setSimpleObjectForKeyWithoutAccessorMethod()
    {
        $this->fixture->setObjectForKey('age', 34);
        $age = $this->fixture->getObjectForKey('age');
        $this->assertEquals(34, $age);
    }

    /**
     * @test
     */
    public function removeSimpleObjectForKeyWithoutAccessorMethod()
    {
        $this->fixture->removeObjectForKey('age');
        $age = $this->fixture->getObjectForKey('age');
        $this->assertNull($age);
    }

    /**
     * @test
     */
    public function getSimpleObjectForKeyWithAccessorMethod()
    {
        $name = $this->fixture->getObjectForKey('name');
        $this->assertEquals('Daniel', $name);
    }

    /**
     * @test
     */
    public function setSimpleObjectForKeyWithAccessorMethod()
    {
        $this->fixture->setObjectForKey('name', 'Ingo');
        $name = $this->fixture->getObjectForKey('name');
        $this->assertEquals('Ingo', $name);
    }

    /**
     * @test
     */
    public function removeSimpleObjectForKeyWithAccessorMethod()
    {
        $this->fixture->removeObjectForKey('name');
        $name = $this->fixture->getObjectForKey('name');
        $this->assertNull($name);
    }

    /**
     * @test
     */
    public function getArrayObjectForKey()
    {
        $address = $this->fixture->getObjectForKey('address');
        $testAddress = [
            'street'  => 'Bingstreet 14',
            'city'    => 'NYC',
            'country' => 'USA',
        ];
        $this->assertEquals($testAddress, $address);
    }

    /**
     * @test
     */
    public function setArrayObjectForKey()
    {
        $newAddress = [
            'street'  => 'Kent St. 161',
            'city'    => 'Sidney',
            'country' => 'Australia',
        ];
        $this->fixture->setObjectForKey('address', $newAddress);
        $address = $this->fixture->getObjectForKey('address');

        $this->assertEquals($newAddress, $address);
    }

    /**
     * @test
     */
    public function getObjectForKeyPathWithArrayAsLastObject()
    {
        $city = $this->fixture->getObjectForKeyPath('address.city');
        $this->assertEquals('NYC', $city);
    }

    /**
     * @test
     */
    public function setObjectForKeyPathWithArrayAsLastObject()
    {
        $this->fixture->setObjectForKeyPath('address.city', 'Boston');
        $city = $this->fixture->getObjectForKeyPath('address.city');
        $this->assertEquals('Boston', $city);
    }

    /**
     * @test
     */
    public function transformGetterMethod()
    {
        $name = $this->fixture->getName();
        $this->assertEquals('Daniel', $name);
    }

    /**
     * @test
     */
    public function transformSetterMethod()
    {
        $this->fixture->setName('Ingo');
        $name = $this->fixture->getName();
        $this->assertEquals('Ingo', $name);
    }

}
