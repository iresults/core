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

use Iresults\Core\DateTime;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for functionality of the DateTime object.
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
class DateTimeTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Iresults\Core\DateTime
	 */
	protected $fixture;

	public function setUp() {
		error_reporting(E_ALL);
		$this->fixture = new DateTime();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function isInstanceOfDateTime() {
		$this->assertInstanceOf('\\DateTime', $this->fixture);
		$this->assertInstanceOf('\\Iresults\\Core\\DateTime', $this->fixture);
	}

	/**
	 * @test
	 */
	public function isEqualToDateTime() {
		$iresultsTestObject = new DateTime();
		$phpTestObject = new \DateTime();

		$this->assertEquals($phpTestObject->getTimestamp(), 	$iresultsTestObject->getTimestamp());
		$this->assertEquals($phpTestObject->getTimezone(), 		$iresultsTestObject->getTimezone());
		$this->assertEquals($phpTestObject->format('r'), 		$iresultsTestObject->format('r'));
	}

	/**
	 * @test
	 */
	public function canCreateFromDifferentFormats() {
		$phpTestObject = new \DateTime('Wed, 20 Nov 2013 11:21:36 +0000');

		$iresultsTestObject = new DateTime(1384946496);
		$this->assertEquals($phpTestObject->getTimestamp(), 	$iresultsTestObject->getTimestamp());
		$this->assertEquals($phpTestObject->format('r'), 		$iresultsTestObject->format('r'));

		$iresultsTestObject = new DateTime('Wed, 20 Nov 2013 11:21:36 +0000');
		$this->assertEquals($phpTestObject->getTimestamp(), 	$iresultsTestObject->getTimestamp());
		$this->assertEquals($phpTestObject->format('r'), 		$iresultsTestObject->format('r'));


	}

	/**
	 * @test
	 */
	public function canCreateWithTimeZone() {
		$testTimeZone = 'Australia/Victoria';

		$iresultsTestObject = new DateTime('Wed, 20 Nov 2013 11:21:36', new \DateTimeZone($testTimeZone));
		$this->assertEquals($testTimeZone, $iresultsTestObject->getTimezone()->getName());

		$iresultsTestObject = new DateTime('now', new \DateTimeZone($testTimeZone));
		$this->assertEquals($testTimeZone, $iresultsTestObject->getTimezone()->getName());

	}
}
?>
