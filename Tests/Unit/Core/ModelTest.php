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

use Iresults\Core\Tests\Fixture\Person;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for Iresults_Model
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class ModelTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Person
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Person();
	}

	public function tearDown() {
	}


	/**
	 * @test
	 */
	public function getSimpleObjectForKeyWithoutAccessorMethod() {
		$age = $this->fixture->getObjectForKey('age');
		$this->assertEquals(26, $age);
	}

	/**
	 * @test
	 */
	public function setSimpleObjectForKeyWithoutAccessorMethod() {
		$this->fixture->setObjectForKey('age', 34);
		$age = $this->fixture->getObjectForKey('age');
		$this->assertEquals(34, $age);
	}

	/**
	 * @test
	 */
	public function removeSimpleObjectForKeyWithoutAccessorMethod() {
		$this->fixture->removeObjectForKey('age');
		$age = $this->fixture->getObjectForKey('age');
		$this->assertNull($age);
	}

	/**
	 * @test
	 */
	public function getSimpleObjectForKeyWithAccessorMethod() {
		$name = $this->fixture->getObjectForKey('name');
		$this->assertEquals('Daniel', $name);
	}

	/**
	 * @test
	 */
	public function setSimpleObjectForKeyWithAccessorMethod() {
		$this->fixture->setObjectForKey('name', 'Ingo');
		$name = $this->fixture->getObjectForKey('name');
		$this->assertEquals('Ingo', $name);
	}

	/**
	 * @test
	 */
	public function removeSimpleObjectForKeyWithAccessorMethod() {
		$this->fixture->removeObjectForKey('name');
		$name = $this->fixture->getObjectForKey('name');
		$this->assertNull($name);
	}

	/**
	 * @test
	 */
	public function getArrayObjectForKey() {
		$address = $this->fixture->getObjectForKey('address');
		$testAddress = array(
							 'street' 	=> 'Bingstreet 14',
							 'city' 	=> 'NYC',
							 'country' 	=> 'USA'
							 );
		$this->assertEquals($testAddress, $address);
	}

	/**
	 * @test
	 */
	public function setArrayObjectForKey() {
		$newAddress = array(
							 'street' 	=> 'Kent St. 161',
							 'city' 	=> 'Sidney',
							 'country' 	=> 'Australia'
							 );
		$this->fixture->setObjectForKey('address', $newAddress);
		$address = $this->fixture->getObjectForKey('address');

		$this->assertEquals($newAddress, $address);
	}

	/**
	 * @test
	 */
	public function getObjectForKeyPathWithArrayAsLastObject() {
		$city = $this->fixture->getObjectForKeyPath('address.city');
		$this->assertEquals('NYC', $city);
	}

	/**
	 * @test
	 */
	public function setObjectForKeyPathWithArrayAsLastObject() {
		$this->fixture->setObjectForKeyPath('address.city', 'Boston');
		$city = $this->fixture->getObjectForKeyPath('address.city');
		$this->assertEquals('Boston', $city);
	}

	/**
	 * @test
	 */
	public function transformGetterMethod() {
		$name = $this->fixture->getName();
		$this->assertEquals('Daniel', $name);
	}

	/**
	 * @test
	 */
	public function transformSetterMethod() {
		$this->fixture->setName('Ingo');
		$name = $this->fixture->getName();
		$this->assertEquals('Ingo', $name);
	}

}
