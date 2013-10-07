<?php
namespace Iresults\Core\Tests\Locale;

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

use Iresults\Core\Iresults;
use Iresults\Core\Locale\Environment;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for the locale translator
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults_Locale
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase {
	static protected $systemLocale;

	public function setUp() {
		if (!self::$systemLocale) {
			self::$systemLocale = Environment::getSharedInstance()->getLocale();
		}
	}

	public function tearDown() {
		Environment::getSharedInstance()->setLocale(self::$systemLocale);
	}

	/**
	 * @test
	 */
	public function getInitialLocaleTest(){
		$locale = setlocale(LC_CTYPE, '0');
		if ($locale === 'C') {
			$locale = Iresults::getLocale();
		}
		$this->assertEquals($locale, Environment::getSharedInstance()->getLocale());
	}

	/**
	 * @test
	 */
	public function setLocaleTest(){
//		$newLocale = 'ne_NP.UTF-8';
		$newLocale = 'de_DE.UTF-8';

		Environment::getSharedInstance()->setLocale($newLocale);
		$this->assertEquals($newLocale, Environment::getSharedInstance()->getLocale());
		$this->assertEquals($newLocale, setlocale(LC_CTYPE, '0'));
	}

	/**
	 * @test
	 */
	public function executeWithLocaleAndCallableTest(){
//		$newLocale = 'ne_NP.UTF-8';
		$newLocale = 'de_DE.UTF-8';

		$result = Environment::getSharedInstance()->executeWithLocale($newLocale,
			function () {
				return setlocale(LC_CTYPE, '0');
			}
		);
		$this->assertEquals($newLocale, $result);
		$this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
		$this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
	}

	/**
	 * @test
	 */
	public function executeWithLocaleAndArrayWithoutArgumentsTest(){
//		$newLocale = 'ne_NP.UTF-8';
		$newLocale = 'de_DE.UTF-8';

		$result = Environment::getSharedInstance()->executeWithLocale($newLocale,
			array(
				array($this, 'functionWithoutArguments')
			)
		);
		$this->assertEquals($newLocale, $result);
		$this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
		$this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
	}

	/**
	 * @test
	 */
	public function executeWithLocaleAndArrayWithArgumentsTest(){
//		$newLocale = 'ne_NP.UTF-8';
		$newLocale = 'de_DE.UTF-8';

		$result = Environment::getSharedInstance()->executeWithLocale($newLocale,
			array(
				array($this, 'functionWithArguments'),
				array('My current locale: ')
			)
		);
		$this->assertEquals('My current locale: ' . $newLocale, $result);
		$this->assertEquals(self::$systemLocale, Environment::getSharedInstance()->getLocale());
		$this->assertEquals(self::$systemLocale, setlocale(LC_CTYPE, '0'));
	}

	public function functionWithoutArguments() {
		return setlocale(LC_CTYPE, '0');
	}
	public function functionWithArguments($arg0) {
		return $arg0 . setlocale(LC_CTYPE, '0');

	}
}
?>
