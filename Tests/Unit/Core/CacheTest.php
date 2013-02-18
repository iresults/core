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
 * A subclass of Iresults_Core
 */
class CacheTestObject extends \Iresults\Core\Core {
	protected $name = 'mars';
}

/**
 * Test case for functionality of the iresults Cache.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults_Helpers
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class CacheTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	/**
	 * @var Iresults_Helpers_Fluid_Mail
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = \Iresults\Core\Cache\Factory::makeInstance();
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function isInstanceOfCacheAbstract() {
		$this->assertTrue(is_a(\Iresults\Core\Cache\Factory::makeInstance(), '\Iresults\Core\Cache\AbstractCache'));
	}

	/**
	 * @test
	 */
	public function setObjectForKey() {
		$this->fixture->setObjectForKey('age', 34);
		$age = $this->fixture->getObjectForKey('age');
		$this->assertEquals(34, $age);
	}

	/**
	 * @test
	 */
	public function getObjectForKeyOrPerformClosure() {
		$result = $this->fixture->getObjectForKeyOrPerformClosure('new_key_' . time(), function($key) {return 'new_value';});
		$this->assertEquals('new_value', $result);
	}

	/**
	 * @test
	 */
	public function getObjectForKeyOrPerformClosureAndSetValue() {
		$key = 'new_key_' . time();
		$this->fixture->getObjectForKeyOrPerformClosure($key, function($key) {return 'new_value';}, TRUE);

		$result = $this->fixture->getObjectForKey($key);
		$this->assertEquals('new_value', $result);
	}

	/**
	 * @test
	 */
	public function getObjectForKeyOrPerformClosureAndDontSetValue() {
		$key = 'new_key_not_to_save_' . time();
		$this->fixture->getObjectForKeyOrPerformClosure($key, function($key) {return 'new_value';}, FALSE);

		$result = $this->fixture->getObjectForKey($key);
		$this->assertNull($result);
	}
}

?>