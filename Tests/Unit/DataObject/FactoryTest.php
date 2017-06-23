<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 05.10.16 14:50
 */


namespace Iresults\Core\Tests\Unit\DataObject;

use Iresults\Core\DataObject;


/**
 * Test case for the Data Object factory
 *
 * @author      Daniel Corn <cod@iresults.li>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function collectionFromCsvUrlTest()
    {
        $collection = DataObject\Factory::collectionFromCsvUrl(__DIR__ . '/../SampleData/csv-example-7.csv');
        $this->assertCount(3, $collection);
        $this->assertInstanceOf(DataObject::class, $collection[0]);
        $this->assertInstanceOf(DataObject::class, $collection[1]);
        $this->assertInstanceOf(DataObject::class, $collection[2]);

        $this->assertSame(
            [
                'firstName' => 'Peter',
                'lastName'  => 'Dingbert',
                'age'       => '29',
            ],
            $collection[0]->toArray()
        );
        $this->assertSame(
            [
                'firstName' => 'Philip',
                'lastName'  => 'Captain',
                'age'       => '53',
            ],
            $collection[1]->toArray()
        );
        $this->assertSame(
            [
                'firstName' => 'Susan',
                'lastName'  => 'Reader',
                'age'       => '101',
            ],
            $collection[2]->toArray()
        );
    }

    /**
     * @test
     */
    public function collectionFromCsvUrlEmptyTest()
    {
        $collection = DataObject\Factory::collectionFromCsvUrl(__DIR__ . '/../SampleData/csv-example-9.csv');
        $this->assertCount(0, $collection);
    }

    /**
     * @test
     */
    public function collectionFromCsvUrlHeaderLineOnlyTest()
    {
        $collection = DataObject\Factory::collectionFromCsvUrl(__DIR__ . '/../SampleData/csv-example-8.csv');
        $this->assertCount(0, $collection);
    }
}
