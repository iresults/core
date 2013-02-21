<?php
namespace Iresults\Core\Tests\Core;

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
