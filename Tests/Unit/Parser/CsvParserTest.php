<?php

namespace Iresults\Core\Tests\Unit\Parser;

use Iresults\Core\Parser\CsvFileParser;

class CsvParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CsvFileParser
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new CsvFileParser();
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function parseFile1Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-1.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['Monday', 'Morning'],
                ['Monday', 'Afternoon'],
                ['Tuesday', 'Morning'],
                ['Tuesday', 'Afternoon'],
                ['Wednesday', 'Morning'],
                ['Wednesday', 'Afternoon'],
                ['Thursday', 'Morning'],
                ['Thursday', 'Afternoon'],
                ['Friday', 'Morning'],
                ['Friday', 'Afternoon'],
                ['Saturday', 'Morning'],
                ['Saturday', 'Afternoon'],
                ['Sunday', 'Morning'],
                ['Sunday', 'Afternoon'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile2Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-2.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['Monday', 'Morning', '2:00'],
                ['Monday', 'Afternoon', '2:00'],
                ['Tuesday', 'Morning', '2:00'],
                ['Tuesday', 'Afternoon', '2:00'],
                ['Wednesday', 'Morning', '2:00'],
                ['Wednesday', 'Afternoon', '2:00'],
                ['Thursday', 'Morning', '2:00'],
                ['Thursday', 'Afternoon', '2:00'],
                ['Friday', 'Morning', '2:00'],
                ['Friday', 'Afternoon', '2:00'],
                ['Saturday', 'Morning', '2:00'],
                ['Saturday', 'Afternoon', '2:00'],
                ['Sunday', 'Morning', '2:00'],
                ['Sunday', 'Afternoon', '2:00'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile3Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-3.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['Monday', 'Morning', '2:00'],
                ['Monday', 'Afternoon', '2:00'],
                ['Tuesday', 'Morning', '2:00'],
                ['Tuesday', 'Afternoon', '2:00'],
                ['Wednesday', 'Morning', '2:00'],
                ['Wednesday', 'Afternoon', '2:00'],
                ['Thursday', 'Morning', '2:00'],
                ['Thursday', 'Afternoon', '2:00'],
                ['Friday', 'Morning', '2:00'],
                ['Friday', 'Afternoon', '2:00'],
                ['Saturday', 'Morning', '2:00'],
                ['Saturday', 'Afternoon', '2:00'],
                ['Sunday', 'Morning', '2:00'],
                ['Sunday', 'Afternoon', '2:00'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile4Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-4.csv';
        $this->fixture->setConfiguration(['enclosure' => 'X']);
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['Monday', 'Morning', '2:00'],
                ['Monday', 'Afternoon', '2:00'],
                ['Tuesday', 'Morning', '2:00'],
                ['Tuesday', 'Afternoon', '2:00'],
                ['Wednesday', 'Morning', '2:00'],
                ['Wednesday', 'Afternoon', '2:00'],
                ['Thursday', 'Morning', '2:00'],
                ['Thursday', 'Afternoon', '2:00'],
                ['Friday', 'Morning', '2:00'],
                ['Friday', 'Afternoon', '2:00'],
                ['Saturday', 'Morning', '2:00'],
                ['Saturday', 'Afternoon', '2:00'],
                ['Sunday', 'Morning', '2:00'],
                ['Sunday', 'Afternoon', '2:00'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile5Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-5.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['01', 'Monday', 'Morning', '2:00'],
                ['02', 'Monday', 'Afternoon', '2:00'],
                ['03', 'Tuesday', 'Morning', '2:00'],
                ['04', 'Tuesday', 'Afternoon', '2:00'],
                ['05', 'Wednesday', 'Morning', '2:00'],
                ['06', 'Wednesday', 'Afternoon', '2:00'],
                ['07', 'Thursday', 'Morning', '2:00'],
                ['08', 'Thursday', 'Afternoon', '2:00'],
                ['09', 'Friday', 'Morning', '2:00'],
                ['10', 'Friday', 'Afternoon', '2:00'],
                ['11', 'Saturday', 'Morning', '2:00'],
                ['12', 'Saturday', 'Afternoon', '2:00'],
                ['13', 'Sunday', 'Morning', '2:00'],
                ['14', 'Sunday', 'Afternoon', '2:00'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile6Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-6.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['01', "Monday\nMorning", '2:00'],
                ['02', "Monday\nAfternoon", '2:00'],
                ['03', "Tuesday\nMorning", '2:00'],
                ['04', "Tuesday\nAfternoon", '2:00'],
                ['05', "Wednesday\nMorning", '2:00'],
                ['06', "Wednesday\nAfternoon", '2:00'],
                ['07', "Thursday\nMorning", '2:00'],
                ['08', "Thursday\nAfternoon", '2:00'],
                ['09', "Friday\nMorning", '2:00'],
                ['10', "Friday\nAfternoon", '2:00'],
                ['11', "Saturday\nMorning", '2:00'],
                ['12', "Saturday\nAfternoon", '2:00'],
                ['13', "Sunday\nMorning", '2:00'],
                ['14', "Sunday\nAfternoon", '2:00'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile7Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-7.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['firstName', 'lastName', 'age'],
                ['Peter', 'Dingbert', '29'],
                ['Philip', 'Captain', '53'],
                ['Susan', 'Reader', '101'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile8Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-8.csv';
        $result = $this->fixture->parse($filePath);

        $this->assertEquals(
            [
                ['firstName', 'lastName', 'age'],
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function parseFile9Test()
    {
        $filePath = __DIR__ . '/../SampleData/csv-example-9.csv';
        $result = $this->fixture->parse($filePath);
        $this->assertCount(0, $result);
    }
}
