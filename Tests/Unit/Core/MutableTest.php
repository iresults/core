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

/**
 * Test case for Iresults_Mutable
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @packname TYPO3
 * @subpackname Iresults
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class MutableTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	/**
	 * @var Iresults_FakeObject
	 */
	protected $fixture;

	public function setUp() {
		$testArray = array(
			'name' => 'Daniel',
			'address' => array(
							   'street' 	=> 'Bingstreet 14',
							   'city' 	=> 'NYC',
							   'country' 	=> 'USA',
							  ),
			'weather'  => array(
			   'temperature' 		=> '29°C',
			   'relative humidity' => '89%'
			)
		);
		$this->fixture = \Iresults\Core\Mutable::mutableWithArray($testArray);
	}

	public function tearDown() {
	}


	/**
	 * @test
	 */
	public function getSimpleObjectForKey() {
		$name = $this->fixture->getObjectForKey('name');
		$this->assertEquals('Daniel', $name);
	}

	/**
	 * @test
	 */
	public function setSimpleObjectForKey() {
		$this->fixture->setObjectForKey('name', 'Daniel');
		$name = $this->fixture->getObjectForKey('name');
		$this->assertEquals('Daniel', $name);
	}

	/**
	 * @test
	 */
	public function removeSimpleObjectForKey() {
		$this->fixture->setObjectForKey('name', 'Daniel');
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
	public function getObjectForKeyPath() {
		$city = $this->fixture->getObjectForKeyPath('address.city');
		$this->assertEquals('NYC', $city);
	}

	/**
	 * @test
	 */
	public function setObjectForKeyPath() {
		$this->fixture->setObjectForKeyPath('address.city', 'Boston');
		$city = $this->fixture->getObjectForKeyPath('address.city');
		$this->assertEquals('Boston', $city);
	}

	/**
	 * @test
	 */
	public function setSimpleObjectForZeroKey() {
		$this->fixture->setObjectForKey(0, 'Daniel');
		$name = $this->fixture->getObjectForKey(0);
		$this->assertEquals('Daniel', $name);
	}

	/**
	 * @test
	 */
	public function removeSimpleObjectForZeroKey() {
		$this->fixture->setObjectForKey(0, 'Daniel');
		$this->fixture->removeObjectForKey(0);
		$name = $this->fixture->getObjectForKey(0);
		$this->assertNull($name);
	}

	/**
	 * @test
	 */
	public function setSimpleObjectForZeroKeyPath() {
		$this->fixture->setObjectForKeyPath(0, 'Daniel');
		$name = $this->fixture->getObjectForKeyPath(0);
		$this->assertEquals('Daniel', $name);
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

	/**
	 * @test
	 */
	public function setObjectForKeyAutoExpandingMutable() {
		$testArray = array(
			'name' => 'Daniel',
			'address' => array(
							   'street' 	=> 'Bingstreet 14',
							   'city' 	=> 'NYC',
							   'country' 	=> 'USA',
							  )
		);
		$mutable = \Iresults\Core\Mutable\AutoExpanding::mutableWithArray($testArray);
		$mutable->setObjectForKeyPath('weather.temperature', '29°C');
		$this->assertInstanceOf('Iresults\Core\Mutable\AutoExpanding', $mutable->getObjectForKeyPath('weather'));
		$this->assertEquals('29°C', $mutable->getObjectForKeyPath('weather.temperature'));
	}
}
?>