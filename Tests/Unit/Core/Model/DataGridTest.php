<?php
/**
 * Created by JetBrains PhpStorm.
 * User: COD
 * Date: 18.09.13
 * Time: 13:47
 * To change this template use File | Settings | File Templates.
 */

namespace Iresults\Core\Tests\Core\Model;

require_once __DIR__ . '/../../Autoloader.php';

use Iresults\Core\Model\DataGrid;
use Iresults\Core\Mutable;

class DataGridTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var DataGrid
	 */
	protected $fixture = NULL;

	/**
	 * Number of data sets to test
	 * @var int
	 */
	protected $dataRowCount = 10000;

	public function setUp() {
		$testArray = array(
			array('name', 'address', 'weather') // The headers
		);
		$testArrayDummy = array(
			'name' => 'Daniel',
			'address' => array(
				'street' 	=> 'Bingstreet ',
				'city' 	=> 'NYC',
				'country' 	=> 'USA',
			),
			'weather'  => array(
				'temperature' 		=> '29°C',
				'relative humidity' => '89%'
			)
		);

		$i = 1;
		while ($i < $this->dataRowCount) {
			$currentTestArrayDummy = $testArrayDummy;
			$currentTestArrayDummy['name'] = $currentTestArrayDummy['name'] . $i;
			$currentTestArrayDummy['address']['street'] = $currentTestArrayDummy['address']['street'] . $i;
			$currentTestArrayDummy['weather']['temperature'] = $i . '°C';

			$testArray[] = array_values($currentTestArrayDummy);
			$i++;
		}

		$this->fixture = new DataGrid($testArray);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getRowCountTest() {
		$this->assertEquals($this->dataRowCount, $this->fixture->getRowCount());
	}

	/**
	 * @test
	 */
	public function getColumnCountTest() {
		$this->assertEquals(3, $this->fixture->getColumnCount());
	}

	/**
	 * @test
	 */
	public function getColumnAtIndexTest() {
		$addressColumn = $this->fixture->getColumnAtIndex(1);

		$columnCount = count($addressColumn);
		for ($i = 1; $i < $columnCount; $i = $i + 5) {
			$address = $addressColumn[$i];
			$this->assertTrue(isset($address['city']));
			$this->assertEquals('NYC', $address['city']);
		}
	}

	/**
	 * @test
	 */
	public function getRowAtIndexAsMutableTest() {
		$addressColumn = $this->fixture->getRowAtIndexAsMutable(2);
		$this->assertInstanceOf('Iresults\\Core\\Mutable', $addressColumn);

		/** @var array $address */
		$address = $addressColumn['address'];
		$this->assertEquals('NYC', $address['city']);
	}

	/**
	 * @test
	 */
	public function findValueTest() {
		$startRowIndex = 1000; // Set this to ($this->dataRowCount - 1) to see the real benefit of the index
		$scale = 15;
		for ($i = $startRowIndex; $i > 0; $i -= 3 * $scale) {
			$position = $this->fixture->findValue('Daniel' . $i);
			$this->assertEquals(array((object)array('row' => $i, 'column' => 0)), $position);
		}
	}

	/**
	 * @test
	 */
	public function findValueWithIndexTest() {
		$this->fixture->addIndexForColumn(0);

		$scale = 15;
		for ($i = ($this->dataRowCount - 1); $i > 0; $i -= 3 * $scale) {
			$position = $this->fixture->findValue('Daniel' . $i);
			$this->assertEquals(array((object)array('row' => $i, 'column' => 0)), $position);
		}
	}

	/**
	 * @test
	 */
	public function gridWithContentsOfCsv() {
		$filePath = __DIR__ . '/../../SampleData/csv-example-1.csv';
		$grid = DataGrid::gridWithContentsOfUrl($filePath);

	}
}
