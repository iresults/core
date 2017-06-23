<?php

namespace Iresults\Core\Tests\Unit\DataObject;
use Iresults\Core\DataObject;

/**
 * Test case for the mutable Data Object
 *
 * @author      Daniel Corn <cod@iresults.li>
 */
class DataObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataObject
     */
    protected $fixture;

    public function setUp()
    {
        $testArray = [
            'name'    => 'Daniel',
            'address' => [
                'street'  => 'Bingstreet 14',
                'city'    => 'NYC',
                'country' => 'USA',
            ],
            'weather' => [
                'temperature'       => '29°C',
                'relative humidity' => '89%',
            ],
        ];
        $this->fixture = new DataObject($testArray);
    }

    public function tearDown()
    {
    }


    /**
     * @test
     */
    public function getSimpleObjectForKey()
    {
        $name = $this->fixture->getObjectForKey('name');
        $this->assertEquals('Daniel', $name);
    }

    /**
     * @test
     */
    public function setSimpleObjectForKey()
    {
        $this->fixture->setObjectForKey('name', 'Peter');
        $name = $this->fixture->getObjectForKey('name');
        $this->assertEquals('Peter', $name);
    }

    /**
     * @test
     */
    public function removeSimpleObjectForKey()
    {
        $this->fixture->setObjectForKey('name', 'Daniel');
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
    public function getObjectForKeyPath()
    {
        $city = $this->fixture->getObjectForKeyPath('address.city');
        $this->assertEquals('NYC', $city);
    }

    /**
     * @test
     */
    public function setObjectForKeyPath()
    {
        $this->fixture->setObjectForKeyPath('address.city', 'Boston');
        $city = $this->fixture->getObjectForKeyPath('address.city');
        $this->assertEquals('Boston', $city);
    }

    /**
     * @test
     */
    public function setSimpleObjectForZeroKey()
    {
        $this->fixture->setObjectForKey(0, 'Peter');
        $name = $this->fixture->getObjectForKey(0);
        $this->assertEquals('Peter', $name);
    }

    /**
     * @test
     */
    public function removeSimpleObjectForZeroKey()
    {
        $this->fixture->setObjectForKey(0, 'Peter');
        $this->fixture->removeObjectForKey(0);
        $name = $this->fixture->getObjectForKey(0);
        $this->assertNull($name);
    }

    /**
     * @test
     */
    public function setSimpleObjectForZeroKeyPath()
    {
        $this->fixture->setObjectForKeyPath(0, 'Peter');
        $name = $this->fixture->getObjectForKeyPath(0);
        $this->assertEquals('Peter', $name);
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
        $this->fixture->setName('Peter');
        $name = $this->fixture->getObjectForKey('name');
        $this->assertEquals('Peter', $name);
    }

    /**
     * @test
     */
    public function magicGetterMethod()
    {
        $name = $this->fixture->name;
        $this->assertEquals('Daniel', $name);
    }

    /**
     * @test
     */
    public function magicSetterMethod()
    {
        $this->fixture->name = 'Peter';
        $name = $this->fixture->getObjectForKey('name');
        $this->assertEquals('Peter', $name);
    }
}
