<?php
/*
 *  Copyright notice
 *
 *  (c) 2016 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
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
 * Created 05.10.16 14:50
 */


namespace Iresults\Core\Tests\Unit\DataObject;

use Iresults\Core\DataObject;
use Iresults\Core\Generator\CollectionGenerator;


/**
 * Test case for collection generator
 */
class CollectionGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function collectionFromCsvUrlTest()
    {
        /** @var DataObject[] $collection */
        $collection = CollectionGenerator::collectionFromCsvUrlWithCallback(
            __DIR__ . '/../SampleData/csv-example-7.csv',
            function ($data) {
                return new DataObject($data);
            }
        );
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
