<?php
/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author COD
 * Created 02.10.13 16:07
 */


namespace Iresults\Core\Tests\Core;

require_once __DIR__ . '/../Autoloader.php';

use Iresults\Core\Base;
use Iresults\Core\Exception\UndefinedMethod;
use Iresults\Core\Iresults;
use Iresults\Core\IresultsBaseInterface;

class IresultsTestImplementation extends Base implements IresultsBaseInterface{
	public function isFullRequest() {
		return FALSE;
	}

	public function dynamicFunction() {
		return TRUE;
	}

}

class IresultsTest extends \PHPUnit_Framework_TestCase {
	protected function setUp() {
	}

	protected function tearDown() {
		Iresults::_destroySharedInstance();
		Iresults::_registerImplementationClassName('\\Iresults\\Core\\Base');
	}

	/**
	 * @test
	 */
	public function getSharedInstanceTest() {
		$this->assertInstanceOf('\\Iresults\\Core\\IresultsBaseInterface', 	Iresults::getSharedInstance());
		$this->assertInstanceOf('\\Iresults\\Core\\Base', 					Iresults::getSharedInstance());
	}

	/**
	 * @test
	 */
	public function overrideImplementationTest() {
		Iresults::_registerImplementationClassName('\\Iresults\\Core\\Tests\\Core\\IresultsTestImplementation');

		$this->assertInstanceOf('\\Iresults\\Core\\IresultsBaseInterface', 						Iresults::getSharedInstance());
		$this->assertInstanceOf('\\Iresults\\Core\\Tests\\Core\\IresultsTestImplementation', 	Iresults::getSharedInstance());

		$this->assertFalse(Iresults::isFullRequest());
	}

	/**
	 * @test
	 */
	public function overrideImplementationAndCallMagicMethodTest() {
		Iresults::_registerImplementationClassName('\\Iresults\\Core\\Tests\\Core\\IresultsTestImplementation');

		$this->assertTrue(Iresults::dynamicFunction());
	}

	/**
	 * @test
	 * @expectedException \Iresults\Core\Exception\UndefinedMethod
	 */
	public function callInvalidMagicMethodTest() {
		Iresults::dynamicFunction();
	}
}
