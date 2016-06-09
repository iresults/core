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

require_once __DIR__ . '/../Autoloader.php';

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
class CacheTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Iresults_Helpers_Fluid_Mail
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = \Iresults\Core\Cache\Factory::getSharedInstance();
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function isInstanceOfCacheAbstract() {
		$this->assertTrue(is_a(\Iresults\Core\Cache\Factory::getSharedInstance(), 	'\Iresults\Core\Cache\AbstractCache'));
		$this->assertTrue(is_a(\Iresults\Core\Cache\Factory::makeInstance(), 		'\Iresults\Core\Cache\AbstractCache'));
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

