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
class ObjectTestObject extends \Iresults\Core\Core {
	protected $name = 'mars';
}

/**
 * Test case for functionality of the Core object.
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
class ObjectTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	/**
	 * @var Iresults_Helpers_Fluid_Mail
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new ObjectTestObject();
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function isInstanceOfCoreObject() {
		$this->assertEquals(get_class($this->fixture), 'b');
		$this->assertTrue(is_a($this->fixture, '\Iresults\Core\Core'));
	}

	/**
	 * @test
	 */
	public function canAddDynamicMethod() {
		$this->fixture->newMethod = function() {
			return 'hello';
		};
		$result = $this->fixture->newMethod();
		$this->assertEquals('hello', $result);
	}

	/**
	 * @test
	 */
	public function canAddDynamicMethodWithArguments() {
		$this->fixture->newMethod = function($name) {
			return 'hello ' . $name;
		};
		$result = $this->fixture->newMethod('world');
		$this->assertEquals('hello world', $result);
	}

	// Setting the context isn't possible at the moment
	///**
	// * @test
	// */
	//public function canAddDynamicMethodWithArgumentsAndThis() {
	//	$er = error_reporting(E_ALL);
	//
	//	$this->fixture->newMethod = function($name) {
	//
	//		$this->name = $name;
	//		Ir::forceDebug();
	//		Ir::pd($this);
	//		die;
	//		return 'hello ' . $this->name;
	//	};
	//	$result = $this->fixture->newMethod('world');
	//
	//	error_reporting($er);
	//	$this->assertEquals('hello world', $result);
	//}
}
?>