<?php
namespace Iresults\Core\Tests\Core;

/*
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *             Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\Flow\Annotations as Flow;

class Person extends \Iresults\Core\Model {
	/**
	 * The name
	 *
	 * @var string
	 */
	protected $name = 'Daniel';

	/**
	 * The age
	 *
	 * @var int
	 */
	protected $age = 26;

	/**
	 * The address
	 *
	 * @var array<string>
	 */
	protected $address = array(
		'street' 	=> 'Bingstreet 14',
		'city'		=> 'NYC',
		'country'	=> 'USA'
	);

	/**
	 * Returns the name.
	 * @return	string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Sets the name.
	 *
	 * @param	string	$newValue The new value to set
	 * @return	void
	 */
	public function setName($newValue){
		$this->name = $newValue;
	}

	public function gar() {
		return $this->address;
	}
}

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
class ModelTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	/**
	 * @var Iresults_FakeObject
	 */
	protected $fixture;

	public function setUp() {
		Ir::forceDebug();
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
?>