<?php
namespace Iresults\Core\Tests\Unit\DataObject;

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

use Iresults\Core\DataObject;

/**
 * Test case for the mutable Data Object
 *
 * @version     $Id$
 * @copyright   Copyright belongs to the respective authors
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
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
        $testArray = array(
            'name'    => 'Daniel',
            'address' => array(
                'street'  => 'Bingstreet 14',
                'city'    => 'NYC',
                'country' => 'USA',
            ),
            'weather' => array(
                'temperature'       => '29°C',
                'relative humidity' => '89%',
            ),
        );
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
        $testAddress = array(
            'street'  => 'Bingstreet 14',
            'city'    => 'NYC',
            'country' => 'USA',
        );
        $this->assertEquals($testAddress, $address);
    }

    /**
     * @test
     */
    public function setArrayObjectForKey()
    {
        $newAddress = array(
            'street'  => 'Kent St. 161',
            'city'    => 'Sidney',
            'country' => 'Australia',
        );
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