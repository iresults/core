<?php
namespace Iresults\Core\Tests\Helpers;

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

use Iresults\Core\Tools\Math;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for the String Tool class.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults_Tools
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class MathTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		Math::setPrecision(9);
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function addTest () {
		$augend = 2900.099;
		$addend = 1.09001;
		$result = Math::add($augend, $addend, TRUE);
		$this->assertSame('2901.189010000', $result);

		$result = Math::add($augend, $addend, FALSE);
		$this->assertEquals('2901.189010000', $result);
		$this->assertNotSame('2901.189010000', $result);
		$this->assertSame('2901.18901', '' . $result);
	}

	/**
	 * @test
	 */
	public function subtractTest() {
		$minuend = 2900.099;
		$subtrahend = 1.09001;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('2899.008990000', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('2899.008990000', $result);
		$this->assertNotSame('2899.008990000', $result);
		$this->assertSame('2899.00899', '' . $result);


		$minuend = 2900.099;
		$subtrahend = 2900.099;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('0.000000000', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('0.000000000', $result);
		$this->assertNotSame('0.000000000', $result);
		$this->assertSame('0', '' . $result);


		$minuend = 2900.099;
		$subtrahend = 2900.0990001;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('-0.000000100', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('-0.000000100', $result);
		$this->assertNotSame('-0.000000100', $result);
		$this->assertSame('-1.0E-7', '' . $result);


		$minuend = 2900.0990001;
		$subtrahend = 2900.099000;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('0.000000100', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('0.000000100', $result);
		$this->assertNotSame('0.000000100', $result);
		$this->assertSame('1.0E-7', '' . $result);


		$minuend = 2900.0990001;
		$subtrahend = 2900.099000;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('0.000000100', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('0.000000100', $result);
		$this->assertNotSame('0.000000100', $result);
		$this->assertSame('1.0E-7', '' . $result);


		Math::setPrecision(9);
		$minuend = 2900.0990000001;
		$subtrahend = 2900.099000000;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('0.000000000', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('0.000000000', $result);
		$this->assertNotSame('0.000000000', $result);
		$this->assertSame('0', '' . $result);


		Math::setPrecision(9);
		$minuend = 2900.099000001;
		$subtrahend = 2900.09900000;
		$result = Math::subtract($minuend, $subtrahend, TRUE);
		$this->assertSame('0.000000001', $result);

		$result = Math::subtract($minuend, $subtrahend, FALSE);
		$this->assertEquals('0.000000001', $result);
		$this->assertNotSame('0.000000001', $result);
		$this->assertSame('1.0E-9', '' . $result);
	}

	/**
	 * @test
	 */
	public function multiplyTest() {
		$multiplicand = 2900.099;
		$multiplier = 1.09001;
		$result = Math::multiply($multiplicand, $multiplier, TRUE);
		$this->assertSame('3161.136910990', $result);

		$result = Math::multiply($multiplicand, $multiplier, FALSE);
		$this->assertEquals('3161.136910990', $result);
		$this->assertNotSame('3161.136910990', $result);
		$this->assertSame('3161.13691099', '' . $result);


		$multiplicand = 2900.099;
		$multiplier = 1.000;
		$result = Math::multiply($multiplicand, $multiplier, TRUE);
		$this->assertSame('2900.099000000', $result);

		$result = Math::multiply($multiplicand, $multiplier, FALSE);
		$this->assertEquals('2900.099000000', $result);
		$this->assertNotSame('2900.099000000', $result);
		$this->assertSame('2900.099', '' . $result);


		$multiplicand = 2900.099;
		$multiplier = 1;
		$result = Math::multiply($multiplicand, $multiplier, TRUE);
		$this->assertSame('2900.099000000', $result);

		$result = Math::multiply($multiplicand, $multiplier, FALSE);
		$this->assertEquals('2900.099000000', $result);
		$this->assertNotSame('2900.099000000', $result);
		$this->assertSame('2900.099', '' . $result);
	}

	/**
	 * @test
	 */
	public function divideTest() {
		$dividend = 2900.099;
		$divisor = 1.09001;
		$result = Math::divide($dividend, $divisor, TRUE);
		$this->assertSame('2660.616875074', $result);

		$result = Math::divide($dividend, $divisor, FALSE);
		$this->assertEquals('2660.616875074', $result);
		$this->assertNotSame('2660.616875074', $result);
		$this->assertSame('2660.616875074', '' . $result);


		$dividend = 2900.099;
		$divisor = 2900.099;
		$result = Math::divide($dividend, $divisor, TRUE);
		$this->assertSame('1.000000000', $result);

		$result = Math::divide($dividend, $divisor, FALSE);
		$this->assertEquals('1.000000000', $result);
		$this->assertNotSame('1.000000000', $result);
		$this->assertSame('1', '' . $result);


		$dividend = 2900.00;
		$divisor = 2900;
		$result = Math::divide($dividend, $divisor, TRUE);
		$this->assertSame('1.000000000', $result);

		$result = Math::divide($dividend, $divisor, FALSE);
		$this->assertEquals('1.000000000', $result);
		$this->assertNotSame('1.000000000', $result);
		$this->assertSame('1', '' . $result);
	}

	/**
	 * @test
	 */
	public function almostEqualsTest() {
		$a = 2900.099;
		$b = 1.09001;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		$a = 1.0000001;
		$b = 1.0000000;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		$a = 1.0000000001;
		$b = 1.0000000000;
		$result = Math::almostEquals($a, $b);
		$this->assertTrue($result);

		Math::setPrecision(12);
		$a = 1.0000000001;
		$b = 1.0000000000;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(3);
		$a = 1.0000000001;
		$b = 1.0000000000;
		$result = Math::almostEquals($a, $b);
		$this->assertTrue($result);

		Math::setPrecision(12);
		$a = 9000000 + 0.1;
		$b = 9000000 - 0.1;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(9);
		$a = 1234567890123 + 0.1;
		$b = 1234567890123 - 0.1;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(9);
		$a = 12345678901234 + 0.1;
		$b = 12345678901234 - 0.1;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(9);
		$a = 123456789012340 + 0.1;
		$b = 123456789012340 - 0.1;
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(9);
		$a = floatval(1);
		$b = 1.0000000001; // 9 times 0 and precision is 9
		$result = Math::almostEquals($a, $b);
		$this->assertTrue($result);

		Math::setPrecision(10);
		$a = floatval(1);
		$b = 1.0000000001; // 9 times 0 and precision is 10
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(9);
		$a = 1;
		$b = 1.0000000001; // 9 times 0 and precision is 9
		$result = Math::almostEquals($a, $b);
		$this->assertTrue($result);

		Math::setPrecision(10);
		$a = 1;
		$b = 1.0000000001; // 9 times 0 and precision is 10
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

		Math::setPrecision(1);
		$a = 12345678901234;
		$b = floatval(12345678901234.01);
		$result = Math::almostEquals($a, $b);
		$this->assertTrue($result);

		Math::setPrecision(2);
		$a = 12345678901234;
		$b = floatval(12345678901234.01);
		$result = Math::almostEquals($a, $b);
		$this->assertFalse($result);

//		Math::setPrecision(400);
//		$a = PHP_INT_MAX + 0.1;
//		$b = PHP_INT_MAX - 0.1;
//		var_dump($a, $b);
//		$result = Math::almostEquals($a, $b);
//		$this->assertFalse($result);



	}
}
?>
