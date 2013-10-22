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
 * Created 22.10.13 14:59
 */


namespace Iresults\Core\Tests\Parser;


require_once __DIR__ . '/../Autoloader.php';

use Iresults\Core\Parser\CsvParser;

class CsvParserTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var CsvParser
	 */
	protected $fixture;

	protected function setUp() {
		$this->fixture = new CsvParser();
	}

	protected function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function parseFile1Test() {
		$filePath = __DIR__ . '/../SampleData/csv-example-1.csv';
		$result = $this->fixture->parse($filePath);

		$this->assertEquals(array(
			array('Monday', 'Morning'),
			array('Monday', 'Afternoon'),
			array('Tuesday', 'Morning'),
			array('Tuesday', 'Afternoon'),
			array('Wednesday', 'Morning'),
			array('Wednesday', 'Afternoon'),
			array('Thursday', 'Morning'),
			array('Thursday', 'Afternoon'),
			array('Friday', 'Morning'),
			array('Friday', 'Afternoon'),
			array('Saturday', 'Morning'),
			array('Saturday', 'Afternoon'),
			array('Sunday', 'Morning'),
			array('Sunday', 'Afternoon'),
		), $result);
	}

	/**
	 * @test
	 */
	public function parseFile2Test() {
		$filePath = __DIR__ . '/../SampleData/csv-example-2.csv';
		$result = $this->fixture->parse($filePath);

		$this->assertEquals(array(
			array('Monday', 'Morning', '2:00'),
			array('Monday', 'Afternoon', '2:00'),
			array('Tuesday', 'Morning', '2:00'),
			array('Tuesday', 'Afternoon', '2:00'),
			array('Wednesday', 'Morning', '2:00'),
			array('Wednesday', 'Afternoon', '2:00'),
			array('Thursday', 'Morning', '2:00'),
			array('Thursday', 'Afternoon', '2:00'),
			array('Friday', 'Morning', '2:00'),
			array('Friday', 'Afternoon', '2:00'),
			array('Saturday', 'Morning', '2:00'),
			array('Saturday', 'Afternoon', '2:00'),
			array('Sunday', 'Morning', '2:00'),
			array('Sunday', 'Afternoon', '2:00'),
		), $result);
	}

	/**
	 * @test
	 */
	public function parseFile3Test() {
		$filePath = __DIR__ . '/../SampleData/csv-example-3.csv';
		$result = $this->fixture->parse($filePath);

		$this->assertEquals(array(
			array('Monday', 'Morning', '2:00'),
			array('Monday', 'Afternoon', '2:00'),
			array('Tuesday', 'Morning', '2:00'),
			array('Tuesday', 'Afternoon', '2:00'),
			array('Wednesday', 'Morning', '2:00'),
			array('Wednesday', 'Afternoon', '2:00'),
			array('Thursday', 'Morning', '2:00'),
			array('Thursday', 'Afternoon', '2:00'),
			array('Friday', 'Morning', '2:00'),
			array('Friday', 'Afternoon', '2:00'),
			array('Saturday', 'Morning', '2:00'),
			array('Saturday', 'Afternoon', '2:00'),
			array('Sunday', 'Morning', '2:00'),
			array('Sunday', 'Afternoon', '2:00'),
		), $result);
	}

	/**
	 * @test
	 */
	public function parseFile4Test() {
		$filePath = __DIR__ . '/../SampleData/csv-example-4.csv';
		$this->fixture->setConfiguration(array('enclosure' => 'X'));
		$result = $this->fixture->parse($filePath);

		$this->assertEquals(array(
			array('Monday', 'Morning', '2:00'),
			array('Monday', 'Afternoon', '2:00'),
			array('Tuesday', 'Morning', '2:00'),
			array('Tuesday', 'Afternoon', '2:00'),
			array('Wednesday', 'Morning', '2:00'),
			array('Wednesday', 'Afternoon', '2:00'),
			array('Thursday', 'Morning', '2:00'),
			array('Thursday', 'Afternoon', '2:00'),
			array('Friday', 'Morning', '2:00'),
			array('Friday', 'Afternoon', '2:00'),
			array('Saturday', 'Morning', '2:00'),
			array('Saturday', 'Afternoon', '2:00'),
			array('Sunday', 'Morning', '2:00'),
			array('Sunday', 'Afternoon', '2:00'),
		), $result);
	}

	/**
	 * @test
	 */
	public function parseFile5Test() {
		$filePath = __DIR__ . '/../SampleData/csv-example-5.csv';
		$result = $this->fixture->parse($filePath);

		$this->assertEquals(array(
			array('01',	'Monday',		'Morning',		'2:00'),
			array('02',	'Monday',		'Afternoon',	'2:00'),
			array('03',	'Tuesday',		'Morning',		'2:00'),
			array('04',	'Tuesday',		'Afternoon',	'2:00'),
			array('05',	'Wednesday',	'Morning',		'2:00'),
			array('06',	'Wednesday',	'Afternoon',	'2:00'),
			array('07',	'Thursday',		'Morning',		'2:00'),
			array('08',	'Thursday',		'Afternoon',	'2:00'),
			array('09',	'Friday',		'Morning',		'2:00'),
			array('10',	'Friday',		'Afternoon',	'2:00'),
			array('11',	'Saturday',		'Morning',		'2:00'),
			array('12',	'Saturday',		'Afternoon',	'2:00'),
			array('13',	'Sunday',		'Morning',		'2:00'),
			array('14',	'Sunday',		'Afternoon',	'2:00'),
		), $result);
	}
}